<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with branches, menu items, and reviews.
     */
    public function index(Request $request)
    {
        $branches = Branch::where('is_active', true)->get();

        $menuItems = MenuItem::where('is_active', true)
            ->with(['branchPrices'])
            ->get()
            ->map(function ($item) {
                // Determine a display price (from first branch price or default)
                $firstPrice = $item->branchPrices->first();
                $item->display_price = $firstPrice ? (float) $firstPrice->harga : 25000;

                return $item;
            });

        $reviews = Review::where('is_pinned', true)
            ->with(['branch', 'menuItem'])
            ->latest()
            ->take(4)
            ->get();

        $categories = \App\Models\MenuCategory::select('id', 'nama as name', 'slug')->get();
        // Add "Semua Hidangan" at the beginning
        $allCategories = collect([['id' => 'all', 'name' => 'Semua Hidangan', 'slug' => 'all']]);
        $categories = $allCategories->concat($categories);

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return view('pages.home', compact('branches', 'menuItems', 'reviews', 'categories', 'settings'));
    }

    public function storeReservation(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'reservation_time' => 'required|date|after:now',
            'guest_count' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string',
        ]);

        $validated['customer_name'] = strip_tags($validated['customer_name']);
        $validated['customer_phone'] = strip_tags($validated['customer_phone']);
        if (isset($validated['notes'])) {
            $validated['notes'] = strip_tags($validated['notes']);
        }

        Reservation::create($validated);

        return redirect()->back()->withFragment('booking-section')->with('success_booking', 'Terima kasih, permintaan reservasi meja Anda berhasil dikirim. Tim kami akan segera menghubungi Anda untuk konfirmasi!');
    }

    public function checkOrderForReview(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = Order::with('items.menuItem')
            ->where('order_number', $request->order_number)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
        }

        if ($order->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Hanya pesanan yang sudah selesai (completed) yang dapat diulas.']);
        }

        // Get items that haven't been reviewed yet by this customer
        // Simplified: allow them to review items. A better check would be seeing if a review exists for this order_id and menu_item_id.
        $reviewedItemIds = Review::where('order_id', $order->id)->pluck('menu_item_id')->toArray();

        $items = $order->items->map(function ($item) use ($reviewedItemIds) {
            return [
                'menu_item_id' => $item->menuItem->id,
                'name' => $item->menuItem->nama,
                'is_reviewed' => in_array($item->menuItem->id, $reviewedItemIds),
            ];
        });

        // Unique items only, in case they ordered multiple of the same item
        $unique_items = collect($items)->unique('menu_item_id')->values()->all();

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name ?? 'Pelanggan',
                'branch_id' => $order->branch_id,
            ],
            'items' => $unique_items,
        ]);
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'branch_id' => 'required|exists:branches,id',
            'nama_pelanggan' => 'required|string|max:255',
            'reviews' => 'required|array',
            'reviews.*.menu_item_id' => 'required|exists:menu_items,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.komentar' => 'required|string|max:1000',
        ]);

        $orderId = $request->order_id;
        $branchId = $request->branch_id;
        $customerName = strip_tags($request->nama_pelanggan);

        $newReviewCount = 0;

        foreach ($request->reviews as $reviewData) {
            // Check if already reviewed
            $exists = Review::where('order_id', $orderId)
                ->where('menu_item_id', $reviewData['menu_item_id'])
                ->exists();

            if (!$exists) {
                Review::create([
                    'order_id' => $orderId,
                    'menu_item_id' => $reviewData['menu_item_id'],
                    'branch_id' => $branchId,
                    'nama_pelanggan' => $customerName,
                    'rating' => $reviewData['rating'],
                    'komentar' => strip_tags($reviewData['komentar']),
                    'is_approved' => false,
                    'is_pinned' => false,
                ]);
                $newReviewCount++;
            }
        }

        if ($newReviewCount > 0) {
            return redirect()->back()->withFragment('ulasan')->with('success_review', 'Terima kasih atas ulasan Anda! Ulasan akan tampil setelah disetujui admin.');
        } else {
            return redirect()->back()->withFragment('ulasan')->with('success_review', 'Semua produk dalam pesanan ini sudah diulas.');
        }
    }
}
