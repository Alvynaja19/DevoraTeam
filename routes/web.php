<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;
use App\Models\Book;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Redirect root to dashboard or login
Route::get('/', function () {
    $books = Book::latest()->take(4)->get(); // Show 4 books in Koleksi Unggulan
    $totalBooks = Book::sum('jumlah_buku');
    $totalMembers = \App\Models\Member::count();
    $activeBorrowings = \App\Models\Borrowing::where('status', 'dipinjam')->count();
    
    return view('welcome', compact('books', 'totalBooks', 'totalMembers', 'activeBorrowings'));
});
// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Reset Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Rute Profil khusus Member
    Route::middleware('role:member')->group(function () {
        Route::get('/my-profile', [\App\Http\Controllers\MemberProfileController::class, 'index'])->name('member.profile');
    });
    
    // Rute yang hanya boleh diakses oleh Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Scan Peminjaman
        Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
        Route::get('/scan/member', [ScanController::class, 'lookupMember'])->name('scan.member');
        Route::get('/scan/book', [ScanController::class, 'lookupBook'])->name('scan.book');
        Route::post('/scan/borrow', [ScanController::class, 'borrow'])->name('scan.borrow');

        // Pengembalian
        Route::get('/return', [ScanController::class, 'returnIndex'])->name('return.index');
        Route::get('/return/lookup', [ScanController::class, 'lookupReturnQr'])->name('return.lookup');
        Route::post('/return/process', [ScanController::class, 'returnBook'])->name('return.process');

        // Books
        Route::post('/books/import', [BookController::class, 'import'])->name('books.import');
        Route::resource('books', BookController::class);

        // Members
        Route::resource('members', MemberController::class);

        // Borrowings
        Route::resource('borrowings', BorrowingController::class)->only(['index', 'create', 'store']);
        Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');
    });
});
