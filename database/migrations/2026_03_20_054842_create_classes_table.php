<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->comment('cth: XI IPA 1');
            $table->tinyInteger('grade')->unsigned()->comment('10, 11, atau 12');
            $table->string('major', 50)->nullable()->comment('IPA, IPS, Bahasa, dll');
            $table->boolean('is_active')->default(true)->comment('false = kelas sudah tidak aktif');
            $table->timestamps();

            $table->index('grade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
