<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members');
            $table->date('visit_date');
            $table->time('visit_time');
            $table->enum('category', ['membaca', 'pengunjung', 'peminjam'])->default('pengunjung');
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('visit_date');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
