<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('barcode')->unique()->nullable()->after('isbn');
            $table->string('no_klasifikasi')->nullable()->after('barcode');
            $table->string('kota')->nullable()->after('penerbit');
            $table->string('perolehan')->nullable()->after('kota');
            $table->date('tanggal_diterima')->nullable()->after('perolehan');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'no_klasifikasi', 'kota', 'perolehan', 'tanggal_diterima']);
        });
    }
};
