<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('member_code', 20)->unique()->comment('cth: SIS-2025-001');
            $table->string('qr_token', 100)->unique()->comment('UUID v4 isi QR profil');
            $table->string('name', 100);
            $table->enum('type', ['siswa', 'guru', 'umum']);
            $table->string('nis_nip', 30)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('photo', 255)->nullable();
            $table->enum('status', ['pending', 'aktif', 'nonaktif', 'suspended', 'ditolak'])->default('aktif');
            $table->date('expired_at')->nullable()->comment('NULL = tidak ada batas');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->text('suspend_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
