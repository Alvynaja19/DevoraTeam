<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('copy_id')->constrained('book_copies');
            $table->timestamp('returned_at')->nullable()->comment('NULL = belum dikembalikan');
            $table->enum('condition_after', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('returned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
