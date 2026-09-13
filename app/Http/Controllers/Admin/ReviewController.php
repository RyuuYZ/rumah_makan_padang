<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('branch')->latest();

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApprove($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => ! $review->is_approved]);

        $status = $review->is_approved ? 'disetujui dan kini tampil di website publik' : 'dibatalkan persetujuannya';

        return redirect()->back()->with('success', "Ulasan dari {$review->nama_pelanggan} telah {$status}.");
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
