<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members');
            $table->tinyInteger('rating')->unsigned()->comment('1 sampai 5 bintang');
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_ratings');
    }
};
