<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->query('branch_id');
        $branches = Branch::where('is_active', true)->get();

        $query = Table::with('branch');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $tables = $query->orderBy('branch_id')->orderBy('table_number')->paginate(30);

        return view('admin.tables.index', compact('tables', 'branches', 'branchId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'table_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        // Check if table_number already exists in this branch
        $exists = Table::where('branch_id', $request->branch_id)
            ->where('table_number', $request->table_number)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Nomor meja sudah ada di cabang ini.');
        }

        Table::create($validated);

        return back()->with('success', 'Meja berhasil ditambahkan.');
    }

    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
            'is_active' => 'boolean',
        ]);

        if ($request->table_number !== $table->table_number) {
            $exists = Table::where('branch_id', $table->branch_id)
                ->where('table_number', $request->table_number)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Nomor meja sudah ada di cabang ini.');
            }
        }

        $table->update($validated);

        return back()->with('success', 'Data meja berhasil diperbarui.');
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return back()->with('success', 'Meja berhasil dihapus.');
    }
}
