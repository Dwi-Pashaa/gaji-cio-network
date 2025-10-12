<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    use HasFactory;
    protected $table = 'expenditure';
    protected $fillable = ['type_id', 'date', 'title', 'amount'];

    public function type()
    {
        return $this->belongsTo(CashAdvanceType::class, 'type_id', 'id');
    }
}
