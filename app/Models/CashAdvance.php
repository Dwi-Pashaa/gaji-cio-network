<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{
    use HasFactory;
    protected $table = 'cash_advance';
    protected $fillable = [
        'user_id', 'amount', 'payment_type', 'admin_fee', 'status', 'request_date', 'approved_date', 'title', 'type_id',
        'bank_name', 'account_number', 'account_holder_name',
        'xendit_disbursement_id', 'xendit_status', 'transfer_at',
    ];

    protected $casts = [
        'transfer_at' => 'datetime',
        'amount'      => 'decimal:2',
        'admin_fee'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(CashAdvanceType::class, 'type_id', 'id');
    }

    /**
     * Label warna badge untuk tiap status kasbon
     */
    public function statusBadgeColor(): string
    {
        return match($this->status) {
            'approved'    => 'primary',
            'transferring'=> 'info',
            'transferred' => 'success',
            'rejected'    => 'danger',
            'failed'      => 'warning',
            default       => 'secondary', // pending
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'     => 'Menunggu',
            'approved'    => 'Disetujui',
            'transferring'=> 'Sedang Transfer',
            'transferred' => 'Sudah Ditransfer',
            'rejected'    => 'Ditolak',
            'failed'      => 'Gagal Transfer',
            default       => ucfirst($this->status),
        };
    }
}
