<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::withCount('members');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = $request->get('per_page', 10);
        $classes = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Kelas/Index', [
            'classes' => $classes,
            'filters' => $request->only('search', 'per_page'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:50|unique:classes,name',
            'grade'     => 'nullable|string|max:10',
            'major'     => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data['grade'] = $data['grade'] ?? 0;
        $data['major'] = $data['major'] ?? '';

        Kelas::create($data);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kela)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:50|unique:classes,name,' . $kela->id,
            'grade'     => 'nullable|string|max:10',
            'major'     => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data['grade'] = $data['grade'] ?? 0;
        $data['major'] = $data['major'] ?? '';

        $kela->update($data);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        if ($kela->members()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki anggota.');
        }

        $kela->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
