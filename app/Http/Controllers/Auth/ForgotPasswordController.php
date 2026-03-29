<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }

        $otp = (string) random_int(100000, 999999);

        // Save hashed OTP into password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        // Send Email
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return back()->with('success', 'Kode OTP telah berhasil dikirim ke email Anda. Silakan periksa kotak masuk atau folder spam.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->otp, $resetRecord->token)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluwarsa.']);
        }

        // Check expiration (10 minutes)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluwarsa. Silakan minta kode OTP baru.']);
        }

        // Generate a temporary reset_token for the actual reset step to ensure security
        $resetToken = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($resetToken),
                'created_at' => now(),
            ]
        );

        // Return the plain resetToken in session to the view
        return back()->with([
            'success'     => 'OTP Valid!',
            'reset_token' => $resetToken
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'reset_token' => 'required|string',
            'password'    => ['required', 'confirmed', 'string', 'min:6'],
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->reset_token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Permintaan reset password tidak valid. Ulangi proses OTP dari awal.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email pengguna tidak ditemukan.']);
        }

        // Update with new password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Cleanup tokens
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Check if role allows auto login, members to home, admin/petugas to dashboard
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Password Anda berhasil diperbarui. Anda telah masuk.');
    }
}
