<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Login dan dapatkan token Sanctum.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('member.kelas')
        ]);
    }

    /**
     * Register Publik (Otomatis tipe Umum)
     */
    public function register(Request $request, MemberService $memberService)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'anggota',
        ]);

        // Register publik API = selalu umum
        $data['type'] = 'umum';
        $member = $memberService->register($user, $data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil! Akun sedang menunggu verifikasi admin.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('member')
        ], 201);
    }

    /**
     * Dapatkan data user yang login
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('member.kelas')
        ]);
    }

    /**
     * Logout dan hapus semua token saat ini
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout'
        ]);
    }

    /**
     * Cari Siswa/Guru berdasarkan NIS/NIP untuk Klaim Akun
     */
    public function claimLookup(Request $request)
    {
        $request->validate([
            'nis_nip' => 'required|string|max:30',
        ]);

        $member = Member::where('nis_nip', $request->nis_nip)
            ->whereNull('user_id')
            ->first();

        if (!$member) {
            return response()->json([
                'message' => 'NIS/NIP tidak ditemukan atau akun sudah diaktivasi. Hubungi admin.'
            ], 404);
        }

        return response()->json([
            'message' => 'Anggota ditemukan',
            'data' => [
                'id' => $member->id,
                'name' => $member->name,
                'type' => $member->type,
                'nis_nip' => $member->nis_nip,
            ]
        ]);
    }

    /**
     * Aktivasi akun (Set Email + Password) 
     */
    public function claimActivate(Request $request)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $member = Member::where('id', $data['member_id'])->whereNull('user_id')->first();

        if (!$member) {
            return response()->json([
                'message' => 'Anggota tidak valid atau sudah diaktivasi.'
            ], 400);
        }

        $user = User::create([
            'name'     => $member->name,
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'anggota',
        ]);

        $member->update([
            'user_id'     => $user->id,
            'status'      => 'aktif', // Langsung aktif
            'verified_at' => now(),
        ]);

        $user->assignRole('anggota');

        // Buat token langsung login
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Akun berhasil diaktivasi.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('member.kelas')
        ]);
    }
}
