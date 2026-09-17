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
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalMenuItems = MenuItem::where('is_active', true)->count();
        $totalBranches = Branch::where('is_active', true)->count();
        $pendingReviews = Review::where('is_approved', false)->count();

        // Status counts for visual pipeline
        $statusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'cooking' => Order::where('status', 'cooking')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // Recent orders
        $recentOrders = Order::with(['branch', 'items.menuItem'])
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
            ->with('menuItem')
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
