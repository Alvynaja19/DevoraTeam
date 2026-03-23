<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\FineController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Anggota\ProfileController;

use App\Http\Controllers\Admin\KelasController;


// ─── Public Routes (no auth) ───────────────────────────────────────────────
Route::get('/', [PublicController::class , 'home'])->name('home');
Route::get('/katalog', [PublicController::class , 'catalog'])->name('catalog');

// ─── Guest Routes ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class , 'login']);
    Route::get('/register', [AuthController::class , 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class , 'register']);

    // Klaim Akun (NIS/NIP first-login)
    Route::get('/claim-account', [AuthController::class , 'showClaim'])->name('claim.show');
    Route::post('/claim-account/lookup', [AuthController::class , 'claimLookup'])->name('claim.lookup');
    Route::post('/claim-account/activate', [AuthController::class , 'claimActivate'])->name('claim.activate');
});

// ─── Auth Routes ──────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

    Route::middleware('role:anggota')->prefix('anggota')->name('anggota.')->group(function () {
            Route::get('/profile', [ProfileController::class , 'show'])->name('profile');
            Route::put('/profile', [ProfileController::class , 'update'])->name('profile.update');
        }
        );

        // ── Admin & Petugas ──────────────────────────────────────────────────
        Route::middleware('role:admin|petugas')->group(function () {

            // Members
            Route::get('/members/create', [MemberController::class , 'create'])->name('members.create');
            Route::post('/members', [MemberController::class , 'store'])->name('members.store');
            Route::post('/members/import', [MemberController::class , 'import'])->name('members.import');
            Route::get('/members/template/{type}', [MemberController::class , 'downloadTemplate'])->name('members.template');
            Route::get('/members', [MemberController::class , 'index'])->name('members.index');
            Route::get('/members/{member}', [MemberController::class , 'show'])->name('members.show');
            Route::post('/members/{member}/approve', [MemberController::class , 'approve'])->name('members.approve');
            Route::post('/members/{member}/reject', [MemberController::class , 'reject'])->name('members.reject');
            Route::post('/members/{member}/suspend', [MemberController::class , 'suspend'])->name('members.suspend');
            Route::post('/members/{member}/activate', [MemberController::class , 'activate'])->name('members.activate');
            Route::put('/members/{member}', [MemberController::class , 'update'])->name('members.update');
            Route::delete('/members/{member}', [MemberController::class , 'destroy'])->name('members.destroy');

            // Books
            Route::post('/books/import', [BookController::class , 'import'])->name('books.import');
            Route::get('/books/{book}/detail', [BookController::class , 'detail'])->name('books.detail');
            Route::resource('books', BookController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::post('/books/{book}/copies', [BookController::class , 'storeCopy'])->name('books.copies.store');
            Route::put('/copies/{copy}', [BookController::class , 'updateCopy'])->name('copies.update');

            // Sirkulasi
            Route::get('/peminjaman', fn() => \Inertia\Inertia::render('Admin/Sirkulasi/Index', ['page' => 'peminjaman']))->name('sirkulasi.peminjaman');
            Route::get('/pengembalian', fn() => \Inertia\Inertia::render('Admin/Sirkulasi/Index', ['page' => 'pengembalian']))->name('sirkulasi.pengembalian');
            Route::get('/riwayat', [LoanController::class , 'riwayat'])->name('sirkulasi.riwayat');

            // Loans API
            Route::post('/loans', [LoanController::class , 'store'])->name('loans.store');
            Route::post('/loans/{loan}/extend', [LoanController::class , 'extend'])->name('loans.extend');

            // Scan helpers (JSON)
            Route::post('/loans/validate-member', [LoanController::class , 'validateMember'])->name('loans.validate-member');
            Route::post('/loans/validate-book', [LoanController::class , 'validateBook'])->name('loans.validate-book');

            // Returns API
            Route::post('/returns/check', [ReturnController::class , 'check'])->name('returns.check');
            Route::post('/returns', [ReturnController::class , 'store'])->name('returns.store');

            // Fines
            Route::get('/fines', [FineController::class , 'index'])->name('fines.index');
            Route::post('/fines/{fine}/pay', [FineController::class , 'pay'])->name('fines.pay');
            Route::post('/fines/{fine}/free', [FineController::class , 'free'])->name('fines.free');

            // Kelas
            Route::resource('kelas', KelasController::class)->only(['index', 'store', 'update', 'destroy']);

            // Settings (admin only)
            Route::middleware('role:admin')->group(function () {
                    Route::get('/settings', [SettingController::class , 'index'])->name('settings.index');
                    Route::post('/settings', [SettingController::class , 'update'])->name('settings.update');
                }
                );
            }
            );
        });