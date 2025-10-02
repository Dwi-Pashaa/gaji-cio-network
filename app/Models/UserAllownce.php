<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAllownce extends Model
{
    use HasFactory;
    protected $table = 'user_allownce';
    protected $fillable = ['user_id', 'allowance_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allowance()
    {
        return $this->belongsTo(Allowance::class);
    }
}
