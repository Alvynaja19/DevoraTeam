<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('classification_number', 50)->nullable()->after('isbn')->comment('No Klasifikasi');
            $table->string('city', 100)->nullable()->after('publisher')->comment('Kota Penerbit');
            $table->string('acquisition_type', 100)->nullable()->after('cover_image')->comment('Metode perolehan (Sumbangan/Beli/dll)');
            $table->date('received_date')->nullable()->after('acquisition_type')->comment('Diterima Tanggal');
            $table->string('inventory_year', 4)->nullable()->after('received_date')->comment('Tahun (Inventaris)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'classification_number',
                'city',
                'acquisition_type',
                'received_date',
                'inventory_year',
            ]);
        });
    }
};
