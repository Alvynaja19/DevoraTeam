<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate(10)->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'   => 'required|string|max:255',
            'status' => 'required|in:siswa,guru,masyarakat umum',
            'nis'    => 'nullable|string|max:20|unique:members',
            'kelas'  => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:500',
            'telepon'=> 'nullable|string|max:20',
        ]);

        Member::create($validated);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function show(Member $member)
    {
        $member->load(['borrowings' => fn($q) => $q->with('book')->latest()->take(10)]);
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'nama'   => 'required|string|max:255',
            'status' => 'required|in:siswa,guru,masyarakat umum',
            'nis'    => 'nullable|string|max:20|unique:members,nis,' . $member->id,
            'kelas'  => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:500',
            'telepon'=> 'nullable|string|max:20',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function destroy(Member $member)
    {
        if ($member->borrowings()->where('status', 'dipinjam')->exists()) {
            return back()->with('error', 'Tidak bisa menghapus anggota yang masih meminjam buku!');
        }

        $member->delete();
        return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus!');
    }
}
