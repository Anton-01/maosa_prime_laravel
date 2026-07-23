<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class ProfileService
{
    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    /**
     * The authenticated admin shaped for the profile screen.
     *
     * @return array<string, mixed>
     */
    public function getForEdit(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'avatar' => $user->avatar,
            'avatarUrl' => $user->avatar ? asset($user->avatar) : null,
            'banner' => $user->banner,
            'bannerUrl' => $user->banner ? asset($user->banner) : null,
        ];
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(User $user, Request $request, array $data): User
    {
        $avatarPath = $this->imageUploadService->upload($request, 'avatar', $user->avatar);
        $bannerPath = $this->imageUploadService->upload($request, 'banner', $user->banner);

        $user->avatar = $avatarPath ?: $user->avatar;
        $user->banner = $bannerPath ?: $user->banner;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->address = $data['address'];
        $user->save();

        return $user;
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->password = bcrypt($password);
        $user->save();
    }
}
