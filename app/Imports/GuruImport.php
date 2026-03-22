<?php

namespace App\Imports;

use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class GuruImport implements ToCollection, WithHeadingRow
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
            
            // Skip baris kosong
            if (empty($data['nama']) || empty($data['nip'])) {
                continue;
            }

            // Cek existensi berdasarkan NIP
            $member = Member::where('nis_nip', $data['nip'])->where('type', 'guru')->first();

            $memberData = [
                'type'             => 'guru',
                'name'             => $data['nama'],
                'nis_nip'          => $data['nip'],
                'pangkat_golongan' => $data['pangkatgolruang'] ?? ($data['pangkat_golongan'] ?? null),
                'jenis_kelamin'    => isset($data['lp']) ? Str::upper($data['lp']) : (isset($data['l_p']) ? Str::upper($data['l_p']) : null),
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
