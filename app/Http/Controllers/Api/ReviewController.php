<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Mengambil daftar ulasan untuk menu tertentu.
     */
    public function index(string $menuItemId): JsonResponse
    {
        $reviews = Review::where('menu_item_id', $menuItemId)
            ->where('is_approved', true)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id', 'menu_item_id', 'nama_pelanggan', 'rating', 'komentar', 'created_at']);

        $averageRating = Review::where('menu_item_id', $menuItemId)
            ->where('is_approved', true)
            ->avg('rating') ?? 5.0;

        $reviewCount = Review::where('menu_item_id', $menuItemId)
            ->where('is_approved', true)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'average_rating' => round((float) $averageRating, 1),
                'review_count' => (int) $reviewCount,
                'reviews' => $reviews->map(fn ($r) => [
                    'id' => $r->id,
                    'menu_item_id' => $r->menu_item_id,
                    'nama_pelanggan' => $r->nama_pelanggan,
                    'rating' => (int) $r->rating,
                    'komentar' => $r->komentar,
                    'created_at' => $r->created_at?->diffForHumans() ?? 'Baru saja',
                ]),
            ],
        ]);
    }

    /**
     * Menerima ulasan baru dari aplikasi mobile.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'nama_pelanggan' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:1000',
            'branch_id' => 'nullable|exists:branches,id',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        $review = Review::create([
            'menu_item_id' => $menuItem->id,
            'branch_id' => $request->branch_id ?? 1,
            'order_id' => $request->order_id,
            'nama_pelanggan' => strip_tags((string) $request->nama_pelanggan),
            'rating' => (int) $request->rating,
            'komentar' => strip_tags((string) $request->komentar),
            'is_approved' => true, // Auto-approve untuk review dari aplikasi mobile
            'is_pinned' => false,
        ]);

        // Perbarui cache/rata-rata rating pada menu item
        $newAvg = Review::where('menu_item_id', $menuItem->id)
            ->where('is_approved', true)
            ->avg('rating');

        if ($newAvg !== null) {
            $menuItem->update(['rating' => round((float) $newAvg, 1)]);
        }

        $reviewCount = Review::where('menu_item_id', $menuItem->id)
            ->where('is_approved', true)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan Anda berhasil dikirim dan ditayangkan. Terima kasih!',
            'data' => [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'average_rating' => round((float) ($newAvg ?? $menuItem->rating), 1),
                'review_count' => $reviewCount,
            ],
        ], 201);
    }
}
