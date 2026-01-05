<?php
namespace App\Services;

use Cache;

class SettingsService {

    function getSettings() {
        if (!class_exists(\App\Models\Setting::class)) {
            return [];
        }

        try {
            return Cache::rememberForever('settings', function () {
                return \App\Models\Setting::pluck('value', 'key')->toArray();
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    function setGlobalSettings(): void
    {
        $settings = $this->getSettings();
        config()->set('settings', $settings);
    }

    function clearCachedSettings(): void
    {
        Cache::forget('settings');
    }
}
