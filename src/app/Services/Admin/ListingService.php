<?php

namespace App\Services\Admin;

use App\Models\Amenity;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingSocialLink;
use App\Models\Location;
use App\Models\SocialNetwork;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ListingService
{
    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    /**
     * All listings shaped for the frontend table.
     */
    public function list(): Collection
    {
        return Listing::with(['category:id,name', 'location:id,name'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Listing $listing) => [
                'id' => $listing->id,
                'title' => $listing->title,
                'imageUrl' => $listing->image ? asset($listing->image) : null,
                'category' => $listing->category->name ?? 'N/A',
                'location' => $listing->location->name ?? 'N/A',
                'status' => (int) $listing->status,
                'isFeatured' => (int) $listing->is_featured,
                'isVerified' => (int) $listing->is_verified,
                'createdAt' => $listing->created_at?->format('d/m/Y'),
            ]);
    }

    /**
     * Select options for the create/edit form.
     *
     * @return array<string, mixed>
     */
    public function getFormOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(['id', 'name'])
                ->map(fn (Category $category) => ['value' => $category->id, 'label' => $category->name]),
            'locations' => Location::orderBy('name')->get(['id', 'name'])
                ->map(fn (Location $location) => ['value' => $location->id, 'label' => $location->name]),
            'socialNetworks' => SocialNetwork::active()->ordered()->get()
                ->map(fn (SocialNetwork $network) => ['value' => $network->id, 'label' => $network->name]),
        ];
    }

    /**
     * The listing shaped for the edit form.
     *
     * @return array<string, mixed>
     */
    public function getForEdit(int $id): array
    {
        $listing = Listing::with('socialLinks')->findOrFail($id);

        return [
            'id' => $listing->id,
            'title' => $listing->title,
            'category' => $listing->category_id,
            'location' => $listing->location_id,
            'address' => $listing->address,
            'phone' => $listing->phone,
            'email' => $listing->email,
            'website' => $listing->website,
            'description' => $listing->description,
            'google_map_embed_code' => $listing->google_map_embed_code,
            'seo_title' => $listing->seo_title,
            'seo_description' => $listing->seo_description,
            'status' => (int) $listing->status,
            'is_featured' => (int) $listing->is_featured,
            'is_verified' => (int) $listing->is_verified,
            'is_previliged' => (int) $listing->is_previliged,
            'image' => $listing->image,
            'imageUrl' => $listing->image ? asset($listing->image) : null,
            'thumbnail_image' => $listing->thumbnail_image,
            'thumbnailImageUrl' => $listing->thumbnail_image ? asset($listing->thumbnail_image) : null,
            'social_links' => $listing->socialLinks
                ->map(fn (ListingSocialLink $link) => [
                    'social_network_id' => $link->social_network_id,
                    'url' => $link->url,
                ])
                ->values(),
        ];
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(Request $request, array $data): Listing
    {
        $listing = new Listing();
        $listing->image = $this->imageUploadService->upload($request, 'image');
        $listing->thumbnail_image = $this->imageUploadService->upload($request, 'thumbnail_image');
        $listing->is_approved = 1;
        $this->fill($listing, $data);
        $listing->save();

        $this->syncSocialLinks($listing, $data['social_links'] ?? []);
        $this->flushCache($listing);

        return $listing;
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(Request $request, int $id, array $data): Listing
    {
        $listing = Listing::findOrFail($id);

        $imagePath = $this->imageUploadService->upload($request, 'image', $listing->image);
        $thumbnailPath = $this->imageUploadService->upload($request, 'thumbnail_image', $listing->thumbnail_image);

        $listing->image = $imagePath ?: $listing->image;
        $listing->thumbnail_image = $thumbnailPath ?: $listing->thumbnail_image;
        $this->fill($listing, $data);
        $listing->save();

        $this->syncSocialLinks($listing, $data['social_links'] ?? []);
        $this->flushCache($listing);

        return $listing;
    }

    public function delete(int $id): void
    {
        $listing = Listing::findOrFail($id);

        $this->flushCache($listing);

        $listing->delete();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fill(Listing $listing, array $data): void
    {
        $listing->user_id = Auth::id();
        $listing->title = $data['title'];
        $listing->slug = Str::slug($data['title']);
        $listing->category_id = $data['category'];
        $listing->location_id = $data['location'];
        $listing->address = $data['address'];
        $listing->phone = $data['phone'];
        $listing->email = $data['email'];
        $listing->website = $data['website'] ?? null;
        $listing->description = $data['description'];
        $listing->google_map_embed_code = $data['google_map_embed_code'] ?? null;
        $listing->seo_title = $data['seo_title'] ?? null;
        $listing->seo_description = $data['seo_description'] ?? null;
        $listing->status = $data['status'];
        $listing->is_featured = $data['is_featured'];
        $listing->is_verified = $data['is_verified'];
        $listing->is_previliged = $data['is_previliged'];
    }

    /**
     * Replace the listing's social links with the submitted set.
     *
     * @param array<int, array<string, mixed>> $links
     */
    private function syncSocialLinks(Listing $listing, array $links): void
    {
        ListingSocialLink::where('listing_id', $listing->id)->delete();

        foreach ($links as $link) {
            if (! empty($link['social_network_id']) && ! empty($link['url'])) {
                ListingSocialLink::create([
                    'listing_id' => $listing->id,
                    'social_network_id' => $link['social_network_id'],
                    'url' => $link['url'],
                    'order' => 0,
                ]);
            }
        }
    }

    private function flushCache(Listing $listing): void
    {
        Cache::forget("listing_detail:{$listing->slug}");
        Cache::forget("listing_related:{$listing->category_id}_*");
        Cache::forget('home_page_data');
        Cache::forget('admin_dashboard_stats');
    }
}
