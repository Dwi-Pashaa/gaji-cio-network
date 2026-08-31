<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $table = 'salarie';
    protected $fillable = ['user_id', 'base_salary', 'effective_date', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(SalaryPayment::class, 'salary_id');
    }

    /**
     * Mendapatkan hari dalam sebulan untuk tanggal aktif gaji (1 - 31)
     */
    public function getEffectiveDayAttribute(): int
    {
        if (empty($this->effective_date)) {
            return 1;
        }
        return (int) Carbon::parse($this->effective_date)->format('d');
    }

    /**
     * Mendapatkan pembayaran gaji untuk periode bulan dan tahun berjalan
     */
    public function getCurrentMonthPayment(int $month = null, int $year = null): ?SalaryPayment
    {
        $month = $month ?? Carbon::now()->month;
        $year  = $year ?? Carbon::now()->year;

        if ($this->relationLoaded('payments')) {
            return $this->payments
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->sortByDesc('id')
                ->first();
        }

        return $this->payments()
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Mengecek status transfer gaji untuk periode berjalan:
     * - 'inactive': Status data gaji nonaktif
     * - 'not_due': Belum masuk tanggal gajian bulan ini (hari_ini < effective_day)
     * - 'can_transfer': Sudah tanggal gajian & belum ada pembayaran
     * - 'already_transferred': Sudah berhasil ditransfer
     * - 'pending': Sedang diproses transfer (Xendit)
     * - 'failed': Transfer sebelumnya gagal (tombol transfer ulang dimunculkan)
     */
    public function getTransferState(int $month = null, int $year = null): array
    {
        if ($this->status !== 'active') {
            return [
                'status'  => 'inactive',
                'message' => 'Status Nonaktif',
                'payment' => null,
            ];
        }

        $now          = Carbon::now();
        $targetMonth  = $month ?? $now->month;
        $targetYear   = $year ?? $now->year;
        $effectiveDay = $this->effective_day;
        $daysInMonth  = Carbon::create($targetYear, $targetMonth, 1)->daysInMonth;
        $payDay       = min($effectiveDay, $daysInMonth);

        $today = $now->day;

        // Ambil pembayaran untuk periode bulan & tahun ini
        $payment = $this->getCurrentMonthPayment($targetMonth, $targetYear);

        // Jika sudah ada pembayaran
        if ($payment) {
            if ($payment->status === 'transferred') {
                return [
                    'status'  => 'already_transferred',
                    'message' => 'Sudah Ditransfer',
                    'payment' => $payment,
                ];
            }

            if ($payment->status === 'pending') {
                return [
                    'status'  => 'pending',
                    'message' => 'Sedang Diproses',
                    'payment' => $payment,
                ];
            }

            if ($payment->status === 'failed') {
                return [
                    'status'  => 'failed',
                    'message' => 'Transfer Gagal (Coba Lagi)',
                    'payment' => $payment,
                ];
            }
        }

        // Jika belum ada pembayaran:
        // Cek apakah tanggal hari ini sudah mencapai tanggal aktif gaji
        if ($today < $payDay) {
            return [
                'status'  => 'not_due',
                'message' => 'Jadwal: Tgl ' . $payDay,
                'pay_day' => $payDay,
                'payment' => null,
            ];
        }

        // Sudah tanggal gajian dan belum ada transfer
        return [
            'status'  => 'can_transfer',
            'message' => 'Transfer Gaji',
            'pay_day' => $payDay,
            'payment' => null,
        ];
    }
}

