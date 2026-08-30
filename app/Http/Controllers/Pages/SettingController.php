<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan sistem & notifikasi OTP
     */
    public function index()
    {
        $otpChannel = Setting::get('reset_password_otp_channel', 'email');

        // Status konfigurasi email SMTP
        $mailDriver = config('mail.default');
        $mailHost   = config('mail.mailers.smtp.host');
        $mailFrom   = config('mail.from.address');

        // Status konfigurasi Mekari Qontak
        $mekariToken       = config('services.mekari_qontak.token') ?? env('MEKARI_QONTAK_TOKEN');
        $mekariChannelId   = config('services.mekari_qontak.channel_integration_id') ?? env('MEKARI_QONTAK_CHANNEL_INTEGRATION_ID');
        $mekariTemplateId  = config('services.mekari_qontak.template_id') ?? env('MEKARI_QONTAK_TEMPLATE_ID');

        return view('pages.settings.index', compact(
            'otpChannel',
            'mailDriver',
            'mailHost',
            'mailFrom',
            'mekariToken',
            'mekariChannelId',
            'mekariTemplateId'
        ));
    }

    /**
     * Simpan perubahan saluran OTP khusus reset password
     */
    public function updateOtpChannel(Request $request)
    {
        $request->validate([
            'otp_channel' => 'required|in:email,whatsapp',
        ]);

        Setting::set('reset_password_otp_channel', $request->otp_channel, Auth::id());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Saluran notifikasi OTP Reset Password berhasil diubah ke ' . strtoupper($request->otp_channel) . '.',
                'channel' => $request->otp_channel,
            ]);
        }

        return redirect()->back()->with('success', 'Saluran notifikasi OTP Reset Password berhasil diperbarui menjadi ' . strtoupper($request->otp_channel) . '.');
    }

    /**
     * Kirim email uji coba untuk mengetes konfigurasi SMTP
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            Mail::to($request->email)->send(new \App\Mail\TestSmtpMail());

            return response()->json([
                'status'  => 'success',
                'message' => 'Email uji coba profesional berhasil dikirim ke ' . $request->email,
            ]);
        } catch (\Throwable $th) {
            Log::error('[Test Email] Gagal: ' . $th->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengirim email uji coba: ' . $th->getMessage(),
            ], 500);
        }
    }
}
