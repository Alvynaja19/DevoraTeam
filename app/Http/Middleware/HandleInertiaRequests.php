<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ] : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
            ],
            // Notif badge: jumlah anggota pending (hanya untuk admin/petugas)
            'pendingCount' => $user && in_array($user->role, ['admin', 'petugas'])
                ? Member::where('status', 'pending')->count()
                : 0,
        ];
    }
}
