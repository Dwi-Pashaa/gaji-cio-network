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
     * Tampilkan halaman pengaturan sistem & notifikasi (OTP, Kasbon, Gaji)
     */
    public function index()
    {
        $otpChannel             = Setting::get('reset_password_otp_channel', 'email');
        $notifyCashAdvanceEmail = Setting::get('notify_cash_advance_email', '1');
        $notifyCashAdvanceWa    = Setting::get('notify_cash_advance_wa', '1');
        $notifySalaryEmail      = Setting::get('notify_salary_email', '1');
        $notifySalaryWa         = Setting::get('notify_salary_wa', '1');
        $adminNotificationEmail = Setting::get('admin_notification_email', config('mail.from.address', 'support@cionetwork.id'));

        // Pengaturan Kasbon & Biaya Admin Transfer
        $maxCashAdvance = Setting::get('max_cash_advance_amount', '5000000');
        $adminFee       = Setting::get('admin_fee_disbursement', '2500');

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
            'notifyCashAdvanceEmail',
            'notifyCashAdvanceWa',
            'notifySalaryEmail',
            'notifySalaryWa',
            'adminNotificationEmail',
            'maxCashAdvance',
            'adminFee',
            'mailDriver',
            'mailHost',
            'mailFrom',
            'mekariToken',
            'mekariChannelId',
            'mekariTemplateId'
        ));
    }

    /**
     * Simpan batas maksimal pengajuan kasbon dan biaya admin transfer
     */
    public function updateFinanceSettings(Request $request)
    {
        $rawMaxCashAdvance = $request->input('max_cash_advance_amount', '');
        $rawAdminFee       = $request->input('admin_fee_disbursement', '');

        $maxCashAdvance = preg_replace('/[^0-9]/', '', (string) $rawMaxCashAdvance);
        $adminFee       = preg_replace('/[^0-9]/', '', (string) $rawAdminFee);

        Setting::set('max_cash_advance_amount', $maxCashAdvance ?: '0', Auth::id());
        Setting::set('admin_fee_disbursement', $adminFee ?: '0', Auth::id());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Pengaturan batas maksimal kasbon dan biaya admin transfer berhasil disimpan.',
                'data'    => [
                    'max_cash_advance_amount' => (float) $maxCashAdvance,
                    'admin_fee_disbursement'  => (float) $adminFee,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan kasbon dan biaya admin berhasil disimpan.');
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
     * Simpan seluruh pengaturan notifikasi (Kasbon & Gaji)
     */
    public function updateNotificationSettings(Request $request)
    {
        $request->validate([
            'admin_notification_email'   => 'nullable|email',
            'notify_cash_advance_email'  => 'nullable|in:0,1',
            'notify_cash_advance_wa'     => 'nullable|in:0,1',
            'notify_salary_email'        => 'nullable|in:0,1',
            'notify_salary_wa'           => 'nullable|in:0,1',
        ]);

        Setting::set('notify_cash_advance_email', $request->input('notify_cash_advance_email', '0'), Auth::id());
        Setting::set('notify_cash_advance_wa', $request->input('notify_cash_advance_wa', '0'), Auth::id());
        Setting::set('notify_salary_email', $request->input('notify_salary_email', '0'), Auth::id());
        Setting::set('notify_salary_wa', $request->input('notify_salary_wa', '0'), Auth::id());

        if ($request->has('admin_notification_email')) {
            Setting::set('admin_notification_email', $request->admin_notification_email, Auth::id());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Pengaturan notifikasi Kasbon dan Gaji berhasil diperbarui.',
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
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
