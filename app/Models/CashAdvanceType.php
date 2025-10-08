<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvanceType extends Model
{
    use HasFactory;
    protected $table = 'cash_advance_type';
    protected $fillable = ['name', 'amount'];
}
