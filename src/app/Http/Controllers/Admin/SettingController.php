<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AppearanceSettingsUpdateRequest;
use App\Http\Requests\Admin\GeneralSettingsUpdateRequest;
use App\Http\Requests\Admin\LogoSettingsUpdateRequest;
use App\Services\Admin\SettingUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(private readonly SettingUpdateService $settingUpdateService)
    {
        $this->middleware(['permission:settings index']);
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Settings/Index', [
            'initialTab' => $request->query('tab', 'general'),
            ...$this->settingUpdateService->getSettingsData(),
            'urls' => [
                'generalUpdate' => route('admin.general-settings.update'),
                'logoUpdate' => route('admin.logo-settings.update'),
                'appearanceUpdate' => route('admin.appearance-settings.update'),
            ],
        ]);
    }

    public function updateGeneralSetting(GeneralSettingsUpdateRequest $request): RedirectResponse
    {
        $this->settingUpdateService->updateKeyValues($request->validated());

        return back()->with('success', '¡Ajustes generales actualizados correctamente!');
    }

    public function logoSettings(LogoSettingsUpdateRequest $request): RedirectResponse
    {
        $this->settingUpdateService->updateLogos($request, $request->validated());

        return back()->with('success', '¡Logo y favicon actualizados correctamente!');
    }

    public function appearanceSetting(AppearanceSettingsUpdateRequest $request): RedirectResponse
    {
        $this->settingUpdateService->updateKeyValues($request->validated());

        return back()->with('success', '¡Apariencia actualizada correctamente!');
    }
}
