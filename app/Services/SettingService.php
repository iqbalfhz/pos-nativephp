<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_KEY = 'app_settings';
    private const CACHE_TTL = 86400; // 24 hours

    /**
     * Get all settings from cache or database
     */
    public static function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->mapWithKeys(fn ($setting) => [
                $setting->key => self::castValue($setting),
            ])->toArray();
        });
    }

    /**
     * Get a setting value
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::all();
        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', ?string $description = null)
    {
        Setting::set($key, $value, $type, $description);
        self::clearCache();
        return self::get($key);
    }

    /**
     * Update multiple settings
     */
    public static function updateMultiple(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => is_array($value) ? json_encode($value) : $value]);
        }
        self::clearCache();
    }

    /**
     * Get default settings
     */
    public static function defaults(): array
    {
        return [
            'store_name' => 'POS Nativephp',
            'store_address' => 'Jakarta, Indonesia',
            'store_phone' => '08123456789',
            'store_tax_id' => '00.000.000.0-000.000',
            'pb1_rate' => 10,
            'receipt_header' => 'Terima Kasih Telah Berbelanja',
            'receipt_footer' => 'Semoga Puas dengan Layanan Kami',
            'low_stock_threshold' => 10,
            'enable_notifications' => true,
        ];
    }

    /**
     * Initialize default settings
     */
    public static function initialize(): void
    {
        foreach (self::defaults() as $key => $value) {
            if (!Setting::where('key', $key)->exists()) {
                Setting::set($key, $value);
            }
        }
        self::clearCache();
    }

    /**
     * Cast setting value based on type
     */
    private static function castValue(Setting $setting)
    {
        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'number' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
