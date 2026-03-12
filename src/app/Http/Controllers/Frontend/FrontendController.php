<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\AboutUs;
use App\Models\Amenity;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Hero;
use App\Models\Listing;
use App\Models\ListingSchedule;
use App\Models\Location;
use App\Models\OurFeature;
use App\Models\PrivacyPolicy;
use App\Models\SectionTitle;
use App\Models\TermsAndCondition;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Mail;
class FrontendController extends Controller
{
    // TTLs de caché (en segundos)
    const CACHE_HOME_TTL     = 300;   // 5 minutos - datos del home
    const CACHE_STATIC_TTL   = 3600;  // 1 hora - páginas estáticas
    const CACHE_LISTING_TTL  = 60;    // 1 minuto - detalle de proveedor

    function index() : View
    {
        $isAuthenticated = Auth::check();
        $heroCacheKey = $isAuthenticated ? 'hero_private' : 'hero_public';

        $hero = Cache::remember($heroCacheKey, self::CACHE_HOME_TTL, function () use ($isAuthenticated) {
            $type = $isAuthenticated ? 'private' : 'public';
            return Hero::where(['type' => $type, 'active' => 1])->first();
        });

        $homeData = Cache::remember('home_page_data', self::CACHE_HOME_TTL, function () {
            return [
                'sectionTitle'      => SectionTitle::first(),
                'ourFeatures'       => OurFeature::where('status', 1)->get(),
                'categories'        => Category::where('status', 1)->get(),
                'locations'         => Location::where('status', 1)->get(),
                'userCount'         => User::where(['user_type' => 'user'])->count(),
                'suppliersCount'    => Listing::count(),
                'featuredCategories' => Category::withCount(['listings' => function ($query) {
                    $query->where('is_approved', 1);
                }])->where(['show_at_home' => 1, 'status' => 1])->take(6)->get(),
                'featuredLocations' => Location::with(['listings' => function ($query) {
                    $query->where(['status' => 1, 'is_approved' => 1])->orderBy('id', 'desc');
                }])->where(['show_at_home' => 1, 'status' => 1])->get(),
                'featuredListings'  => Listing::where(['status' => 1, 'is_approved' => 1, 'is_featured' => 1])
                    ->orderBy('id', 'desc')->limit(10)->get(),
            ];
        });

        return view('frontend.home.index', array_merge($homeData, compact('hero')));
    }

    function listings(Request $request) : View {

        $listings = Listing::with(['category', 'location'])->where(['status' => 1, 'is_approved' => 1]);

        $listings->when($request->has('category') && $request->filled('category'), function($query) use ($request){
            $query->whereHas('category', function($query) use ($request) {
                $query->where('slug', $request->category);
            });
        });

        $listings->when($request->has('search') && $request->filled('search') , function($query) use ($request) {
            $query->where(function($subQuery) use ($request) {
                $subQuery->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        });

        $listings->when($request->has('location') && $request->filled('location') , function($query) use ($request) {
            $query->whereHas('location', function($subQuery) use ($request) {
                $subQuery->where('slug', $request->location);
            });
        });

        $listings->when($request->has('amenity') && is_array($request->amenity) , function($query) use ($request) {

            $amenityIds = Amenity::whereIn('slug', $request->amenity)->pluck('id');

            $query->whereHas('amenities', function($subQuery) use ($amenityIds) {
                $subQuery->whereIn('amenity_id', $amenityIds);
            });
        });

        $listings = $listings->orderByDesc('is_previliged')->orderByDesc('is_featured')->paginate(12);

        // Listas de filtros cacheadas (cambian poco)
        $categories = Cache::remember('frontend_categories_active', self::CACHE_STATIC_TTL, function () {
            return Category::where('status', 1)->get();
        });
        $locations = Cache::remember('frontend_locations_active', self::CACHE_STATIC_TTL, function () {
            return Location::where('status', 1)->get();
        });

        return view('frontend.pages.listings', compact('listings', 'categories', 'locations'));
    }

    function listingModal(string $id): string
    {
        $listing = Listing::findOrFail($id);

        return view('frontend.layouts.ajax-listing-modal', compact('listing'))->render();
    }

    function showListing(string $slug) : View {

        $listing = Cache::remember("listing_detail:{$slug}", self::CACHE_LISTING_TTL, function () use ($slug) {
            return Listing::with(['socialNetworks', 'amenities.amenity', 'schedules', 'user', 'location'])
                ->where(['status' => 1])
                ->where('slug', $slug)
                ->firstOrFail();
        });

        $openStatus = $this->listingScheduleStatus($listing);

        $smellerListings = Cache::remember("listing_related:{$listing->category_id}_{$listing->id}", self::CACHE_LISTING_TTL, function () use ($listing) {
            return Listing::where('category_id', $listing->category_id)
                ->where('id', '!=', $listing->id)
                ->orderBy('id', 'DESC')
                ->take(4)
                ->get();
        });

        return view('frontend.pages.listing-view', compact('listing', 'smellerListings', 'openStatus'));
    }

    function listingScheduleStatus(Listing $listing) : ?string {
        $openStatus = '';
        $day = ListingSchedule::where('listing_id', $listing->id)->where('day', \Str::lower(date('l')))->first();
        if($day) {
            $startTime = strtotime($day->start_time);
            $endTime = strtotime($day->end_time);
            if(time() >= $startTime && time() <= $endTime) {
                $openStatus = 'open';
            }else {
                $openStatus = 'close';
            }
        }
        return $openStatus;
    }

    function blogShow(string $slug) : View {
        $blog = Blog::with(['category', 'comments'])->where(['slug' => $slug, 'status' => 1])->firstOrFail();
        $popularBlogs = Cache::remember('blog_popular', self::CACHE_STATIC_TTL, function () use ($slug) {
            return Blog::select(['id', 'title', 'slug', 'created_at', 'image'])
                ->where('is_popular', 1)
                ->orderBy('id', 'DESC')
                ->take(5)
                ->get();
        });
        $categories = Cache::remember('blog_categories', self::CACHE_STATIC_TTL, function () {
            return BlogCategory::withCount(['blogs' => function($query){
                $query->where('status', 1);
            }])->where('status', 1)->get();
        });

        return view('frontend.pages.blog-show', compact('blog', 'categories', 'popularBlogs'));
    }

    function aboutIndex() : View {
        $aboutData = Cache::remember('about_page_data', self::CACHE_STATIC_TTL, function () {
            return [
                'sectionTitle' => SectionTitle::first(),
                'about'        => AboutUs::first(),
                'categories'   => Category::withCount(['listings' => function ($query) {
                    $query->where('is_approved', 1);
                }])->where(['show_at_home' => 1, 'status' => 1])->take(6)->get(),
                'blogs'        => Blog::where('status', 1)->orderBy('id', 'Desc')->take(2)->get(),
            ];
        });

        return view('frontend.pages.about', $aboutData);
    }

    function contactIndex() : View {
        $contact = Cache::remember('contact_info', self::CACHE_STATIC_TTL, function () {
            return Contact::first();
        });
        return view('frontend.pages.contact', compact('contact'));
    }

    function contactMessage(Request $request) : RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:50'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'max:2000']
        ]);

        Mail::to(env('MAIL_CONTACT'))->send(new ContactMail($request->name, $request->subject, $request->message));
        return back()->with('status', [
            'main_message' => '¡Mensaje enviado!',
            'description' => 'Un administrador validará sus datos y se pondra en contacto con usted.',
            'alert_type' => 'success'
        ]);
    }

    function privacyPolicy() : View {
        $privacyPolicy = Cache::remember('privacy_policy', self::CACHE_STATIC_TTL, function () {
            return PrivacyPolicy::first();
        });
        return view('frontend.pages.privacy-policy', compact('privacyPolicy'));
    }

    function termsAndCondition() : View {
        $termsAndCondition = Cache::remember('terms_and_condition', self::CACHE_STATIC_TTL, function () {
            return TermsAndCondition::first();
        });
        return view('frontend.pages.terms-and-condition', compact('termsAndCondition'));
    }
}
