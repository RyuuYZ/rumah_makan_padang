<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom-kolom penting ke tabel orders sesuai BRD & ERD:
     * - order_number: nomor unik tiap transaksi (BR-01)
     * - order_type: dine_in atau takeaway (BRD Skenario A, B, C)
     * - table_number: nomor meja / titik pemesanan (POS-04)
     * - qr_code_token: token unik untuk generate QR Order customer (CUS-07)
     * - payment_status: status pembayaran terpisah dari status order
     * - source: asal pesanan (kasir POS / customer web / QR)
     * - cashier_id: FK ke user kasir yang memproses (nullable)
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number', 30)->unique()->nullable()->after('id');
            $table->enum('order_type', ['dine_in', 'takeaway'])->default('dine_in')->after('branch_id');
            $table->string('table_number', 20)->nullable()->after('order_type');
            $table->string('qr_code_token', 100)->unique()->nullable()->after('table_number');
            $table->enum('source', ['pos', 'customer_web', 'qr_scan'])->default('pos')->after('qr_code_token');
            $table->enum('payment_status', ['unpaid', 'paid', 'voided'])->default('unpaid')->after('total');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete()->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cashier_id']);
            $table->dropColumn([
                'order_number',
                'order_type',
                'table_number',
                'qr_code_token',
                'source',
                'payment_status',
                'cashier_id',
            ]);
        });
    }
};
