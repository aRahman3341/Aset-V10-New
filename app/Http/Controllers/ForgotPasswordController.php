<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    // ── 1. Tampilkan form input email + NIP ──────────────────
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    // ── 2. Validasi email+NIP, kirim OTP ────────────────────
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nip'   => 'required|string|max:12',
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email'    => 'Format email tidak valid.',
            'nip.required'   => 'NIP harus diisi.',
        ]);

        // Cari user berdasarkan email + NIP
        $user = User::where('email', $request->email)
                    ->where('nip',   $request->nip)
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email dan NIP tidak cocok atau tidak terdaftar.'
            ])->withInput();
        }

        // Hapus OTP lama milik user ini
        PasswordResetOtp::where('email', $request->email)
                        ->where('nip',   $request->nip)
                        ->delete();

        // Buat OTP baru 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email'      => $request->email,
            'nip'        => $request->nip,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'used'       => false,
        ]);

        // Kirim email OTP
        Mail::to($request->email)->send(new OtpMail($otp, $user->name));

        // Simpan email+nip di session untuk tahap berikutnya
        session([
            'otp_email' => $request->email,
            'otp_nip'   => $request->nip,
        ]);

        return redirect()->route('password.verify-otp')
            ->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    // ── 3. Tampilkan form verifikasi OTP ────────────────────
    public function showVerifyOtp()
    {
        if (!session('otp_email')) {
            return redirect()->route('password.forgot');
        }
        return view('auth.verify-otp');
    }

    // ── 4. Verifikasi kode OTP ───────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP harus diisi.',
            'otp.size'     => 'Kode OTP harus 6 digit.',
        ]);

        $email = session('otp_email');
        $nip   = session('otp_nip');

        if (!$email || !$nip) {
            return redirect()->route('password.forgot')
                ->withErrors(['otp' => 'Sesi habis. Silakan ulangi.']);
        }

        $record = PasswordResetOtp::where('email', $email)
                                  ->where('nip',   $nip)
                                  ->where('otp',   $request->otp)
                                  ->where('used',  false)
                                  ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if ($record->isExpired()) {
            return back()->withErrors(['otp' => 'Kode OTP sudah expired. Silakan minta ulang.']);
        }

        // Tandai OTP sudah dipakai
        $record->update(['used' => true]);

        // Simpan flag verified di session
        session(['otp_verified' => true]);

        return redirect()->route('password.reset-form');
    }

    // ── 5. Tampilkan form password baru ─────────────────────
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('otp_email')) {
            return redirect()->route('password.forgot');
        }
        return view('auth.reset-password-otp');
    }

    // ── 6. Simpan password baru ──────────────────────────────
    public function resetPassword(Request $request)
    {
        if (!session('otp_verified') || !session('otp_email')) {
            return redirect()->route('password.forgot');
        }

        $request->validate([
            'password' => [
                'required', 'string', 'min:8',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
                'confirmed',
            ],
        ], [
            'password.required'  => 'Password baru harus diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.regex'     => 'Password harus mengandung minimal 1 angka dan 1 simbol.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::where('email', session('otp_email'))
                    ->where('nip',   session('otp_nip'))
                    ->firstOrFail();

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus semua session OTP
        session()->forget(['otp_email', 'otp_nip', 'otp_verified']);

        return redirect()->route('session.formLogin')
            ->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }

    // ── 7. Kirim ulang OTP ───────────────────────────────────
    public function resendOtp()
    {
        $email = session('otp_email');
        $nip   = session('otp_nip');

        if (!$email || !$nip) {
            return redirect()->route('password.forgot');
        }

        $user = User::where('email', $email)
                    ->where('nip',   $nip)
                    ->firstOrFail();

        // Hapus OTP lama
        PasswordResetOtp::where('email', $email)
                        ->where('nip',   $nip)
                        ->delete();

        // Buat OTP baru
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email'      => $email,
            'nip'        => $nip,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'used'       => false,
        ]);

        Mail::to($email)->send(new OtpMail($otp, $user->name));

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}