<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 20);
            $table->text('customer_address');
            $table->dateTime('order_date');
            $table->decimal('total_price', 12, 2)->default(0);
            // payment_status: belum_bayar | menunggu_verifikasi | lunas | ditolak
            $table->string('payment_status', 30)->default('belum_bayar');
            // order_status: menunggu_ongkir | menunggu_pembayaran | diproses | dikirim | selesai | batal
            $table->string('order_status', 30)->default('menunggu_ongkir');
            $table->string('source', 20)->default('web'); // web = dari keranjang, admin = diinput admin
            $table->text('notes')->nullable();

            // Pengiriman: kirim = diantar (ongkir ditentukan admin), ambil = ambil di toko (ongkir 0)
            $table->string('delivery_method', 20)->default('kirim');
            $table->decimal('shipping_cost', 12, 2)->nullable(); // null = belum ditentukan admin
            $table->string('shipping_note')->nullable();

            // Pembayaran lewat website: bukti transfer diupload pelanggan, diverifikasi admin
            $table->string('payment_proof')->nullable();
            $table->string('payment_note')->nullable(); // alasan penolakan bukti
            $table->dateTime('pay_deadline')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        // Nama produk & varian disalin (snapshot) supaya riwayat pesanan tetap utuh
        // walau produknya nanti diubah atau dihapus admin.
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name', 100);
            $table->string('variant_label', 150)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
    }
};
