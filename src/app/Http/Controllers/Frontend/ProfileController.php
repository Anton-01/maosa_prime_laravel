<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ProfileUpdateRequest;
use App\Traits\FileUploadTrait;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    use FileUploadTrait;

    function index(): Response
    {
        return Inertia::render('User/Profile', [
            'profile' => $this->presentUser(),
        ]);
    }

    function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $avatarPath = $this->uploadImage($request, 'avatar', $request->old_avatar);
        $bannerPath = $this->uploadImage($request, 'banner', $request->old_banner);

        $user = Auth::user();
        $user->avatar = !empty($avatarPath) ? $avatarPath : $request->old_avatar;
        $user->banner = !empty($bannerPath) ? $bannerPath : $request->old_banner;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->about = $request->about;
        $user->save();

        return back()->with('success', '¡Perfil actualizado correctamente!');
    }

    function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:5', 'confirmed']
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', [
            'main_message' => 'Contraseña Actualizada',
            'description' => 'Tu contraseña ha sido actualizada correctamente. Por favor, inicia sesión de nuevo.',
            'alert_type' => 'success'
        ]);
    }

    /**
     * Shape the authenticated user for the profile screen.
     *
     * @return array<string, mixed>
     */
    protected function presentUser(): array
    {
        $user = Auth::user();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'about' => $user->about,
            'avatar' => $user->avatar,
            'banner' => $user->banner,
            'user_type' => $user->user_type,
            'can_view_price_table' => $user->canViewPriceTable(),
        ];
    }
}
