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

        $reviews = Review::where('is_approved', true)
            ->with('branch')
            ->latest()
            ->take(6)
            ->get();

        $categories = [
            ['id' => 'all', 'name' => 'Semua Hidangan'],
            ['id' => 'ayam', 'name' => 'Ayam'],
            ['id' => 'ikan', 'name' => 'Ikan'],
            ['id' => 'daging', 'name' => 'Daging Sapi'],
            ['id' => 'topping', 'name' => 'Topping & Bumbu'],
            ['id' => 'minuman', 'name' => 'Minuman Tradisional'],
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

        Reservation::create($validated);

        return redirect()->back()->withFragment('booking-section')->with('success_booking', 'Terima kasih, permintaan reservasi meja Anda berhasil dikirim. Tim kami akan segera menghubungi Anda untuk konfirmasi!');
    }
}
