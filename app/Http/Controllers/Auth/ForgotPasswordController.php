<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MekariQontakService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Step 1: Cari akun berdasarkan email & kirim OTP via WhatsApp
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Akun dengan email tersebut tidak ditemukan dalam sistem.',
            ], 404);
        }

        if (empty($user->phone)) {
            return response()->json([
                'status'  => false,
                'message' => 'Akun Anda belum memiliki nomor WhatsApp terdaftar. Silakan hubungi Admin untuk bantuan.',
            ], 422);
        }

        // Generate 6 digit angka OTP
        $otp = (string) random_int(100000, 999999);

        // Simpan OTP dan waktu buat di tabel password_reset_tokens
        // Token disimpan dengan format: OTP:PLAIN_OTP|SALT agar mudah diverifikasi dan aman
        $tokenData = $otp;

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => $tokenData,
                'created_at' => Carbon::now(),
            ]
        );

        // Kirim OTP via Mekari Qontak WhatsApp
        $qontak = app(MekariQontakService::class);
        $formattedPhone = MekariQontakService::formatPhone($user->phone);

        $sendResult = $qontak->sendOtpPasswordReset(
            userName:     $user->name,
            userPhone:    $formattedPhone,
            otpCode:      $otp,
            validMinutes: 10
        );

        // Samarkan nomor telepon untuk privasi tampilan UI (contoh: 0857****6642)
        $phoneStr = $user->phone;
        $len = strlen($phoneStr);
        if ($len > 6) {
            $maskedPhone = substr($phoneStr, 0, 4) . str_repeat('*', max(2, $len - 7)) . substr($phoneStr, -3);
        } else {
            $maskedPhone = $phoneStr;
        }

        Log::info('[Password Reset] OTP requested', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'phone'   => $formattedPhone,
        ]);

        return response()->json([
            'status'       => true,
            'message'      => 'Kode OTP berhasil dikirim ke nomor WhatsApp Anda (' . $maskedPhone . ').',
            'masked_phone' => $maskedPhone,
            'email'        => $user->email,
        ]);
    }

    /**
     * Step 2: Verifikasi kode OTP yang dimasukkan user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'otp.required'   => 'Kode OTP wajib diisi.',
            'otp.size'       => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return response()->json([
                'status'  => false,
                'message' => 'Permintaan reset password tidak ditemukan atau sudah kedaluwarsa. Silakan kirim ulang OTP.',
            ], 404);
        }

        // Cek kedaluwarsa (10 menit)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(Carbon::now()) > 10) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'status'  => false,
                'message' => 'Kode OTP telah kedaluwarsa (lebih dari 10 menit). Silakan kirim ulang OTP baru.',
            ], 422);
        }

        // Cocokkan kode OTP
        if ($record->token !== $request->otp) {
            return response()->json([
                'status'  => false,
                'message' => 'Kode OTP yang Anda masukkan salah. Periksa kembali pesan WhatsApp Anda.',
            ], 422);
        }

        // Generate token reset unik yang valid untuk submit password baru
        $resetToken = Str::random(64);
        DB::table('password_reset_tokens')->where('email', $request->email)->update([
            'token'      => 'VERIFIED:' . $resetToken,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'status'      => true,
            'message'     => 'Kode OTP terverifikasi! Silakan buat password baru Anda.',
            'reset_token' => $resetToken,
            'email'       => $request->email,
        ]);
    }

    /**
     * Step 3: Simpan password baru setelah OTP terverifikasi
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'reset_token'           => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || $record->token !== ('VERIFIED:' . $request->reset_token)) {
            return response()->json([
                'status'  => false,
                'message' => 'Sesi reset password tidak valid atau sudah kedaluwarsa. Silakan ulangi proses dari awal.',
            ], 403);
        }

        // Cek kedaluwarsa sesi password (15 menit)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(Carbon::now()) > 15) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'status'  => false,
                'message' => 'Sesi reset password telah kedaluwarsa. Silakan ulangi proses dari awal.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        // Update password baru user
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus token reset dari database
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        Log::info('[Password Reset] Password successfully updated', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Password Anda berhasil diperbarui! Silakan masuk dengan password baru Anda.',
        ]);
    }
}
