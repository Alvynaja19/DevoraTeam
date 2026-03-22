<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show()
    {
        $user   = auth()->user();
        $member = $user->member()
            ->with([
                'kelas',
                'loans' => fn($q) => $q->where('status', 'aktif')
                    ->with('items.copy.book'),
            ])
            ->first();

        return Inertia::render('Anggota/Profile', [
            'member' => $member,
        ]);
    }

    public function update(Request $request)
    {
        $member = auth()->user()->member;

        $data = $request->validate([
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'nis_nip' => 'nullable|string|max:30',
            'photo'   => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('member-photos', 'public');
        }

        $member->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
