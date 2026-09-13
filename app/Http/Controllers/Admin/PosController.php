<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PosController extends Controller
{
    /**
     * Tampilkan antarmuka POS Scanner.
     */
    public function index()
    {
        return view('admin.pos.index');
    }

    /**
     * Cari pesanan berdasarkan kode (dari input manual atau scan QR).
     */
    public function findOrder(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
        ]);

        $code = trim($request->order_code);

        // Cari pesanan berdasarkan nomor pesanan ATAU token QR
        $order = Order::with(['branch', 'items.menuItem', 'payment'])
            ->where('order_number', $code)
            ->orWhere('qr_code_token', $code)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan dengan kode tersebut.',
            ], 404);
        }

        // Format data untuk dikirim ke frontend
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name ?: 'Pelanggan Walk-in',
            'customer_phone' => $order->customer_phone ?: '-',
            'service_type' => $order->service_type,
            'table_number' => $order->table_number ?: '-',
            'status' => $order->status,
            'total' => $order->total,
            'total_formatted' => 'Rp '.number_format($order->total, 0, ',', '.'),
            'created_at' => $order->created_at->format('d M Y, H:i'),
            'branch' => [
                'name' => $order->branch->name ?? '-',
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->menuItem->name ?? 'Menu Dihapus',
                    'quantity' => $item->quantity,
                    'price_formatted' => 'Rp '.number_format($item->price, 0, ',', '.'),
                    'subtotal_formatted' => 'Rp '.number_format($item->subtotal, 0, ',', '.'),
                    'notes' => $item->notes,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'order' => $data,
            'message' => 'Pesanan berhasil ditemukan.',
        ]);
    }
}
