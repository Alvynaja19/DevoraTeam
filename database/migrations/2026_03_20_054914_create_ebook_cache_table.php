<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_cache', function (Blueprint $table) {
            $table->id();
            $table->enum('source', ['openlibrary', 'googlebooks', 'gutenberg']);
            $table->string('external_id', 100);
            $table->json('data')->comment('Raw response dari API');
            $table->timestamp('cached_at')->useCurrent();
            $table->timestamp('expires_at');

            $table->unique(['source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_cache');
    }
};
