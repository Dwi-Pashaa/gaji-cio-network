<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FinanceApiService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $keyId;
    protected string $secret;
    protected string $clientName;

    public function __construct()
    {
        $rawUrl = (string) Config::get('services.finance.url', 'http://127.0.0.1:8000');
        // Pastikan tidak ada trailing slash atau akhiran /api/v1 ganda
        $cleanUrl = preg_replace('/\/api\/v1\/?$/', '', rtrim($rawUrl, '/'));
        $this->baseUrl = $cleanUrl;
        $this->clientId = (string) Config::get('services.finance.client_id', '');
        $this->keyId = (string) Config::get('services.finance.key_id', '');
        $this->secret = (string) Config::get('services.finance.secret', '');
        $this->clientName = (string) Config::get('services.finance.client_name', 'Web CIO Keuangan');
    }

    /**
     * Hitung HMAC SHA-256 Signature Base64 sesuai spesifikasi API Finance.
     */
    public function generateSignature(string $method, string $pathWithQuery, string $timestamp, string $nonce, string $bodyContent = ''): string
    {
        $bodyHash = hash('sha256', $bodyContent);
        $canonical = implode("\n", [
            strtoupper($method),
            '/' . ltrim($pathWithQuery, '/'),
            $this->clientId,
            $this->keyId,
            $timestamp,
            $nonce,
            $bodyHash
        ]);

        return base64_encode(hash_hmac('sha256', $canonical, $this->secret, true));
    }

    /**
     * Buat HTTP headers lengkap untuk request HMAC.
     */
    public function buildHeaders(string $method, string $pathWithQuery, string $bodyContent = ''): array
    {
        $timestamp = (string) time();
        $nonce = 'req-' . Str::random(16) . '-' . $timestamp;
        $signature = $this->generateSignature($method, $pathWithQuery, $timestamp, $nonce, $bodyContent);

        return [
            'X-Client-ID' => $this->clientId,
            'X-Key-ID' => $this->keyId,
            'X-Timestamp' => $timestamp,
            'X-Nonce' => $nonce,
            'X-Signature' => $signature,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Cek apakah konfigurasi API Finance sudah terisi lengkap.
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->keyId) && !empty($this->secret) && !empty($this->baseUrl);
    }

    /**
     * Ambil jumlah saldo website dari API Web Finance (GET /api/v1/balance).
     *
     * @return array ['success' => bool, 'balance' => float, 'message' => string, 'data' => ?array]
     */
    public function getBalance(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'balance' => 0.0,
                'message' => 'Konfigurasi API Finance belum lengkap di .env (FINANCE_CLIENT_ID, FINANCE_KEY_ID, FINANCE_CLIENT_SECRET).',
                'data' => null,
            ];
        }

        $path = '/api/v1/balance';
        try {
            $headers = $this->buildHeaders('GET', $path, '');
            $url = $this->baseUrl . $path;

            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->get($url);

            if ($response->successful()) {
                $json = $response->json();
                $balanceStr = $json['data']['balance'] ?? '0';
                $balanceVal = (float) $balanceStr;
                return [
                    'success' => true,
                    'balance' => $balanceVal,
                    'message' => 'Berhasil mengambil saldo website.',
                    'data' => $json['data'] ?? [],
                ];
            }

            Log::error('Finance API getBalance Error: ' . $response->body());

            return [
                'success' => false,
                'balance' => 0.0,
                'message' => 'Gagal mengambil saldo dari API Finance: ' . ($response->json('message') ?? $response->status()),
                'data' => null,
            ];
        } catch (Exception $e) {
            Log::error('Finance API getBalance Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'balance' => 0.0,
                'message' => 'Koneksi ke API Finance gagal: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Catat riwayat pengeluaran (POST /api/v1/history).
     */
    public function recordExpense(
        string $subjectExternalId,
        float $amount,
        string $description,
        string $category = 'pengeluaran',
        string $note = ''
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi API Finance belum lengkap.',
            ];
        }

        $path = '/api/v1/history';
        $payload = [
            'event' => 'created',
            'subject_type' => 'Expense',
            'subject_external_id' => $subjectExternalId,
            'description' => $description,
            'properties' => [
                'client_name' => $this->clientName,
                'amount' => $amount,
                'category' => $category,
                'note' => $note,
            ]
        ];

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        try {
            $headers = $this->buildHeaders('POST', $path, $jsonPayload);
            $url = $this->baseUrl . $path;

            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->withBody($jsonPayload, 'application/json')
                ->post($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Riwayat pengeluaran berhasil dikirim ke Finance API.',
                    'data' => $response->json(),
                ];
            }

            Log::error('Finance API recordExpense Error: ' . $response->body());

            return [
                'success' => false,
                'message' => 'Gagal mengirim history pengeluaran ke Finance: ' . ($response->json('message') ?? $response->status()),
            ];
        } catch (Exception $e) {
            Log::error('Finance API recordExpense Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Koneksi ke Finance API gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Catat riwayat pemasukan (POST /api/v1/history).
     */
    public function recordIncome(
        string $subjectExternalId,
        float $amount,
        string $description,
        string $source = 'Manual'
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi API Finance belum lengkap.',
            ];
        }

        $path = '/api/v1/history';
        $payload = [
            'event' => 'created',
            'subject_type' => 'Income',
            'subject_external_id' => $subjectExternalId,
            'description' => $description,
            'properties' => [
                'client_name' => $this->clientName,
                'amount' => $amount,
                'source' => $source,
            ]
        ];

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        try {
            $headers = $this->buildHeaders('POST', $path, $jsonPayload);
            $url = $this->baseUrl . $path;

            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->withBody($jsonPayload, 'application/json')
                ->post($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Riwayat pemasukan berhasil dikirim ke Finance API.',
                    'data' => $response->json(),
                ];
            }

            Log::error('Finance API recordIncome Error: ' . $response->body());

            return [
                'success' => false,
                'message' => 'Gagal mengirim history pemasukan ke Finance: ' . ($response->json('message') ?? $response->status()),
            ];
        } catch (Exception $e) {
            Log::error('Finance API recordIncome Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Koneksi ke Finance API gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Memotong saldo langsung di API Finance (POST /api/v1/balance/deduct).
     *
     * @param float $amount
     * @param string $referenceId
     * @param string $description
     * @param string $category
     * @param string $note
     * @return array ['success' => bool, 'message' => string, 'data' => ?array]
     */
    public function deductBalance(
        float $amount,
        string $referenceId,
        string $description,
        string $category = 'kasbon',
        string $note = ''
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi API Finance belum lengkap.',
                'data' => null,
            ];
        }

        $path = '/api/v1/balance/deduct';
        $payload = [
            'amount' => (float) $amount,
            'reference_id' => $referenceId,
            'description' => $description,
            'category' => $category,
            'note' => $note,
        ];

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        try {
            $headers = $this->buildHeaders('POST', $path, $jsonPayload);
            $url = $this->baseUrl . $path;

            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->withBody($jsonPayload, 'application/json')
                ->post($url);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'message' => 'Saldo website berhasil dipotong.',
                    'data' => $body['data'] ?? [],
                ];
            }

            // Jika endpoint /balance/deduct belum tersedia (404), fallback ke recordExpense
            if ($response->status() === 404) {
                Log::info('Endpoint /balance/deduct 404, fallback to recordExpense');
                return $this->recordExpense($referenceId, $amount, $description, $category, $note);
            }

            Log::error('Finance API deductBalance Error: ' . $response->body());

            return [
                'success' => false,
                'message' => 'Gagal memotong saldo di Finance API: ' . ($response->json('message') ?? $response->status()),
                'data' => null,
            ];
        } catch (Exception $e) {
            Log::error('Finance API deductBalance Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Koneksi ke Finance API gagal: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Kembalikan saldo ke website Finance (refund) saat transfer GAGAL.
     * Menggunakan POST /api/v1/balance/refund jika ada, fallback ke recordIncome.
     *
     * @param float  $amount        Jumlah yang dikembalikan
     * @param string $referenceId   ID referensi transfer yang gagal
     * @param string $description   Keterangan refund
     * @param string $reason        Alasan pengembalian (misal: INVALID_DESTINATION)
     * @return array ['success' => bool, 'message' => string, 'data' => ?array]
     */
    public function refundBalance(
        float $amount,
        string $referenceId,
        string $description,
        string $reason = 'Transfer gagal'
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi API Finance belum lengkap.',
                'data'    => null,
            ];
        }

        // Coba endpoint /balance/refund dulu
        $path    = '/api/v1/balance/refund';
        $payload = [
            'amount'       => (float) $amount,
            'reference_id' => 'REFUND-' . $referenceId,
            'description'  => $description,
            'reason'       => $reason,
        ];

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        try {
            $headers  = $this->buildHeaders('POST', $path, $jsonPayload);
            $url      = $this->baseUrl . $path;
            $response = Http::timeout(10)->withHeaders($headers)->withBody($jsonPayload, 'application/json')->post($url);

            if ($response->successful()) {
                Log::info('[Finance API] Refund saldo berhasil', [
                    'reference_id' => $referenceId,
                    'amount'       => $amount,
                ]);
                return [
                    'success' => true,
                    'message' => 'Saldo berhasil dikembalikan ke Finance.',
                    'data'    => $response->json()['data'] ?? [],
                ];
            }

            // Jika endpoint belum tersedia, fallback ke recordIncome (menambah saldo)
            if ($response->status() === 404 || $response->status() === 405) {
                Log::info('[Finance API] Endpoint /balance/refund tidak tersedia, fallback ke recordIncome');
                return $this->recordIncome(
                    subjectExternalId: 'REFUND-' . $referenceId,
                    amount:            $amount,
                    description:       $description,
                    source:            'Refund Transfer Gagal'
                );
            }

            Log::error('[Finance API] refundBalance Error: ' . $response->body());

            return [
                'success' => false,
                'message' => 'Gagal mengembalikan saldo ke Finance API: ' . ($response->json('message') ?? $response->status()),
                'data'    => null,
            ];
        } catch (Exception $e) {
            Log::error('[Finance API] refundBalance Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Koneksi ke Finance API gagal saat refund: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }
}
