<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        foreach ($branches as $branch) {
            for ($i = 1; $i <= 20; $i++) {
                Table::firstOrCreate([
                    'branch_id' => $branch->id,
                    'table_number' => 'Meja '.str_pad($i, 2, '0', STR_PAD_LEFT),
                ], [
                    'capacity' => ($i % 4 == 0) ? 6 : 4, // Make every 4th table larger
                    'status' => 'available',
                    'is_active' => true,
                ]);
            }
        }
    }
}
