<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchMenuPrice;
use App\Models\MenuItem;
use App\Services\WebpUploadService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('branchPrices')->latest();

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('deskripsi', 'like', "%{$request->search}%");
            });
        }

        $menuItems = $query->latest()->paginate(10)->withQueryString();

        $categories = [
            'daging' => 'Lauk Daging',
            'ayam' => 'Lauk Ayam',
            'ikan' => 'Lauk Ikan',
            'sayur' => 'Sayur & Sambal',
            'topping' => 'Lauk Tambahan',
            'minuman' => 'Minuman Tradisional',
            'nasi-padang' => 'Paket Nasi Padang',
        ];

        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    public function store(Request $request, WebpUploadService $webpUploadService)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:daging,ayam,ikan,sayur,topping,minuman,nasi-padang',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|string',
            'foto_file' => 'nullable|image|mimes:webp,png,jpg,jpeg,gif,bmp,svg|max:10240',
            'badge' => 'nullable|in:Signature,Favorit,Baru',
            'rating' => 'nullable|numeric|min:0|max:5',
            'harga' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $defaultFoto = '/menu/nasi-padang-rendang.webp';
        $fotoPath = ($request->filled('foto')) ? $request->input('foto') : $defaultFoto;

        if ($request->hasFile('foto_file')) {
            $fotoPath = $webpUploadService->uploadAndConvertToWebp($request->file('foto_file'), 'menu');
        }

        $stock = $validated['stock_quantity'] ?? null;
        $status = 'tersedia';
        if ($stock !== null) {
            if ((int) $stock === 0) {
                $status = 'habis';
            } elseif ((int) $stock <= 5) {
                $status = 'hampir_habis';
            }
        }

        $menuItem = MenuItem::create([
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto' => $fotoPath,
            'badge' => $validated['badge'] ?? null,
            'rating' => $validated['rating'] ?? 5.0,
            'is_active' => true,
            'availability_status' => $status,
            'stock_quantity' => $stock,
        ]);

        // Seed prices for all branches
        $branches = Branch::all();
        foreach ($branches as $branch) {
            BranchMenuPrice::create([
                'branch_id' => $branch->id,
                'menu_item_id' => $menuItem->id,
                'harga' => $validated['harga'],
                'is_available' => true,
            ]);
        }

        \Illuminate\Support\Facades\Cache::forget('active_menu_items');

        return redirect()->route('admin.menu.index')->with('success', "Hidangan '{$menuItem->nama}' (WebP) berhasil ditambahkan ke menu!");
    }

    public function update(Request $request, $id, WebpUploadService $webpUploadService)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:daging,ayam,ikan,sayur,topping,minuman,nasi-padang',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|string',
            'foto_file' => 'nullable|image|mimes:webp,png,jpg,jpeg,gif,bmp,svg|max:10240',
            'badge' => 'nullable|in:Signature,Favorit,Baru',
            'rating' => 'nullable|numeric|min:0|max:5',
            'harga' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $menuItem = MenuItem::findOrFail($id);
        $fotoPath = $menuItem->foto;

        if ($request->hasFile('foto_file')) {
            $fotoPath = $webpUploadService->uploadAndConvertToWebp($request->file('foto_file'), 'menu');
        } elseif ($request->filled('foto')) {
            $fotoPath = $request->input('foto');
        }

        $stock = $validated['stock_quantity'] ?? null;
        $status = 'tersedia';
        if ($stock !== null) {
            if ((int) $stock === 0) {
                $status = 'habis';
            } elseif ((int) $stock <= 5) {
                $status = 'hampir_habis';
            }
        }

        $menuItem->update([
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto' => $fotoPath,
            'badge' => $validated['badge'] ?? null,
            'rating' => $validated['rating'] ?? $menuItem->rating,
            'availability_status' => $status,
            'stock_quantity' => $stock,
        ]);

        // Update branch menu prices base
        BranchMenuPrice::where('menu_item_id', $menuItem->id)->update([
            'harga' => $validated['harga'],
        ]);

        \Illuminate\Support\Facades\Cache::forget('active_menu_items');

        return redirect()->route('admin.menu.index')->with('success', "Menu '{$menuItem->nama}' berhasil diperbarui!");
    }

    public function toggleActive($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->update(['is_active' => ! $menuItem->is_active]);

        $statusText = $menuItem->is_active ? 'diaktifkan' : 'dinonaktifkan';

        \Illuminate\Support\Facades\Cache::forget('active_menu_items');

        return redirect()->back()->with('success', "Status menu '{$menuItem->nama}' berhasil {$statusText}.");
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $name = $menuItem->nama;
        $menuItem->delete();

        \Illuminate\Support\Facades\Cache::forget('active_menu_items');

        return redirect()->route('admin.menu.index')->with('success', "Menu '{$name}' berhasil dihapus.");
    }
}
