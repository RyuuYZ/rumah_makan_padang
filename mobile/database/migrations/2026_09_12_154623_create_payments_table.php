<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel payments sesuai ERD (PAYMENTS) dan BRD:
     * - method: tunai atau QRIS (POS-07, POS-08)
     * - cash_given / change_amount: untuk kembalian tunai (BR-05)
     * - reference_number: kode transaksi QRIS
     * - status: pending → completed / failed (BR-06)
     * - Harga di-snapshot dari orders.total saat pembayaran, tidak berubah meski menu berubah (BR-03)
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('method', ['cash', 'qris'])->default('cash');
            $table->decimal('amount', 12, 2);
            $table->decimal('cash_given', 12, 2)->nullable()->comment('Uang diterima (khusus tunai)');
            $table->decimal('change_amount', 12, 2)->nullable()->comment('Kembalian (khusus tunai)');
            $table->string('reference_number', 100)->nullable()->comment('Kode referensi QRIS / transaksi');
            $table->enum('status', ['pending', 'completed', 'failed', 'voided'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
