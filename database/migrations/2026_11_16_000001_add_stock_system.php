<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stok dihitung per varian (ukuran/motif)
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0);
        });

        // Penanda: stok pesanan ini sudah dikurangi (mencegah dikurangi dua kali)
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('stock_deducted')->default(false);
        });

        // Riwayat setiap perubahan stok
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('product_name', 100);      // salinan, supaya riwayat tetap terbaca
            $table->string('variant_label', 150)->nullable();
            $table->integer('change');                // + masuk, - keluar
            $table->unsignedInteger('stock_after');
            $table->string('reason', 40);             // stok_awal, penyesuaian, pesanan_lunas, pesanan_batal, ...
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn('stock_deducted'));
        Schema::table('product_variants', fn (Blueprint $t) => $t->dropColumn('stock'));
    }
};
