<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{
    use HasFactory;
    protected $table = 'cash_advance';
    protected $fillable = ['user_id', 'amount', 'status', 'request_date', 'approved_date', 'title'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
