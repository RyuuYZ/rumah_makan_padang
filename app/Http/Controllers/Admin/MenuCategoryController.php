<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::withCount('items')->latest()->get();
        return view('admin.menu_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        MenuCategory::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
        ]);

        return redirect()->route('menu-categories.index')->with('success', 'Kategori menu berhasil ditambahkan.');
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $menuCategory->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
        ]);

        return redirect()->route('menu-categories.index')->with('success', 'Kategori menu berhasil diperbarui.');
    }

    public function destroy(MenuCategory $menuCategory)
    {
        if ($menuCategory->items()->count() > 0) {
            return redirect()->route('menu-categories.index')->with('error', 'Tidak dapat menghapus kategori yang masih memiliki menu.');
        }

        $menuCategory->delete();

        return redirect()->route('menu-categories.index')->with('success', 'Kategori menu berhasil dihapus.');
    }
}
