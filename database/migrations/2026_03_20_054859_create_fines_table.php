<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_item_id')->constrained('loan_items')->cascadeOnDelete();
            $table->enum('fine_type', ['keterlambatan', 'kerusakan', 'kehilangan']);
            $table->smallInteger('days_late')->unsigned()->nullable();
            $table->integer('amount')->unsigned()->comment('Nominal denda (Rp)');
            $table->enum('status', ['belum_lunas', 'lunas', 'dibebaskan'])->default('belum_lunas');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('freed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('freed_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('fine_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
