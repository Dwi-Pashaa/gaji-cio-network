<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MekariQontakService
{
    private string $token;
    private string $channelId;
    private string $baseUrl;
    private string $adminPhone;

    public function __construct()
    {
        $this->token      = config('services.mekari_qontak.token', '');
        $this->channelId  = config('services.mekari_qontak.channel_id', '');
        $this->baseUrl    = rtrim(config('services.mekari_qontak.base_url', 'https://service-chat.qontak.com/api/open/v1'), '/');

        // Ambil nomor WA admin dari settingan database (Companie), fallback ke config/env
        $companySetting   = \App\Models\Companie::latest()->first();
        $dbPhone          = $companySetting?->telp;
        $this->adminPhone = $dbPhone ? self::formatPhone($dbPhone) : config('services.mekari_qontak.admin_phone', '6285324780031');
    }

    /**
     * Periksa apakah Mekari Qontak sudah dikonfigurasi
     */
    public function isConfigured(): bool
    {
        return !empty($this->token) && !empty($this->channelId);
    }

    /**
     * Format nomor telepon ke standar internasional (62xxxx)
     */
    public static function formatPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }
        return $cleaned;
    }

    /**
     * Kirim template WhatsApp via Mekari Qontak
     *
     * @param string $toName       Nama penerima
     * @param string $toNumber     Nomor WhatsApp tujuan (628xxx)
     * @param string $templateId   ID template WhatsApp di Qontak
     * @param array  $bodyParams   Array parameter teks untuk isi body template
     *                             Contoh: ['Toni', 'Rp 200.000', 'BCA', '12345678']
     * @return array ['success' => bool, 'message' => string, 'data' => ?array]
     */
    public function sendTemplateMessage(
        string $toName,
        string $toNumber,
        string $templateId,
        array $bodyParams = []
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Mekari Qontak belum dikonfigurasi (MEKARI_QONTAK_TOKEN atau CHANNEL_ID kosong).',
                'data'    => null,
            ];
        }

        $formattedPhone = self::formatPhone($toNumber);
        if (!$formattedPhone) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp penerima tidak valid.',
                'data'    => null,
            ];
        }

        // Format parameters body untuk Qontak Direct Broadcast:
        // 'key'        => '1' (nomor urut variabel)
        // 'value'      => 'param_1' (nama variabel identifier: 2-16 karakter, lowercase/number/_)
        // 'value_text' => nilai teks yang dikirimkan (contoh: 'Toni Holidin')
        $parametersBody = [];
        foreach ($bodyParams as $index => $value) {
            $num = $index + 1;
            $parametersBody[] = [
                'key'        => (string) $num,
                'value'      => 'param_' . $num,
                'value_text' => (string) $value,
            ];
        }

        $payload = [
            'to_name'                => $toName,
            'to_number'              => $formattedPhone,
            'message_template_id'    => $templateId,
            'channel_integration_id' => $this->channelId,
            'language'               => [
                'code' => 'id',
            ],
            'parameters'             => [
                'body' => $parametersBody,
            ],
        ];

        try {
            $response = Http::withToken($this->token)
                ->timeout(15)
                ->post("{$this->baseUrl}/broadcasts/whatsapp/direct", $payload);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? '') === 'success') {
                Log::info('[Mekari Qontak] WhatsApp sent successfully', [
                    'to'          => $formattedPhone,
                    'template_id' => $templateId,
                    'response'    => $body,
                ]);

                return [
                    'success' => true,
                    'message' => 'Pesan WhatsApp berhasil dikirim via Mekari Qontak.',
                    'data'    => $body,
                ];
            }

            Log::error('[Mekari Qontak] Send failed', [
                'to'          => $formattedPhone,
                'status_code' => $response->status(),
                'response'    => $body,
            ]);

            return [
                'success' => false,
                'message' => 'Gagal kirim via Qontak: ' . ($body['error']['message'] ?? $body['message'] ?? $response->body()),
                'data'    => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[Mekari Qontak] Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Koneksi ke Mekari Qontak gagal: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * Kirim notifikasi pengajuan kasbon baru ke Admin / HRD
     */
    public function notifyAdminNewCashAdvance(
        string $employeeName,
        string $title,
        float $amount,
        string $bankName,
        string $accountNumber,
        string $dateStr
    ): array {
        $templateId = config('services.mekari_qontak.template_id_pengajuan');
        if (!$templateId) {
            return ['success' => false, 'message' => 'Template ID pengajuan belum diisi di .env'];
        }

        return $this->sendTemplateMessage(
            toName: 'Admin Keuangan',
            toNumber: $this->adminPhone,
            templateId: $templateId,
            bodyParams: [
                $employeeName,
                $title,
                'Rp ' . number_format($amount, 0, ',', '.'),
                "{$bankName} - {$accountNumber}",
                $dateStr,
            ]
        );
    }

    /**
     * Kirim notifikasi konfirmasi approval kasbon ke Karyawan
     */
    public function notifyEmployeeApproval(
        string $employeeName,
        string $employeePhone,
        string $title,
        float $amount,
        string $bankName,
        string $accountNumber,
        string $accountHolderName,
        string $dateStr,
        bool $transferred = true,
        ?string $invoiceUrl = null
    ): array {
        $templateId = config('services.mekari_qontak.template_id_approval');
        if (!$templateId) {
            return ['success' => false, 'message' => 'Template ID approval belum diisi di .env'];
        }

        $params = [
            $employeeName,
            $title,
            'Rp ' . number_format($amount, 0, ',', '.'),
            "{$bankName} ({$accountNumber} a/n {$accountHolderName})",
            $transferred ? 'Disetujui & Berhasil Ditransfer' : 'Disetujui',
            $dateStr,
        ];

        if ($invoiceUrl) {
            $params[] = $invoiceUrl;
        }

        return $this->sendTemplateMessage(
            toName: $employeeName,
            toNumber: $employeePhone,
            templateId: $templateId,
            bodyParams: $params
        );
    }

    /**
     * Kirim notifikasi transfer gaji karyawan via Mekari Qontak
     * Menggunakan template khusus gaji: MEKARI_QONTAK_TEMPLATE_ID_GAJI
     */
    public function notifySalaryTransfer(
        string $employeeName,
        string $employeePhone,
        string $monthYearStr,
        float $baseSalary,
        float $totalAllowance,
        float $totalCashAdvance,
        float $netSalary,
        string $bankName,
        string $accountNumber,
        string $accountHolderName,
        string $dateStr
    ): array {
        $templateId = config('services.mekari_qontak.template_id_gaji');
        if (!$templateId) {
            Log::info('[Mekari Qontak] Template ID Gaji (MEKARI_QONTAK_TEMPLATE_ID_GAJI) belum diatur di .env. Notifikasi WA gaji dilewati.');
            return ['success' => false, 'message' => 'Template ID Gaji belum diatur di .env'];
        }

        $adminFeeDeduction = ($baseSalary + $totalAllowance) - $totalCashAdvance - $netSalary;
        $breakdownDetail   = "Gaji Pokok: Rp " . number_format($baseSalary, 0, ',', '.') . " | Tunjangan: +Rp " . number_format($totalAllowance, 0, ',', '.') . " | Potongan Kasbon: -Rp " . number_format($totalCashAdvance, 0, ',', '.');
        if ($adminFeeDeduction > 0) {
            $breakdownDetail .= " | Potongan Admin: -Rp " . number_format($adminFeeDeduction, 0, ',', '.');
        }

        $params = [
            $employeeName,
            "Gaji Bulan {$monthYearStr}",
            'Rp ' . number_format($netSalary, 0, ',', '.'),
            "{$bankName} ({$accountNumber} a/n {$accountHolderName})",
            $breakdownDetail,
            $dateStr,
        ];

        return $this->sendTemplateMessage(
            toName: $employeeName,
            toNumber: $employeePhone,
            templateId: $templateId,
            bodyParams: $params
        );
    }

    /**
     * Kirim kode OTP Reset Password via WhatsApp Mekari Qontak
     *
     * @param string $userName      Nama pengguna
     * @param string $userPhone     Nomor WhatsApp pengguna
     * @param string $otpCode       Kode OTP 6 digit
     * @param int    $validMinutes  Masa berlaku dalam menit
     * @return array
     */
    public function sendOtpPasswordReset(
        string $userName,
        string $userPhone,
        string $otpCode,
        int $validMinutes = 10
    ): array {
        $templateId = config('services.mekari_qontak.template_id_otp');

        // Jika ada template OTP khusus di Mekari Qontak
        if ($templateId) {
            return $this->sendTemplateMessage(
                toName: $userName,
                toNumber: $userPhone,
                templateId: $templateId,
                bodyParams: [
                    $userName,
                    $otpCode,
                    (string) $validMinutes,
                ]
            );
        }

        // Fallback: Jika belum ada template OTP khusus, coba kirim atau log info
        Log::info('[Mekari Qontak] OTP Generated for User', [
            'name'          => $userName,
            'phone'         => $userPhone,
            'otp'           => $otpCode,
            'valid_minutes' => $validMinutes,
        ]);

        return [
            'success' => true,
            'message' => 'Kode OTP berhasil diproses.',
            'data'    => [
                'otp_simulated' => $otpCode,
            ]
        ];
    }
}

