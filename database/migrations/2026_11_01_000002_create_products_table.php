<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('product_name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            // Harga "mulai dari" = harga varian termurah, diisi otomatis saat simpan
            $table->decimal('price', 12, 2)->default(0);
            $table->string('availability', 50)->default('tersedia');
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('size', 60)->nullable();
            $table->string('motif', 60)->nullable();
            $table->decimal('price', 12, 2);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('products_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('products_id')->constrained('products')->cascadeOnDelete();
            $table->string('image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products_image');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
