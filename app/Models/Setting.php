<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get the setting value with automatic type casting.
     */
    public function getValueAttribute($value)
    {
        // Skip JSON parsing if value is null or empty
        if (empty($value)) {
            return $value;
        }

        // Try to decode JSON first with proper error handling
        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            return $decoded;
        } catch (\JsonException $e) {
            // If JSON parsing fails, continue with other type casting
            // Log the error for debugging but don't throw it
            \Log::debug('JSON parsing failed for setting value: ' . $value . ' Error: ' . $e->getMessage());
        }

        // Handle boolean strings
        if ($value === 'true') return true;
        if ($value === 'false') return false;

        // Handle numeric strings
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        return $value;
    }

    /**
     * Set the setting value with automatic JSON encoding for arrays.
     */
    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set($key, $value, $type = 'string', $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Check if a setting exists.
     */
    public static function has($key)
    {
        return static::where('key', $key)->exists();
    }

    /**
     * Delete a setting by key.
     */
    public static function remove($key)
    {
        return static::where('key', $key)->delete();
    }

    /**
     * Get all settings as key-value pairs.
     */
    public static function getAllSettings()
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Get settings by group (prefix).
     */
    public static function getGroup($prefix)
    {
        return static::where('key', 'like', $prefix . '%')
                    ->pluck('value', 'key')
                    ->toArray();
    }
}
