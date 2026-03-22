<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code', 30)->unique()->comment('cth: TRX-20250901-001');
            $table->foreignId('member_id')->constrained('members');
            $table->date('loan_date');
            $table->date('due_date')->comment('Dihitung otomatis via sp_calculate_due_date');
            $table->enum('loan_type', ['pembaca', 'lomba'])->default('pembaca');
            $table->date('competition_end_date')->nullable()->comment('Wajib jika loan_type = lomba');
            $table->tinyInteger('extended_count')->unsigned()->default(0);
            $table->date('extended_due_date')->nullable();
            $table->enum('status', ['aktif', 'diperpanjang', 'terlambat', 'selesai', 'hilang'])->default('aktif');
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('due_date');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
