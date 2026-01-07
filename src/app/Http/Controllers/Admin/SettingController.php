<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingsService;
use App\Traits\FileUploadTrait;
use Artisan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Redirect;

class SettingController extends Controller
{
    use FileUploadTrait;

    function __construct()
    {
        $this->middleware(['permission:settings index']);
    }

    function index() : View {
        return view('admin.setting.index');
    }

    function updateGeneralSetting(Request $request) : RedirectResponse {
        $validatedData = $request->validate([
            'site_name' => ['required', 'max:255'],
            'site_email' => ['required', 'max:255', 'email'],
            'site_phone' => ['required', 'max:255']
        ]);

        foreach($validatedData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $settingsService = app(SettingsService::class);
        $settingsService->clearCachedSettings();

        return back()->with('statusStnGen', true);
    }

    function logoSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'max:3000'],
            'favicon' => ['nullable', 'image', 'max:3000'],
        ]);

        $logoPath = $this->uploadImage($request, 'logo', $request->old_image);
        $faviconPath = $this->uploadImage($request, 'favicon', $request->old_favicon);

        Setting::updateOrCreate(
            ['key' => 'logo'],
            ['value' => !empty($logoPath) ? $logoPath : $request->old_image]
        );

        Setting::updateOrCreate(
            ['key' => 'favicon'],
            ['value' => !empty($faviconPath) ? $faviconPath : $request->old_favicon]
        );


        $settingsService = app(SettingsService::class);
        $settingsService->clearCachedSettings();

        return back()->with('statusStnLg', true);
    }

    function appearanceSetting(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'site_default_color' => ['required'],

        ]);

        foreach($validatedData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $settingsService = app(SettingsService::class);
        $settingsService->clearCachedSettings();

        return back()->with('statusStnApr', true);
    }
}
