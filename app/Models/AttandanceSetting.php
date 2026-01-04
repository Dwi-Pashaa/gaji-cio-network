<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttandanceSetting extends Model
{
    use HasFactory;
    protected $table = 'attendance_settings';
    protected $fillable = [
        'start_work',
        'end_work',
        'alpha',
        'cuti',
        'telat',
    ];
}
