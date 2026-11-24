<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Logo toko yang diupload admin. Kosong = pakai public/images/logo.png
        Schema::table('business_profile', function (Blueprint $table) {
            $table->string('logo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('business_profile', fn (Blueprint $t) => $t->dropColumn('logo'));
    }
};
