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
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete()->after('id');
            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete()->after('order_id');
            $table->boolean('is_pinned')->default(false)->after('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['menu_item_id']);
            $table->dropColumn(['order_id', 'menu_item_id', 'is_pinned']);
        });
    }
};
