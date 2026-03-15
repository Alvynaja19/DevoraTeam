<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberProfileController extends Controller
{
    public function index()
    {
        // Ambil data user yang sedang login beserta relasi member-nya
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            // Jika tidak ada member padahal role-nya member (fallback case)
            return redirect('/')->with('error', 'Profil anggota Anda belum dibuat.');
        }

        // Ambil riwayat peminjaman member ini
        $borrowings = $member->borrowings()->with('book')->latest()->get();

        return view('member-profile.index', compact('member', 'borrowings'));
    }
}
