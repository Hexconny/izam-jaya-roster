<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gambar kategori (kartu "Produk Kami") dan gambar banner utama beranda
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('business_profile', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('categories', fn (Blueprint $t) => $t->dropColumn('image'));
        Schema::table('business_profile', fn (Blueprint $t) => $t->dropColumn('hero_image'));
    }
};
