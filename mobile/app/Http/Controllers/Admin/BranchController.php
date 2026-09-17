<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchMenuPrice;
use App\Models\MenuItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['orders', 'reviews'])->get();

        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kota' => 'required|string|max:100',
            'alamat' => 'required|string',
            'jam_buka' => 'required|string|max:100',
            'kontak_whatsapp' => 'nullable|string|max:25',
        ]);

        $branch = Branch::create(array_merge($validated, ['is_active' => true]));

        // Copy menu pricing for this new branch using the highest existing price as master
        $menuItems = MenuItem::with('branchPrices')->get();
        foreach ($menuItems as $item) {
            $basePrice = $item->branchPrices->max('harga') ?? 25000;
            BranchMenuPrice::create([
                'branch_id' => $branch->id,
                'menu_item_id' => $item->id,
                'harga' => $basePrice,
                'is_available' => true,
            ]);
        }

        // Generate initial default tables for new branch
        for ($i = 1; $i <= 20; $i++) {
            Table::firstOrCreate([
                'branch_id' => $branch->id,
                'table_number' => 'Meja '.str_pad($i, 2, '0', STR_PAD_LEFT),
            ], [
                'capacity' => ($i % 4 == 0) ? 6 : 4,
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        Cache::forget('active_branches_v2');

        return redirect()->route('admin.branches.index')->with('success', "Cabang baru '{$branch->nama}' berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kota' => 'required|string|max:100',
            'alamat' => 'required|string',
            'jam_buka' => 'required|string|max:100',
            'kontak_whatsapp' => 'nullable|string|max:25',
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update($validated);

        Cache::forget('active_branches_v2');

        return redirect()->route('admin.branches.index')->with('success', "Data cabang '{$branch->nama}' berhasil diperbarui!");
    }

    public function toggleActive($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->update(['is_active' => ! $branch->is_active]);

        $statusText = $branch->is_active ? 'dibuka kembali' : 'ditutup sementara';

        Cache::forget('active_branches_v2');

        return redirect()->back()->with('success', "Status cabang '{$branch->nama}' {$statusText}.");
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $nama = $branch->nama;
        $branch->delete();

        Cache::forget('active_branches_v2');

        return redirect()->route('admin.branches.index')->with('success', "Cabang '{$nama}' berhasil dihapus.");
    }
}
