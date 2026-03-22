<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('source', ['openlibrary', 'googlebooks', 'gutenberg']);
            $table->string('external_id', 100);
            $table->json('progress_data')->nullable()->comment('page, percentage, last_position');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['member_id', 'source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_reading_progress');
    }
};
