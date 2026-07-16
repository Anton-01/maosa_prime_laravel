<?php

namespace App\Services\Admin;

use App\Models\AboutUs;
use App\Models\Contact;
use App\Models\PrivacyPolicy;
use App\Models\TermsAndCondition;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

/**
 * Singleton content of the static pages (about us, contact, privacy
 * policy and terms & conditions), managed from the unified Pages screen.
 */
class StaticPageService
{
    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    /**
     * Everything the unified Pages screen needs, shaped for the frontend.
     *
     * @return array<string, mixed>
     */
    public function getPagesData(): array
    {
        $about = AboutUs::first();
        $contact = Contact::first();

        return [
            'about' => [
                'image' => $about?->image,
                'imageUrl' => $about?->image ? asset($about->image) : null,
                'video_url' => $about?->video_url,
                'description' => $about?->description,
                'button_url' => $about?->button_url,
            ],
            'contact' => [
                'phone' => $contact?->phone,
                'email' => $contact?->email,
                'address' => $contact?->address,
                'map_link' => $contact?->map_link,
            ],
            'privacyPolicy' => [
                'description' => PrivacyPolicy::first()?->description,
            ],
            'terms' => [
                'description' => TermsAndCondition::first()?->description,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function updateAbout(Request $request, array $data): AboutUs
    {
        $imagePath = $this->imageUploadService->upload($request, 'image', $data['old_image'] ?? null);

        return AboutUs::updateOrCreate(
            ['id' => 1],
            [
                'image' => $imagePath ?: ($data['old_image'] ?? null),
                'video_url' => $data['video_url'],
                'description' => $data['description'],
                'button_url' => $data['button_url'] ?? null,
            ],
        );
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function updateContact(array $data): Contact
    {
        return Contact::updateOrCreate(
            ['id' => 1],
            [
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'map_link' => $data['map_link'],
            ],
        );
    }

    public function updatePrivacyPolicy(string $description): PrivacyPolicy
    {
        return PrivacyPolicy::updateOrCreate(['id' => 1], ['description' => $description]);
    }

    public function updateTermsAndConditions(string $description): TermsAndCondition
    {
        return TermsAndCondition::updateOrCreate(['id' => 1], ['description' => $description]);
    }
}
