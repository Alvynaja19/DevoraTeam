<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Member;
use App\Models\User;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $redirect = $user->role === 'anggota'
            ? route('home')
            : route('dashboard');

        return redirect()->intended($redirect)->with('success', 'Login berhasil! Selamat datang.');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request, MemberService $memberService)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', 'string', 'min:6'],
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'anggota',
        ]);

        // Register publik = selalu umum
        $data['type'] = 'umum';
        $memberService->register($user, $data);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registrasi berhasil! Selamat datang di perpustakaan.');
    }

    // ── Klaim Akun (NIS/NIP) ──

    public function showClaim()
    {
        return Inertia::render('Auth/ClaimAccount');
    }

    public function claimLookup(Request $request)
    {
        $request->validate(['nis_nip' => 'required|string|max:30']);

        $member = Member::where('nis_nip', $request->nis_nip)
            ->whereNull('user_id')
            ->first();

        if (!$member) {
            return back()->withErrors(['nis_nip' => 'NIS/NIP tidak ditemukan atau akun sudah diaktivasi. Hubungi admin.']);
        }

        return Inertia::render('Auth/ClaimAccount', [
            'foundMember' => [
                'id'   => $member->id,
                'name' => $member->name,
                'type' => $member->type,
                'nis_nip' => $member->nis_nip,
            ],
        ]);
    }

    public function claimActivate(Request $request)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', 'confirmed', 'string', 'min:6'],
        ]);

        $member = Member::where('id', $data['member_id'])->whereNull('user_id')->firstOrFail();

        $user = User::create([
            'name'     => $member->name,
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'anggota',
        ]);

        $member->update([
            'user_id'     => $user->id,
            'status'      => 'aktif',
            'verified_at' => now(),
        ]);

        $user->assignRole('anggota');
        Auth::login($user);

        return redirect()->route('home')->with('success', "Selamat datang, {$member->name}! Akun berhasil diaktivasi.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
