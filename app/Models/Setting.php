<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    // دریافت یک مقدار
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        return match($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            default => $setting->value,
        };
    }

    // دریافت همه تنظیمات
    public static function allSettings()
    {
        $settings = self::all();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = match($setting->type) {
                'boolean' => (bool) $setting->value,
                'integer' => (int) $setting->value,
                default => $setting->value,
            };
        }

        return $result;
    }

    // بروزرسانی
    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
            return true;
        }
        return false;
    }

    // ذخیره یا ایجاد تنظیمات جدید
    /*public static function setOrCreate($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            $setting->update([
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]);
        }

        return true;
    }*/
}