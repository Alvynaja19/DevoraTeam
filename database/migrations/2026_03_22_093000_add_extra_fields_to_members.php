<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Make user_id nullable (admin-imported members have no User yet)
            $table->foreignId('user_id')->nullable()->change();

            // Extra fields for siswa
            $table->string('nisn', 20)->nullable()->after('nis_nip');
            $table->string('nik', 20)->nullable()->after('nisn');
            $table->string('tempat_lahir', 100)->nullable()->after('nik');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama', 30)->nullable()->after('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('agama');

            // Extra fields for guru
            $table->string('pangkat_golongan', 100)->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir',
                'agama', 'jenis_kelamin', 'pangkat_golongan',
            ]);
        });
    }
};
