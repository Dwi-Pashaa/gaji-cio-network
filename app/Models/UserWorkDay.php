<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWorkDay extends Model
{
    use HasFactory;
    protected $table = 'user_workday';
    protected $fillable = [
        'user_id',
        'weekday',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
