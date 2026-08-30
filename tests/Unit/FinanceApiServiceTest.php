<?php

namespace Tests\Unit;

use App\Services\FinanceApiService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FinanceApiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.finance.url', 'http://127.0.0.1:8000');
        Config::set('services.finance.client_id', 'client-test-123');
        Config::set('services.finance.key_id', 'key-test-456');
        Config::set('services.finance.secret', 'secret-key-test');
        Config::set('services.finance.client_name', 'Web CIO Keuangan Test');
    }

    public function test_generate_signature(): void
    {
        $service = new FinanceApiService();
        $method = 'POST';
        $path = '/api/v1/history';
        $timestamp = '1724650000';
        $nonce = 'req-test-123';
        $body = '{"test":"value"}';

        $signature = $service->generateSignature($method, $path, $timestamp, $nonce, $body);

        $bodyHash = hash('sha256', $body);
        $canonical = "POST\n/api/v1/history\nclient-test-123\nkey-test-456\n1724650000\nreq-test-123\n" . $bodyHash;
        $expected = base64_encode(hash_hmac('sha256', $canonical, 'secret-key-test', true));

        $this->assertSame($expected, $signature);
    }

    public function test_get_balance_success(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/api/v1/balance' => Http::response([
                'code' => 200,
                'status' => 'success',
                'data' => [
                    'client_code' => 'CLT-01',
                    'client_name' => 'Web CIO Keuangan Test',
                    'balance' => '15000000.00',
                ]
            ], 200)
        ]);

        $service = new FinanceApiService();
        $result = $service->getBalance();

        $this->assertTrue($result['success']);
        $this->assertSame(15000000.0, $result['balance']);
    }

    public function test_record_expense_success(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/api/v1/history' => Http::response([
                'code' => 200,
                'status' => 'success',
                'message' => 'History created successfully'
            ], 200)
        ]);

        $service = new FinanceApiService();
        $result = $service->recordExpense(
            'KB-1',
            500000.0,
            'melakukan pembayaran kasbon karyawan: Budi',
            'kasbon',
            'Kasbon operasional'
        );

        $this->assertTrue($result['success']);
    }
}
