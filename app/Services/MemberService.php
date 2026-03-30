<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MemberService
{
    /**
     * Generate kode anggota unik: SIS-2025-001 / GRU-2025-001 / UMU-2025-001
     */
    public function generateMemberCode(string $type): string
    {
        $prefix = match ($type) {
            'siswa' => 'SIS',
            'guru'  => 'GRU',
            default => 'UMU',
        };
        $year  = now()->year;
        $count = Member::whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%s-%03d', $prefix, $year, $count);
    }

    /**
     * Generate QR token UUID v4 yang unik.
     */
    public function generateQrToken(): string
    {
        do {
            $token = (string) Str::uuid();
        } while (Member::where('qr_token', $token)->exists());

        return $token;
    }

    /**
     * Registrasi anggota baru (status langsung aktif).
     */
    public function register(User $user, array $data): Member
    {
        return DB::transaction(function () use ($user, $data) {
            $member = Member::create([
                'user_id'     => $user->id,
                'class_id'    => $data['class_id'] ?? null,
                'member_code' => $this->generateMemberCode($data['type']),
                'qr_token'    => $this->generateQrToken(),
                'name'        => $data['name'],
                'type'        => $data['type'],
                'nis_nip'     => $data['nis_nip'] ?? null,
                'phone'       => $data['phone'] ?? null,
                'address'     => $data['address'] ?? null,
                'status'      => 'aktif',
                'verified_at' => now(),
                'expired_at'  => now()->addYear(),
            ]);

            $user->assignRole('anggota');

            return $member;
        });
    }

    /**
     * Buat anggota baru oleh admin (siswa/guru), tanpa User, status langsung aktif.
     */
    public function createByAdmin(array $data, ?int $adminId = null): Member
    {
        return Member::create([
            'class_id'         => $data['class_id'] ?? null,
            'member_code'      => $this->generateMemberCode($data['type']),
            'qr_token'         => $this->generateQrToken(),
            'name'             => $data['name'],
            'type'             => $data['type'],
            'nis_nip'          => $data['nis_nip'] ?? null,
            'phone'            => $data['phone'] ?? null,
            'address'          => $data['address'] ?? null,
            'status'           => 'aktif', // langsung aktif
            'verified_at'      => now(),
            'verified_by'      => $adminId,
            'expired_at'       => now()->addYear(),
            // Extra fields
            'nisn'             => $data['nisn'] ?? null,
            'nik'              => $data['nik'] ?? null,
            'tempat_lahir'     => $data['tempat_lahir'] ?? null,
            'tanggal_lahir'    => $data['tanggal_lahir'] ?? null,
            'agama'            => $data['agama'] ?? null,
            'jenis_kelamin'    => $data['jenis_kelamin'] ?? null,
            'pangkat_golongan' => $data['pangkat_golongan'] ?? null,
        ]);
    }

    /**
     * Approve anggota pending.
     */
    public function approve(Member $member, User $approvedBy): Member
    {
        $member->update([
            'status'      => 'aktif',
            'verified_at' => now(),
            'verified_by' => $approvedBy->id,
            'expired_at'  => now()->addYear(),
        ]);

        return $member;
    }

    /**
     * Tolak anggota pending.
     */
    public function reject(Member $member, string $reason, User $rejectedBy): Member
    {
        $member->update([
            'status'           => 'ditolak',
            'rejection_reason' => $reason,
            'verified_by'      => $rejectedBy->id,
            'verified_at'      => now(),
        ]);

        return $member;
    }

    /**
     * Suspend anggota aktif.
     */
    public function suspend(Member $member, string $reason): Member
    {
        $member->update([
            'status'         => 'suspended',
            'suspend_reason' => $reason,
        ]);

        return $member;
    }

    /**
     * Aktifkan kembali anggota yang suspended/nonaktif.
     */
    public function activate(Member $member): Member
    {
        $member->update(['status' => 'aktif', 'suspend_reason' => null]);
        return $member;
    }
}
