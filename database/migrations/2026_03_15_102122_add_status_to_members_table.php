<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('status')->default('siswa')->after('nama'); // siswa, guru, masyarakat umum
            $table->string('nis')->nullable()->change();
            $table->string('kelas')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('nis')->nullable(false)->change();
            $table->string('kelas')->nullable(false)->change();
        });
    }
};
