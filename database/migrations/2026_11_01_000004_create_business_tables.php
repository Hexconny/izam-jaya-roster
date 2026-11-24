<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_profile', function (Blueprint $table) {
            $table->id();
            $table->string('business_name', 100);
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('image')->nullable();
            // Kolom tambahan untuk kebutuhan "Edit Beranda" dan peta lokasi toko
            $table->string('hero_title', 150)->nullable();
            $table->string('hero_subtitle', 300)->nullable();
            $table->string('open_hours', 100)->nullable();
            $table->string('instagram', 100)->nullable();
            $table->string('map_query', 255)->nullable();
            // Info pembayaran yang ditampilkan ke pelanggan di halaman pesanan
            $table->string('bank_name', 60)->nullable();
            $table->string('bank_account', 40)->nullable();
            $table->string('bank_holder', 100)->nullable();
            $table->string('qris_image')->nullable();
            $table->text('shipping_policy')->nullable();
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('business_profile');
    }
};
