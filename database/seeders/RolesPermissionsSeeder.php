<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissions ──────────────────────────────────────────────
        $permissions = [
            // Members
            'member.view', 'member.approve', 'member.reject',
            'member.suspend', 'member.delete',
            // Books
            'book.view', 'book.create', 'book.edit', 'book.delete',
            // Loans
            'loan.view', 'loan.create', 'loan.extend', 'loan.force_close',
            // Returns
            'return.process',
            // Fines
            'fine.view', 'fine.pay', 'fine.free',
            // Reports
            'report.view', 'report.export',
            // Settings
            'setting.view', 'setting.edit',
            // Activity Logs
            'log.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ─── Roles ────────────────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $petugas = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        $petugas->syncPermissions([
            'member.view', 'member.approve', 'member.reject', 'member.suspend',
            'book.view', 'book.create', 'book.edit',
            'loan.view', 'loan.create', 'loan.extend',
            'return.process',
            'fine.view', 'fine.pay',
            'report.view', 'report.export',
        ]);

        Role::firstOrCreate(['name' => 'anggota', 'guard_name' => 'web']);

        // ─── Default Admin ────────────────────────────────────────────
        $adminUser = User::firstOrCreate(
        ['email' => 'admin@gmail.com'],
        [
            'name' => 'Administrator',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]
        );
        $adminUser->assignRole('admin');

        // Default Petugas
        $petugasUser = User::firstOrCreate(
        ['email' => 'petugas@gmail.com'],
        [
            'name' => 'Petugas Perpustakaan',
            'password' => Hash::make('petugas123'),
            'role' => 'petugas',
        ]
        );
        $petugasUser->assignRole('petugas');

        $this->command->info('✅ Roles, permissions, dan akun default berhasil dibuat.');
    }
}