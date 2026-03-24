<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value');
            $table->enum('type', ['string', 'integer', 'boolean', 'json'])->default('string');
            $table->string('group', 40)->default('general');
            $table->string('description', 255)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            // Denda
            ['key' => 'denda_per_hari', 'value' => '1000', 'type' => 'integer', 'group' => 'denda', 'description' => 'Nominal denda keterlambatan per hari (Rp)'],
            ['key' => 'denda_rusak_ringan', 'value' => '10000', 'type' => 'integer', 'group' => 'denda', 'description' => 'Denda buku rusak ringan (Rp)'],
            ['key' => 'denda_rusak_berat', 'value' => '30000', 'type' => 'integer', 'group' => 'denda', 'description' => 'Denda buku rusak berat (Rp)'],
            ['key' => 'denda_hilang', 'value' => '50000', 'type' => 'integer', 'group' => 'denda', 'description' => 'Denda buku hilang (Rp)'],
            // Peminjaman
            ['key' => 'max_pinjam', 'value' => '2', 'type' => 'integer', 'group' => 'peminjaman', 'description' => 'Maksimum buku dipinjam per anggota'],
            ['key' => 'lama_pinjam_hari', 'value' => '3', 'type' => 'integer', 'group' => 'peminjaman', 'description' => 'Lama peminjaman default dalam hari kerja'],
            ['key' => 'max_perpanjangan', 'value' => '1', 'type' => 'integer', 'group' => 'peminjaman', 'description' => 'Maksimum perpanjangan per transaksi'],
            ['key' => 'aktif_rules_rabu_kamis', 'value' => 'true', 'type' => 'boolean', 'group' => 'peminjaman', 'description' => 'Aktifkan rules: pinjam Rabu/Kamis → kembali Senin'],
            // Reservasi
            ['key' => 'batas_ambil_reservasi', 'value' => '2', 'type' => 'integer', 'group' => 'reservasi', 'description' => 'Hari batas ambil buku reservasi setelah notifikasi'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};