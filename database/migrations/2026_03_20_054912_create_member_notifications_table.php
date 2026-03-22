<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('type', [
                'akun_diaktifkan',
                'akun_ditolak',
                'reminder_pengembalian',
                'terlambat_pengembalian',
                'denda_baru',
                'denda_lunas',
                'reservasi_tersedia',
                'reservasi_kadaluarsa',
                'perpanjangan_berhasil',
                'info',
            ]);
            $table->string('title', 150);
            $table->text('body');
            $table->json('data')->nullable()->comment('Payload: loan_id, fine_id, book_id, dll');
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('type');
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_notifications');
    }
};
