<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where(function ($query) {
            $query->where('status', 'completed')
                ->orWhere('payment_status', 'paid');
        })->where('status', '!=', 'cancelled')->sum('total');
        $rawStatusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Status counts for visual pipeline
        $statusCounts = [
            'pending' => (int) $rawStatusCounts->get('pending', 0),
            'confirmed' => (int) $rawStatusCounts->get('confirmed', 0),
            'cooking' => (int) $rawStatusCounts->get('cooking', 0),
            'ready' => (int) $rawStatusCounts->get('ready', 0),
            'completed' => (int) $rawStatusCounts->get('completed', 0),
            'cancelled' => (int) $rawStatusCounts->get('cancelled', 0),
        ];

        $totalOrders = (int) $rawStatusCounts->sum();
        $pendingOrders = $statusCounts['pending'];
        $totalMenuItems = MenuItem::where('is_active', true)->count();
        $totalBranches = Branch::where('is_active', true)->count();
        $pendingReviews = Review::where('is_approved', false)->count();

        // Recent orders
        $recentOrders = Order::with(['branch', 'items.menuItem.category'])
            ->latest()
            ->take(6)
            ->get();

        // Top dishes (excluding cancelled orders)
        $topDishes = OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->with('menuItem.category')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'totalMenuItems',
            'totalBranches',
            'pendingReviews',
            'statusCounts',
            'recentOrders',
            'topDishes'
        ));
    }
}
