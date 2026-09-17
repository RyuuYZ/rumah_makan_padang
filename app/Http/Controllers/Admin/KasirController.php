<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Table;

class KasirController extends Controller
{
    /**
     * Menampilkan antarmuka POS (Kasir)
     */
    public function index()
    {
        $branches = Branch::where('is_active', true)->get();
        // Mengambil semua menu yang aktif untuk ditampilkan di grid kasir
        $menuItems = MenuItem::where('is_active', true)
            ->with(['branchPrices', 'category'])
            ->orderBy('menu_category_id')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                // Determine a display price (from first branch price or default)
                $firstPrice = $item->branchPrices->first();
                $item->display_price = $firstPrice ? (float) $firstPrice->harga : 25000;

                return $item;
            });

        $tables = Table::where('is_active', true)->get();

        return view('kasir.index', compact('branches', 'menuItems', 'tables'));
    }
}
