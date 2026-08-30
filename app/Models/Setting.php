<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'setting';

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    /**
     * Get a setting value by key with optional default value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting !== null ? $setting->value : $default;
    }

    /**
     * Set or update a setting value by key.
     */
    public static function set(string $key, mixed $value, ?int $userId = null): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value'   => (string) $value,
                'user_id' => $userId,
            ]
        );
    }
}
