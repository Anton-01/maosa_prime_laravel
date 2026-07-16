<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Services\ImageUploadService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingUpdateService
{
    public function __construct(
        private readonly ImageUploadService $imageUploadService,
        private readonly SettingsService $settingsService,
    ) {
    }

    /**
     * Current values shaped for the settings screen.
     *
     * @return array<string, mixed>
     */
    public function getSettingsData(): array
    {
        return [
            'general' => [
                'site_name' => config('settings.site_name'),
                'site_email' => config('settings.site_email'),
                'site_phone' => config('settings.site_phone'),
            ],
            'logos' => [
                'logo' => config('settings.logo'),
                'logoUrl' => config('settings.logo') ? asset(config('settings.logo')) : null,
                'favicon' => config('settings.favicon'),
                'faviconUrl' => config('settings.favicon') ? asset(config('settings.favicon')) : null,
            ],
            'appearance' => [
                'site_default_color' => config('settings.site_default_color'),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data Validated key/value settings.
     */
    public function updateKeyValues(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->settingsService->clearCachedSettings();
    }

    /**
     * @param array<string, mixed> $data Validated payload (old_logo, old_favicon).
     */
    public function updateLogos(Request $request, array $data): void
    {
        $logoPath = $this->imageUploadService->upload($request, 'logo', $data['old_logo'] ?? null);
        $faviconPath = $this->imageUploadService->upload($request, 'favicon', $data['old_favicon'] ?? null);

        Setting::updateOrCreate(
            ['key' => 'logo'],
            ['value' => $logoPath ?: ($data['old_logo'] ?? null)],
        );

        Setting::updateOrCreate(
            ['key' => 'favicon'],
            ['value' => $faviconPath ?: ($data['old_favicon'] ?? null)],
        );

        $this->settingsService->clearCachedSettings();
    }
}
