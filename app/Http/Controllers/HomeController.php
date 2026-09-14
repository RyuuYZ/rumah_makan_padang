<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\MenuItem;
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
        $branches = \Illuminate\Support\Facades\Cache::rememberForever('active_branches', function () {
            return Branch::where('is_active', true)->get();
        });

        $menuItems = \Illuminate\Support\Facades\Cache::rememberForever('active_menu_items', function () {
            return MenuItem::where('is_active', true)
                ->with(['branchPrices'])
                ->get()
                ->map(function ($item) {
                    // Determine a display price (from first branch price or default)
                    $firstPrice = $item->branchPrices->first();
                    $item->display_price = $firstPrice ? (float) $firstPrice->harga : 25000;

                    return $item;
                });
        });

        $reviews = Review::where('is_approved', true)
            ->with('branch')
            ->latest()
            ->take(6)
            ->get();

        $categories = [
            ['id' => 'all', 'name' => 'Semua Hidangan'],
            ['id' => 'daging', 'name' => 'Daging Sapi'],
            ['id' => 'ayam', 'name' => 'Ayam'],
            ['id' => 'ikan', 'name' => 'Ikan'],
            ['id' => 'sayur', 'name' => 'Sayur & Sambal'],
            ['id' => 'topping', 'name' => 'Lauk Tambahan'],
            ['id' => 'minuman', 'name' => 'Minuman Tradisional'],
            ['id' => 'nasi-padang', 'name' => 'Paket Nasi Padang'],
        ];

        return view('pages.home', compact('branches', 'menuItems', 'reviews', 'categories'));
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

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'nama_pelanggan' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:1000',
        ]);

        $validated['nama_pelanggan'] = strip_tags($validated['nama_pelanggan']);
        $validated['komentar'] = strip_tags($validated['komentar']);
        $validated['is_approved'] = false; // Need admin approval

        Review::create($validated);

        return redirect()->back()->withFragment('ulasan')->with('success_review', 'Terima kasih atas ulasan Anda! Ulasan akan tampil setelah disetujui admin.');
    }
}
