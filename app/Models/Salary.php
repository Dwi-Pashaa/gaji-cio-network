<?php

namespace App\Models;

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
}
