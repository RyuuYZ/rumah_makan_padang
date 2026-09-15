<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'order_type' => 'nullable|in:dine_in,takeaway',
            'table_number' => 'nullable|string|max:20',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        // Validasi ketersediaan menu dan hitung total (snapshot harga saat order — BR-03)
        return DB::transaction(function () use ($request, $validated) {
            $groupedItems = [];
            foreach ($validated['items'] as $item) {
                $id = $item['menu_item_id'];
                if (isset($groupedItems[$id])) {
                    $groupedItems[$id]['quantity'] += $item['quantity'];
                    if (!empty($item['notes'])) {
                        $groupedItems[$id]['notes'] = !empty($groupedItems[$id]['notes']) 
                            ? $groupedItems[$id]['notes'] . ' | ' . $item['notes'] 
                            : $item['notes'];
                    }
                } else {
                    $groupedItems[$id] = $item;
                }
            }
            $validated['items'] = array_values($groupedItems);

            $items = [];
        foreach ($validated['items'] as $item) {
            $price = BranchMenuPrice::where('branch_id', $validated['branch_id'])
                ->where('menu_item_id', $item['menu_item_id'])
                ->first();
            $menuModel = MenuItem::lockForUpdate()->find($item['menu_item_id']);

            if (! $price || ! $price->is_available || ($menuModel && $menuModel->availability_status === 'habis')) {
                return response()->json([
                    'success' => false,
                    'message' => "Menu dengan ID {$item['menu_item_id']} tidak tersedia.",
                ], 422);
            }

            if ($menuModel && $menuModel->stock_quantity !== null && $menuModel->stock_quantity < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok untuk menu {$menuModel->nama} tidak mencukupi. Sisa stok: {$menuModel->stock_quantity} porsi.",
                ], 422);
            }

            $items[] = array_merge($item, ['price' => (float) $price->harga]);
        }
        
        $orderMethod = $validated['order_type'] ?? 'dine_in';
        $tableNum = $validated['table_number'] ?? null;
        
        // B8: Validasi Ketersediaan Meja
        if ($orderMethod === 'dine_in' && $tableNum) {
            $table = \App\Models\Table::lockForUpdate()
                ->where('branch_id', $validated['branch_id'])
                ->where('table_number', $tableNum)
                ->first();
                
            if (!$table || $table->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => "Maaf, Meja {$tableNum} sudah terisi atau tidak tersedia.",
                ], 422);
            }
        }

        $total = collect($items)->sum(fn ($item) => $item['quantity'] * $item['price']);

        // Buat order; order_number & qr_code_token digenerate otomatis via Order::booted()
        $order = Order::create([
            'branch_id' => $validated['branch_id'],
            'order_type' => $validated['order_type'] ?? 'dine_in',
            'table_number' => $validated['table_number'] ?? null,
            'source' => $request->input('source', 'customer_web'),
            'method' => 'dine-in',
            'customer_name' => isset($validated['customer_name']) ? strip_tags($validated['customer_name']) : null,
            'customer_phone' => isset($validated['customer_phone']) ? strip_tags($validated['customer_phone']) : null,
            'notes' => isset($validated['notes']) ? strip_tags($validated['notes']) : null,
            'total' => $total,
            'status' => $request->input('payment_status') === 'paid' ? 'completed' : 'pending',
            'payment_status' => $request->input('payment_status', 'unpaid'),
            'cashier_id' => $request->input('source') === 'kasir_pos' && auth()->check() ? auth()->id() : null,
        ]);

        if ($order->payment_status === 'paid') {
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

            // Decrement Stock
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

        if ($order->method === 'dine-in' && $request->table_number) {
            Table::where('branch_id', $order->branch_id)
                ->where('table_number', $request->table_number)
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

    public function orderStatus(string $token)
    {
        $order = Order::with(['items.menuItem', 'branch'])
            ->where('qr_code_token', $token)
            ->firstOrFail();

        return view('order-status', compact('order'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cooking,ready,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        
        // Bug 14: Status Order Bisa Loncat-Loncat (State Machine Validation)
        $validTransitions = [
            'pending' => ['confirmed', 'cooking', 'ready', 'completed', 'cancelled'],
            'confirmed' => ['pending', 'cooking', 'ready', 'completed', 'cancelled'],
            'cooking' => ['pending', 'ready', 'completed', 'cancelled'],
            'ready' => ['pending', 'completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];
        
        if (!in_array($validated['status'], $validTransitions[$order->status] ?? [])) {
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
