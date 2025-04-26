<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'name',
        'description',
        'is_public',
        'order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Clear cache when settings are updated
        static::saved(function ($setting) {
            Cache::forget('settings');
            Cache::forget('settings.groups');
            Cache::forget('settings.' . $setting->group);
            Cache::forget('settings.key.' . $setting->key);
        });

        static::deleted(function ($setting) {
            Cache::forget('settings');
            Cache::forget('settings.groups');
            Cache::forget('settings.' . $setting->group);
            Cache::forget('settings.key.' . $setting->key);
        });
    }

    /**
     * Get typed value of the setting.
     *
     * @return mixed
     */
    public function getTypedValueAttribute()
    {
        // Return typed value based on type
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'json':
                return json_decode($this->value, true) ?: [];
            case 'array':
                if (empty($this->value)) {
                    return [];
                }
                return json_decode($this->value, true) ?: [];
            default: // string, text, etc
                return $this->value;
        }
    }

    /**
     * Set the value with proper type casting.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        // Cast value based on type before saving
        if (isset($this->attributes['type'])) {
            switch ($this->attributes['type']) {
                case 'json':
                case 'array':
                    if (is_array($value)) {
                        $this->attributes['value'] = json_encode($value);
                    } else {
                        $this->attributes['value'] = $value;
                    }
                    break;
                case 'boolean':
                    $this->attributes['value'] = $value ? '1' : '0';
                    break;
                default:
                    $this->attributes['value'] = (string) $value;
            }
        } else {
            // Default to string
            $this->attributes['value'] = (string) $value;
        }
    }

    /**
     * Get all settings as a key-value array.
     *
     * @return array
     */
    public static function getAllSettings()
    {
        return Cache::rememberForever('settings', function () {
            $settings = [];
            
            foreach(self::all() as $setting) {
                $settings[$setting->key] = $setting->typed_value;
            }
            
            return $settings;
        });
    }

    /**
     * Get settings by group
     *
     * @param string $group
     * @return array
     */
    public static function getGroup($group)
    {
        return Cache::rememberForever('settings.' . $group, function () use ($group) {
            $settings = [];
            
            foreach(self::where('group', $group)->get() as $setting) {
                $settings[$setting->key] = $setting->typed_value;
            }
            
            return $settings;
        });
    }

    /**
     * Get setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Cache::rememberForever('settings.key.' . $key, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return $setting->typed_value;
        });
    }

    /**
     * Set setting value by key
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return false;
        }
        
        $setting->value = $value;
        return $setting->save();
    }

    /**
     * Get available setting groups
     *
     * @return array
     */
    public static function getGroups()
    {
        return Cache::rememberForever('settings.groups', function () {
            return self::select('group')->distinct()->pluck('group')->toArray();
        });
    }
}
