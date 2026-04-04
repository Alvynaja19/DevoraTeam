<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('isbn', 20)->unique()->nullable();
            $table->string('title', 255);
            $table->string('author', 200);
            $table->string('publisher', 150)->nullable();
            $table->year('year')->nullable();
            $table->string('edition', 20)->nullable();
            $table->string('language', 30)->default('Indonesia');
            $table->smallInteger('pages')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->string('rack_number', 20)->nullable()->comment('Nomor rak lokasi fisik buku');
            $table->smallInteger('total_copies')->unsigned()->default(0)->comment('Auto via trigger');
            $table->integer('total_loans')->unsigned()->default(0)->comment('Counter popularitas');
            $table->timestamps();

            $table->index('category_id');
            $table->fullText(['title', 'author']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
