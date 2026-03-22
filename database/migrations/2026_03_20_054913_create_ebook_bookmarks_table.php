<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('source', ['openlibrary', 'googlebooks', 'gutenberg']);
            $table->string('external_id', 100)->comment('ID buku di sumber eksternal');
            $table->string('title', 255);
            $table->string('author', 200)->nullable();
            $table->string('cover_url', 500)->nullable();
            $table->string('read_url', 500)->nullable()->comment('URL baca online');
            $table->timestamp('last_read')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['member_id', 'source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_bookmarks');
    }
};
