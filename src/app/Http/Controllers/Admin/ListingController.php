<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ListingDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListingStoreRequest;
use App\Http\Requests\Admin\ListingUpdateRequest;
use App\Models\Amenity;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingAmenity;
use App\Models\ListingSocialLink;
use App\Models\Location;
use App\Models\SocialNetwork;
use App\Traits\FileUploadTrait;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Str;

class ListingController extends Controller
{
    use FileUploadTrait;

    function __construct()
    {
        $this->middleware(['permission:access management suppliers']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListingDataTable $dataTable) : View | JsonResponse
    {
        return $dataTable->render('admin.listing.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        $categories = Category::all();
        $locations = Location::all();
        $amenities = Amenity::all();
        $socialNetworks = SocialNetwork::active()->ordered()->get();

        return view('admin.listing.create', compact('categories', 'locations', 'amenities', 'socialNetworks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ListingStoreRequest $request): RedirectResponse
    {
        $imagePath = $this->uploadImage($request, 'image');
        $thumbnailPath = $this->uploadImage($request, 'thumbnail_image');

        $listing = new Listing();
        $listing->user_id = Auth::user()->id;
        $listing->image = $imagePath;
        $listing->thumbnail_image = $thumbnailPath;
        $listing->title = $request->title;
        $listing->slug = Str::slug($request->title);
        $listing->category_id = $request->category;
        $listing->location_id = $request->location;
        $listing->address = $request->address;
        $listing->phone = $request->phone;
        $listing->email = $request->email;
        $listing->website = $request->website;
        $listing->description = $request->description;
        $listing->google_map_embed_code = $request->google_map_embed_code;
        $listing->seo_title = $request->seo_title;
        $listing->seo_description = $request->seo_description;
        $listing->status = $request->status;
        $listing->is_featured = $request->is_featured;
        $listing->is_verified = $request->is_verified;
        $listing->is_previliged = $request->is_previliged;
        $listing->is_approved = 1;
        $listing->save();

        if ($request->has('social_links')) {
            foreach ($request->social_links as $link) {
                if (!empty($link['social_network_id']) && !empty($link['url'])) {
                    ListingSocialLink::create([
                        'listing_id' => $listing->id,
                        'social_network_id' => $link['social_network_id'],
                        'url' => $link['url'],
                        'order' => 0
                    ]);
                }
            }
        }

        $this->flushListingCache($listing->slug, $listing->category_id);

        return to_route('admin.listing.index')->with('statusCtLts', true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) : View
    {
        $listing = Listing::findOrFail($id);
        $listingAmenities = ListingAmenity::where('listing_id', $listing->id)->pluck('amenity_id')->toArray();

        $categories = Category::all();
        $locations = Location::all();
        $amenities = Amenity::all();
        $socialNetworks = SocialNetwork::active()->ordered()->get();

        return view('admin.listing.edit', compact('categories', 'locations', 'amenities', 'listing', 'listingAmenities', 'socialNetworks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ListingUpdateRequest $request, string $id) : RedirectResponse
    {
        $listing = Listing::findOrFail($id);

        $imagePath = $this->uploadImage($request, 'image', $request->old_image);
        $thumbnailPath = $this->uploadImage($request, 'thumbnail_image', $request->old_thumbnail_image);

        $listing->user_id = Auth::user()->id;
        $listing->image = !empty($imagePath) ? $imagePath : $request->old_image;
        $listing->thumbnail_image = !empty($thumbnailPath) ? $thumbnailPath : $request->old_thumbnail_image;
        $listing->title = $request->title;
        $listing->slug = Str::slug($request->title);
        $listing->category_id = $request->category;
        $listing->location_id = $request->location;
        $listing->address = $request->address;
        $listing->phone = $request->phone;
        $listing->email = $request->email;
        $listing->website = $request->website;
        $listing->description = $request->description;
        $listing->google_map_embed_code = $request->google_map_embed_code;
        $listing->seo_title = $request->seo_title;
        $listing->seo_description = $request->seo_description;
        $listing->status = $request->status;
        $listing->is_featured = $request->is_featured;
        $listing->is_verified = $request->is_verified;
        $listing->is_previliged = $request->is_previliged;
        $listing->save();

        // Update social links
        ListingSocialLink::where('listing_id', $listing->id)->delete();

        if ($request->has('social_links')) {
            foreach ($request->social_links as $link) {
                if (!empty($link['social_network_id']) && !empty($link['url'])) {
                    ListingSocialLink::create([
                        'listing_id' => $listing->id,
                        'social_network_id' => $link['social_network_id'],
                        'url' => $link['url'],
                        'order' => 0
                    ]);
                }
            }
        }

        $this->flushListingCache($listing->slug, $listing->category_id);

        return to_route('admin.listing.index')->with('statusUpLts', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $listing = Listing::findOrFail($id);
            $this->flushListingCache($listing->slug, $listing->category_id);
            $listing->delete();
            return response(['status' => 'success', 'message' => 'Deleted successfully!']);
        }catch(Exception $e){
            logger($e);
            return response(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Flush listing-related cache keys.
     */
    private function flushListingCache(string $slug, ?int $categoryId): void
    {
        Cache::forget("listing_detail:{$slug}");
        Cache::forget("listing_related:{$categoryId}_*");
        Cache::forget('home_page_data');
        Cache::forget('admin_dashboard_stats');
    }
}
