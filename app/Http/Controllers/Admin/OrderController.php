<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['branch', 'items.menuItem'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        $orders = $query->paginate(10)->withQueryString();
        $branches = Branch::where('is_active', true)->get();

        return view('admin.orders.index', compact('orders', 'branches'));
    }

    public function show($id)
    {
        $order = Order::with(['branch', 'items.menuItem'])->findOrFail($id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cooking,ready,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $updateData = ['status' => $request->status];

        if ($request->status === 'completed') {
            $updateData['payment_status'] = 'paid';
            if (auth()->check() && ! $order->cashier_id) {
                $updateData['cashier_id'] = auth()->id();
            }

            // Create or update Payment record
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

            // Free table if dine-in
            if ($order->table_number && $order->branch_id) {
                Table::where('branch_id', $order->branch_id)
                    ->where('table_number', $order->table_number)
                    ->update(['status' => 'available']);
            }
        } elseif ($request->status === 'cancelled') {
            if ($order->payment_status === 'unpaid') {
                $updateData['payment_status'] = 'voided';
            }
            // Free table if dine-in
            if ($order->table_number && $order->branch_id) {
                Table::where('branch_id', $order->branch_id)
                    ->where('table_number', $order->table_number)
                    ->update(['status' => 'available']);
            }
        }

        $order->update($updateData);

        if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status pesanan #{$order->id} berhasil diperbarui menjadi ".ucfirst($request->status).'!',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'status_label' => ucfirst($order->status),
                    'payment_status' => $order->payment_status,
                ],
                'pending_count' => Order::where('status', 'pending')->count(),
            ]);
        }

        return redirect()->back()->with('success', "Status pesanan #{$order->id} berhasil diperbarui menjadi ".ucfirst($request->status).'!');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->back()->with('success', "Pesanan #{$id} berhasil dihapus.");
    }
}
