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
            $table->index('kategori');
            $table->index('is_active');
            $table->index('nama');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('branch_id');
            $table->index('created_at');
            $table->index('order_number');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->index('status');
            $table->index('reservation_time');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['nama']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['order_number']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['reservation_time']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['is_approved']);
        });
    }
};
