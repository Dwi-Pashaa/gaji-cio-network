<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditDisbursementService
{
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key', '');
        $this->baseUrl   = config('services.xendit.base_url', 'https://api.xendit.co');
    }

    /**
     * Periksa apakah Xendit sudah dikonfigurasi
     */
    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && $this->secretKey !== 'xnd_development_xxxxxx';
    }

    /**
     * Kirim disbursement (transfer) ke rekening bank karyawan
     *
     * @param  string $externalId          ID unik dari sistem kita (misal: KB-123)
     * @param  string $bankCode            Kode bank Xendit (misal: BCA, BNI, MANDIRI)
     * @param  string $accountNumber       Nomor rekening tujuan
     * @param  string $accountHolderName   Nama pemilik rekening
     * @param  float  $amount              Jumlah transfer (dalam Rupiah)
     * @param  string $description         Keterangan transfer
     * @return array{success: bool, message: string, data: array|null}
     */
    public function sendDisbursement(
        string $externalId,
        string $bankCode,
        string $accountNumber,
        string $accountHolderName,
        float $amount,
        string $description = 'Pembayaran Kasbon Karyawan'
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Xendit belum dikonfigurasi. Silakan isi XENDIT_SECRET_KEY di .env',
                'data'    => null,
            ];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(30)
                ->post("{$this->baseUrl}/disbursements", [
                    'external_id'          => $externalId,
                    'bank_code'            => strtoupper($bankCode),
                    'account_holder_name'  => $accountHolderName,
                    'account_number'       => $accountNumber,
                    'description'          => $description,
                    'amount'               => (int) $amount,
                    'email_to'             => [],
                ]);

            $body = $response->json();

            if ($response->successful() && isset($body['id'])) {
                return [
                    'success'               => true,
                    'message'               => 'Disbursement berhasil dikirim.',
                    'disbursement_id'       => $body['id'],
                    'status'                => $body['status'] ?? 'PENDING',
                    'data'                  => $body,
                ];
            }

            $errorMsg = $body['message'] ?? $body['error_code'] ?? 'Terjadi kesalahan pada Xendit';

            Log::error('[Xendit] Disbursement failed', [
                'external_id' => $externalId,
                'status_code' => $response->status(),
                'body'        => $body,
            ]);

            return [
                'success' => false,
                'message' => 'Xendit Error: ' . $errorMsg,
                'data'    => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[Xendit] sendDisbursement exception', [
                'external_id' => $externalId,
                'error'       => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Xendit: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * Cek status disbursement berdasarkan external_id
     *
     * @param  string $externalId
     * @return array{success: bool, message: string, status: string|null, data: array|null}
     */
    public function getDisbursementStatus(string $externalId): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Xendit belum dikonfigurasi.',
                'status'  => null,
                'data'    => null,
            ];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(15)
                ->get("{$this->baseUrl}/disbursements", [
                    'external_id' => $externalId,
                ]);

            $body = $response->json();

            if ($response->successful() && !empty($body)) {
                $item = is_array($body) && isset($body[0]) ? $body[0] : $body;
                return [
                    'success' => true,
                    'message' => 'Status berhasil diambil.',
                    'status'  => $item['status'] ?? null,
                    'data'    => $item,
                ];
            }

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Data disbursement tidak ditemukan.',
                'status'  => null,
                'data'    => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal cek status Xendit: ' . $e->getMessage(),
                'status'  => null,
                'data'    => null,
            ];
        }
    }

    /**
     * Cek status disbursement berdasarkan Xendit ID (disbursement_id)
     *
     * @param  string $disbursementId
     * @return array{success: bool, message: string, status: string|null, data: array|null}
     */
    public function getDisbursementById(string $disbursementId): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Xendit belum dikonfigurasi.',
                'status'  => null,
                'data'    => null,
            ];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(15)
                ->get("{$this->baseUrl}/disbursements/{$disbursementId}");

            $body = $response->json();

            if ($response->successful() && isset($body['status'])) {
                return [
                    'success' => true,
                    'message' => 'Status berhasil diambil.',
                    'status'  => $body['status'],
                    'data'    => $body,
                ];
            }

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Data disbursement tidak ditemukan.',
                'status'  => null,
                'data'    => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal cek status Xendit: ' . $e->getMessage(),
                'status'  => null,
                'data'    => null,
            ];
        }
    }

    /**
     * Mengambil daftar bank yang didukung langsung dari API Xendit
     * Endpoint: GET https://api.xendit.co/available_disbursements_banks
     * Menggunakan cache 24 jam agar performa cepat dan hemat request.
     */
    public static function availableBanks(): array
    {
        $defaultFallback = [
            'BCA'       => 'Bank Central Asia (BCA)',
            'BNI'       => 'Bank Negara Indonesia (BNI)',
            'BRI'       => 'Bank Rakyat Indonesia (BRI)',
            'MANDIRI'   => 'Bank Mandiri',
            'PERMATA'   => 'Bank Permata',
            'CIMB'      => 'Bank CIMB Niaga',
            'DANAMON'   => 'Bank Danamon',
            'BSI'       => 'Bank Syariah Indonesia (BSI)',
            'BTN'       => 'Bank Tabungan Negara (BTN)',
            'BTPN'      => 'Bank BTPN Jenius',
            'MAYBANK'   => 'Maybank',
            'OCBC'      => 'OCBC NISP',
            'MUAMALAT'  => 'Bank Muamalat',
            'MEGA'      => 'Bank Mega',
            'PANIN'     => 'Bank Panin',
            'BUKOPIN'   => 'Bank Bukopin',
        ];

        $secretKey = config('services.xendit.secret_key');
        if (empty($secretKey)) {
            return $defaultFallback;
        }

        try {
            return \Illuminate\Support\Facades\Cache::remember('xendit_available_disbursements_banks', 86400, function () use ($secretKey, $defaultFallback) {
                $response = Http::withBasicAuth($secretKey, '')
                    ->timeout(10)
                    ->get('https://api.xendit.co/available_disbursements_banks');

                if ($response->successful()) {
                    $banks = $response->json();
                    if (is_array($banks) && !empty($banks)) {
                        $formatted = [];
                        foreach ($banks as $bank) {
                            if (isset($bank['code']) && isset($bank['name'])) {
                                $formatted[$bank['code']] = $bank['name'] . ' (' . $bank['code'] . ')';
                            }
                        }
                        if (!empty($formatted)) {
                            return $formatted;
                        }
                    }
                }

                return $defaultFallback;
            });
        } catch (\Exception $e) {
            Log::warning('[Xendit] Failed to fetch available_disbursements_banks: ' . $e->getMessage());
            return $defaultFallback;
        }
    }

    /**
     * Alias method untuk availableBanks()
     */
    public static function getSupportedBanks(): array
    {
        return self::availableBanks();
    }
}
