<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfilePasswordUpdateRequest;
use App\Http\Requests\Admin\ProfileUpdateRequest;
use App\Services\Admin\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Profile/Index', [
            'profile' => $this->profileService->getForEdit(Auth::user()),
            'urls' => [
                'update' => route('admin.profile.update'),
                'passwordUpdate' => route('admin.profile-password.update'),
            ],
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->profileService->update(Auth::user(), $request, $request->validated());

        return back()->with('success', '¡Perfil actualizado correctamente!');
    }

    public function passwordUpdate(ProfilePasswordUpdateRequest $request): RedirectResponse
    {
        $this->profileService->updatePassword(Auth::user(), $request->validated()['password']);

        return back()->with('success', '¡Contraseña actualizada correctamente!');
    }
}
