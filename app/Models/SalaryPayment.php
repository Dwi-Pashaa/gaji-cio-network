<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    protected $table = 'salary_payments';

    protected $fillable = [
        'salary_id',
        'user_id',
        'payment_type',
        'transferred_by',
        'period_month',
        'period_year',
        'base_salary',
        'total_allowance',
        'total_cash_advance',
        'net_salary',
        'bank_name',
        'account_number',
        'account_holder_name',
        'xendit_external_id',
        'xendit_disbursement_id',
        'xendit_status',
        'status',
        'transfer_at',
        'notes',
    ];

    protected $casts = [
        'base_salary'       => 'decimal:2',
        'total_allowance'   => 'decimal:2',
        'total_cash_advance'=> 'decimal:2',
        'net_salary'        => 'decimal:2',
        'transfer_at'       => 'datetime',
        'period_month'      => 'integer',
        'period_year'       => 'integer',
    ];

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    /**
     * Label periode bulan-tahun dalam Bahasa Indonesia
     */
    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return ($months[$this->period_month] ?? $this->period_month) . ' ' . $this->period_year;
    }
}
