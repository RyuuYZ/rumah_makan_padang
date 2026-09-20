<?php

use App\Models\Branch;
use App\Models\BranchMenuPrice;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Table;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ambil semua cabang dan kelompokkan berdasarkan nama yang sama
        $allBranches = Branch::withTrashed()->orderBy('id')->get();
        $grouped = $allBranches->groupBy(function ($b) {
            return trim(mb_strtolower((string) $b->nama));
        });

        foreach ($grouped as $name => $branches) {
            if ($branches->count() <= 1) {
                continue;
            }

            $canonical = $branches->first();
            $duplicates = $branches->slice(1);

            foreach ($duplicates as $duplicate) {
                // Relasikan atau bersihkan tabel meja
                $duplicateTables = Table::where('branch_id', $duplicate->id)->get();
                foreach ($duplicateTables as $table) {
                    $existingTable = Table::where('branch_id', $canonical->id)
                        ->where('table_number', $table->table_number)
                        ->first();

                    if ($existingTable) {
                        Order::where('table_id', $table->id)->update([
                            'table_id' => $existingTable->id,
                            'branch_id' => $canonical->id,
                        ]);
                        Reservation::where('table_id', $table->id)->update([
                            'table_id' => $existingTable->id,
                            'branch_id' => $canonical->id,
                        ]);
                        $table->delete();
                    } else {
                        $table->update(['branch_id' => $canonical->id]);
                    }
                }

                // Bersihkan harga menu cabang duplikat
                $duplicatePrices = BranchMenuPrice::where('branch_id', $duplicate->id)->get();
                foreach ($duplicatePrices as $price) {
                    $existingPrice = BranchMenuPrice::where('branch_id', $canonical->id)
                        ->where('menu_item_id', $price->menu_item_id)
                        ->first();

                    if ($existingPrice) {
                        $price->delete();
                    } else {
                        $price->update(['branch_id' => $canonical->id]);
                    }
                }

                // Relasikan pesanan, reservasi, dan ulasan ke cabang utama
                Order::where('branch_id', $duplicate->id)->update(['branch_id' => $canonical->id]);
                Reservation::where('branch_id', $duplicate->id)->update(['branch_id' => $canonical->id]);
                Review::where('branch_id', $duplicate->id)->update(['branch_id' => $canonical->id]);

                // Hapus data duplikat secara permanen
                $duplicate->forceDelete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operasi pembersihan duplikat tidak memerlukan rollback
    }
};
