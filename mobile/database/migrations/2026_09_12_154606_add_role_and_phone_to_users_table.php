<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom role dan phone ke tabel users.
     * ERD memisahkan CASHIERS/ADMINS, namun kita satukan dengan kolom role
     * agar autentikasi Laravel (Auth Guard) lebih sederhana.
     * Catatan DBA: lihat private-note.md — poin A.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'cashier', 'customer', 'warehouse_staff', 'kitchen'])
                ->default('customer')
                ->after('email');
            $table->string('phone', 20)->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'is_active']);
        });
    }
};
