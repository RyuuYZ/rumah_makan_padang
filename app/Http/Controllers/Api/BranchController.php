<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::query()->where('is_active', true);

        if ($request->has('kota')) {
            $query->where('kota', 'like', '%'.$request->kota.'%');
        }

        $branches = $query->orderBy('kota')->get();

        return response()->json([
            'success' => true,
            'data' => $branches,
        ]);
    }

    public function show(string $id)
    {
        $branch = Branch::with(['menuPrices.menuItem', 'reviews'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $branch,
        ]);
    }

    public function branchesWithMenu()
    {
        $branches = Branch::with(['menuPrices' => function ($query) {
            $query->where('is_available', true)->with('menuItem');
        }, 'menuPrices.menuItem'])
            ->where('is_active', true)
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'nama' => $branch->nama,
                    'kota' => $branch->kota,
                    'alamat' => $branch->formatted_address,
                    'jam_buka' => $branch->jam_buka,
                    'kontak_whatsapp' => $branch->kontak_whatsapp,
                    'menu_items' => $branch->menuPrices->map(function ($bp) {
                        return [
                            'id' => $bp->menu_item_id,
                            'nama' => $bp->menuItem->nama,
                            'kategori' => $bp->menuItem->kategori,
                            'deskripsi' => $bp->menuItem->deskripsi,
                            'foto' => $bp->menuItem->foto,
                            'badge' => $bp->menuItem->badge,
                            'rating' => (float) $bp->menuItem->rating,
                            'harga' => (int) $bp->harga,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $branches,
        ]);
    }
}
