<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Str;

class SiswaImport implements ToCollection, WithHeadingRow
{
    protected $memberService;
    protected $adminId;

    public function __construct($adminId)
    {
        $this->memberService = app(MemberService::class);
        $this->adminId = $adminId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = $row->toArray();
            Log::info('Row import:', $data);
            
            // Skip baris kosong
            if (empty($data['nama']) || empty($data['nis'])) {
                continue;
            }

            // Cari atau buat Kelas
            $classId = null;
            if (!empty($data['kelas'])) {
                $className = trim($data['kelas']);
                
                // Parse grade dari nama kelas (X = 10, XI = 11, XII = 12)
                $grade = 10; // Default 10
                $upperName = strtoupper($className);
                if (str_starts_with($upperName, 'XII')) {
                    $grade = 12;
                } elseif (str_starts_with($upperName, 'XI')) {
                    $grade = 11;
                } elseif (str_starts_with($upperName, 'X')) {
                    $grade = 10;
                } elseif (preg_match('/^(\d+)/', $upperName, $matches)) {
                    $grade = (int) $matches[1];
                }

                $kelas = Kelas::firstOrCreate(
                    ['name' => $className],
                    ['grade' => $grade, 'is_active' => true]
                );
                $classId = $kelas->id;
            }

            // Parse Tanggal Lahir (Excel date or string)
            $tanggalLahir = null;
            if (!empty($data['tanggal_lahir'])) {
                if (is_numeric($data['tanggal_lahir'])) {
                    $tanggalLahir = Date::excelToDateTimeObject($data['tanggal_lahir'])->format('Y-m-d');
                } else {
                    $tanggalLahir = date('Y-m-d', strtotime(str_replace('/', '-', $data['tanggal_lahir'])));
                }
            }

            // Cek existensi berdasarkan NIS
            $member = Member::where('nis_nip', $data['nis'])->where('type', 'siswa')->first();

            $memberData = [
                'type'          => 'siswa',
                'name'          => $data['nama'],
                'nis_nip'       => $data['nis'],
                'class_id'      => $classId,
                'jenis_kelamin' => isset($data['jk']) ? Str::upper($data['jk']) : null,
                'nisn'          => $data['nisn'] ?? null,
                'tempat_lahir'  => $data['tempat_lahir'] ?? null,
                'tanggal_lahir' => $tanggalLahir,
                'nik'           => $data['nik'] ?? null,
                'agama'         => $data['agama'] ?? null,
                'address'       => $data['alamat'] ?? null,
                'phone'         => $data['hp'] ?? null,
            ];

            if ($member) {
                // Update
                $member->update($memberData);
            } else {
                // Create
                $this->memberService->createByAdmin($memberData, $this->adminId);
            }
        }
    }
}
