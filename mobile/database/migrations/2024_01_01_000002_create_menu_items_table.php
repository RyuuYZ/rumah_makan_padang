<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kategori', ['nasi-padang', 'daging', 'ayam', 'ikan', 'sayur', 'topping', 'minuman']);
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('badge', ['Signature', 'Favorit', 'Baru'])->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
