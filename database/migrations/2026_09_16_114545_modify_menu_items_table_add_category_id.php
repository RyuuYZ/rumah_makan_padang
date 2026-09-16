<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('menu_category_id')->nullable()->after('nama')->constrained('menu_categories')->nullOnDelete();
        });

        $categories = ['nasi-padang', 'daging', 'ayam', 'ikan', 'sayur', 'topping', 'minuman'];
        foreach ($categories as $cat) {
            $id = \Illuminate\Support\Facades\DB::table('menu_categories')->insertGetId([
                'nama' => ucwords(str_replace('-', ' ', $cat)),
                'slug' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            \Illuminate\Support\Facades\DB::table('menu_items')->where('kategori', $cat)->update(['menu_category_id' => $id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['menu_category_id']);
            $table->dropColumn('menu_category_id');
        });
    }
};
