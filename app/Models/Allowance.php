<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;
    protected $table = 'allowance';
    protected $fillable = ['name', 'amount'];

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_allownce', 'allowance_id', 'user_id');
    }
}
