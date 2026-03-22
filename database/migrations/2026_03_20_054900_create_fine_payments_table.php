<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_id')->constrained('fines')->cascadeOnDelete();
            $table->integer('amount_paid')->unsigned();
            $table->date('payment_date');
            $table->string('receipt_number', 50)->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_payments');
    }
};
