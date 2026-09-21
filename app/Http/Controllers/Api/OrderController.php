<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchMenuPrice;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.menuItem', 'branch']);

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('created_at')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        // Resolve branch ID gracefully (fallback to first active branch)
        $branchId = $request->input('branch_id');
        if (! $branchId || ! Branch::where('id', $branchId)->exists()) {
            $branchId = Branch::where('is_active', true)->value('id') ?? 1;
        }

        // Normalize order type ('dine_in', 'dine-in', 'takeaway', etc.)
        $rawType = $request->input('order_type') ?? $request->input('service_type') ?? 'dine_in';
        $orderType = in_array(str_replace('-', '_', strtolower((string) $rawType)), ['takeaway', 'take_away', 'bungkus']) ? 'takeaway' : 'dine_in';

        $customerName = strip_tags(trim((string) ($request->input('customer_name') ?? 'Pelanggan')));
        $customerPhone = $request->filled('customer_phone') ? strip_tags(trim((string) $request->input('customer_phone'))) : null;
        $tableNum = $request->filled('table_number') ? strip_tags(trim((string) $request->input('table_number'))) : null;
        $notes = $request->filled('notes') ? strip_tags(trim((string) $request->input('notes'))) : null;

        $rawItems = $request->input('items', []);
        if (! is_array($rawItems) || empty($rawItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan pilih minimal satu hidangan untuk dipesan.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $branchId, $orderType, $customerName, $customerPhone, $tableNum, $notes, $rawItems) {
            // Group duplicate items by menu_item_id
            $groupedItems = [];
            foreach ($rawItems as $it) {
                $menuId = $it['menu_item_id'] ?? $it['id'] ?? $it['menu_id'] ?? null;
                if (! $menuId) {
                    continue;
                }
                $qty = max(1, (int) ($it['quantity'] ?? 1));
                $note = ! empty($it['notes']) ? strip_tags(trim((string) $it['notes'])) : '';
                if (isset($groupedItems[$menuId])) {
                    $groupedItems[$menuId]['quantity'] += $qty;
                    if ($note) {
                        $groupedItems[$menuId]['notes'] = $groupedItems[$menuId]['notes'] ? $groupedItems[$menuId]['notes'].' | '.$note : $note;
                    }
                } else {
                    $groupedItems[$menuId] = [
                        'menu_item_id' => (int) $menuId,
                        'quantity' => $qty,
                        'notes' => $note,
                    ];
                }
            }

            if (empty($groupedItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Daftar hidangan pesanan tidak valid.',
                ], 422);
            }

            $isCashierPos = in_array($request->input('source'), ['kasir_pos', 'pos'])
                || (auth()->check() && (auth()->user()->isCashier() || auth()->user()->isAdmin()));

            // Check table availability for customer dine-in (allow cashier pos to seat freely)
            if (! $isCashierPos && $orderType === 'dine_in' && $tableNum) {
                $table = Table::where('branch_id', $branchId)
                    ->where(function ($q) use ($tableNum) {
                        $q->where('table_number', $tableNum)
                            ->orWhere('table_number', 'Meja '.ltrim(preg_replace('/[^0-9]/', '', (string) $tableNum), '0'))
                            ->orWhere('table_number', 'Meja '.str_pad(preg_replace('/[^0-9]/', '', (string) $tableNum), 2, '0', STR_PAD_LEFT));
                    })
                    ->first();

                if ($table && $table->status === 'occupied') {
                    return response()->json([
                        'success' => false,
                        'message' => "Maaf, Meja {$tableNum} saat ini sedang terisi. Silakan pilih nomor meja lain.",
                    ], 422);
                }
            }

            $items = [];
            foreach ($groupedItems as $item) {
                $menuModel = MenuItem::lockForUpdate()->find($item['menu_item_id']);

                if (! $menuModel || ! $menuModel->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => "Menu pilihan Anda (ID {$item['menu_item_id']}) sedang tidak tersedia.",
                    ], 422);
                }

                $price = BranchMenuPrice::where('branch_id', $branchId)
                    ->where('menu_item_id', $item['menu_item_id'])
                    ->first();

                // If price record doesn't exist for this branch, auto fallback or create
                if (! $price) {
                    $fallbackPrice = BranchMenuPrice::where('menu_item_id', $item['menu_item_id'])->first();
                    $harga = $fallbackPrice ? (float) $fallbackPrice->harga : 25000;
                    $price = BranchMenuPrice::firstOrCreate(
                        ['branch_id' => $branchId, 'menu_item_id' => $item['menu_item_id']],
                        ['harga' => $harga, 'is_available' => true]
                    );
                }

                // If stock is limited (stock_quantity is not null), validate stock
                if ($menuModel->stock_quantity !== null) {
                    if ($menuModel->stock_quantity < $item['quantity'] || ($menuModel->availability_status === 'habis' && $menuModel->stock_quantity <= 0)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Stok untuk menu {$menuModel->nama} tidak mencukupi (sisa {$menuModel->stock_quantity} porsi).",
                        ], 422);
                    }
                }

                $items[] = array_merge($item, ['price' => (float) $price->harga]);
            }

            $total = collect($items)->sum(fn ($item) => $item['quantity'] * $item['price']);

            $paymentStatus = ($isCashierPos && $request->input('payment_status') === 'paid') ? 'paid' : 'unpaid';
            $orderStatus = 'pending';
            $cashierId = $isCashierPos ? (auth()->id() ?? 1) : null;
            $source = $isCashierPos ? 'pos' : ($request->input('source') === 'mobile_app' ? 'pos' : 'customer_web');

            // Create Order
            $order = Order::create([
                'branch_id' => $branchId,
                'order_type' => $orderType,
                'table_number' => $tableNum,
                'source' => $source,
                'method' => 'dine-in',
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'notes' => $notes,
                'total' => $total,
                'status' => $orderStatus,
                'payment_status' => $paymentStatus,
                'cashier_id' => $cashierId,
            ]);

            if ($order->payment_status === 'paid' && $isCashierPos) {
                Payment::create([
                    'order_id' => $order->id,
                    'cashier_id' => $order->cashier_id,
                    'method' => 'cash',
                    'amount' => $order->total,
                    'cash_given' => $order->total,
                    'change_amount' => 0,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
            }

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'notes' => isset($item['notes']) ? strip_tags($item['notes']) : null,
                ]);

                // Decrement Stock if stock is limited
                $menuModel = MenuItem::lockForUpdate()->find($item['menu_item_id']);
                if ($menuModel && $menuModel->stock_quantity !== null) {
                    $newStock = max(0, $menuModel->stock_quantity - $item['quantity']);
                    $status = 'tersedia';
                    if ($newStock === 0) {
                        $status = 'habis';
                    } elseif ($newStock <= 5) {
                        $status = 'hampir_habis';
                    }

                    $menuModel->update([
                        'stock_quantity' => $newStock,
                        'availability_status' => $status,
                    ]);
                }
            }

            if ($order->order_type === 'dine_in' && $tableNum) {
                Table::where('branch_id', $order->branch_id)
                    ->where(function ($q) use ($tableNum) {
                        $q->where('table_number', $tableNum)
                            ->orWhere('table_number', 'Meja '.ltrim(preg_replace('/[^0-9]/', '', (string) $tableNum), '0'))
                            ->orWhere('table_number', 'Meja '.str_pad(preg_replace('/[^0-9]/', '', (string) $tableNum), 2, '0', STR_PAD_LEFT));
                    })
                    ->update(['status' => 'occupied']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'qr_code_token' => $order->qr_code_token,
                    'total' => $order->total,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'order_type' => $order->order_type,
                    'table_number' => $order->table_number,
                ],
            ], 201);
        });
    }

    public function show(string $id)
    {
        $order = Order::with(['items.menuItem', 'branch'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function showByToken(string $token)
    {
        $order = Order::with(['items.menuItem', 'branch'])
            ->where('qr_code_token', $token)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function showSearch()
    {
        return view('order-search');
    }

    public function processSearch(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
        ]);

        $code = trim($request->order_code);

        $order = Order::where('order_number', $code)
            ->orWhere('qr_code_token', $code)
            ->first();

        if (! $order) {
            return back()->with('error', 'Maaf, pesanan dengan kode tersebut tidak ditemukan.')->withInput();
        }

        return redirect()->route('order.status', ['token' => $order->qr_code_token]);
    }

    public function orderStatus(Request $request, string $token)
    {
        $order = Order::with(['items.menuItem', 'branch'])
            ->where('qr_code_token', $token)
            ->firstOrFail();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
            ]);
        }

        return view('order-status', compact('order'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,process,ready,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);

        // Bug 14: Status Order Bisa Loncat-Loncat (State Machine Validation)
        $validTransitions = [
            'pending' => ['confirmed', 'process', 'ready', 'completed', 'cancelled'],
            'confirmed' => ['pending', 'process', 'ready', 'completed', 'cancelled'],
            'process' => ['pending', 'ready', 'completed', 'cancelled'],
            'ready' => ['pending', 'completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (! in_array($validated['status'], $validTransitions[$order->status] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => "Transisi status dari {$order->status} ke {$validated['status']} tidak diizinkan.",
            ], 422);
        }

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'completed') {
            $updateData['payment_status'] = 'paid';
            if (auth()->check() && ! $order->cashier_id) {
                $updateData['cashier_id'] = auth()->id();
            }

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'cashier_id' => auth()->id() ?? $order->cashier_id,
                    'method' => 'cash',
                    'amount' => $order->total,
                    'cash_given' => $order->total,
                    'change_amount' => 0,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]
            );

            if ($order->table_number && $order->branch_id) {
                Table::where('branch_id', $order->branch_id)
                    ->where('table_number', $order->table_number)
                    ->update(['status' => 'available']);
            }
        } elseif ($validated['status'] === 'cancelled') {
            if ($order->payment_status === 'unpaid') {
                $updateData['payment_status'] = 'voided';
            }
            if ($order->table_number && $order->branch_id) {
                Table::where('branch_id', $order->branch_id)
                    ->where('table_number', $order->table_number)
                    ->update(['status' => 'available']);
            }

            // Return stock
            foreach ($order->items as $item) {
                $menuModel = MenuItem::lockForUpdate()->find($item->menu_item_id);
                if ($menuModel && $menuModel->stock_quantity !== null) {
                    $newStock = $menuModel->stock_quantity + $item->quantity;
                    $status = 'tersedia';
                    if ($newStock === 0) {
                        $status = 'habis';
                    } elseif ($newStock <= 5) {
                        $status = 'hampir_habis';
                    }
                    $menuModel->update([
                        'stock_quantity' => $newStock,
                        'availability_status' => $status,
                    ]);
                }
            }
        }

        $order->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan diperbarui.',
            'data' => $order,
        ]);
    }
}
