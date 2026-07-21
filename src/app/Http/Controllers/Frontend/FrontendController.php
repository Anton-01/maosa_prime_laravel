<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\AboutUs;
use App\Models\Amenity;
use App\Models\Blog;
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
use Inertia\Inertia;
use Inertia\Response;
use Mail;

class FrontendController extends Controller
{
    // TTLs de caché (en segundos)
    const CACHE_HOME_TTL     = 300;   // 5 minutos - datos del home
    const CACHE_STATIC_TTL   = 3600;  // 1 hora - páginas estáticas
    const CACHE_LISTING_TTL  = 60;    // 1 minuto - detalle de proveedor

    function index(): Response
    {
        // Guest users get a standalone full-screen landing (no navbar/footer).
        if (!Auth::check()) {
            $hero = Cache::remember('hero_public', self::CACHE_HOME_TTL, function () {
                return Hero::where(['type' => 'public', 'active' => 1])->first();
            });

            return Inertia::render('Public/GuestLanding', ['hero' => $hero]);
        }

        $hero = Cache::remember('hero_private', self::CACHE_HOME_TTL, function () {
            return Hero::where(['type' => 'private', 'active' => 1])->first();
        });

        $homeData = Cache::remember('home_page_data_v2', self::CACHE_HOME_TTL, function () {
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
                    $query->with('category')->where(['status' => 1, 'is_approved' => 1])->orderBy('id', 'desc');
                }])->where(['show_at_home' => 1, 'status' => 1])->get(),
                'featuredListings'  => Listing::with(['category', 'location'])
                    ->where(['status' => 1, 'is_approved' => 1, 'is_featured' => 1])
                    ->orderBy('id', 'desc')->limit(10)->get(),
            ];
        });

        return Inertia::render('Public/Home', array_merge($homeData, ['hero' => $hero]));
    }

    function listings(Request $request): Response
    {
        $listings = Listing::with(['category', 'location'])->where(['status' => 1, 'is_approved' => 1]);

        $listings->when($request->filled('category'), function ($query) use ($request) {
            $query->whereHas('category', function ($query) use ($request) {
                $query->where('slug', $request->category);
            });
        });

        $listings->when($request->filled('search'), function ($query) use ($request) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        });

        $listings->when($request->filled('location'), function ($query) use ($request) {
            $query->whereHas('location', function ($subQuery) use ($request) {
                $subQuery->where('slug', $request->location);
            });
        });

        $listings->when($request->has('amenity') && is_array($request->amenity), function ($query) use ($request) {
            $amenityIds = Amenity::whereIn('slug', $request->amenity)->pluck('id');
            $query->whereHas('amenities', function ($subQuery) use ($amenityIds) {
                $subQuery->whereIn('amenity_id', $amenityIds);
            });
        });

        $listings = $listings->orderByDesc('is_previliged')->orderByDesc('is_featured')
            ->paginate(12)->withQueryString();

        $categories = Cache::remember('frontend_categories_active', self::CACHE_STATIC_TTL, function () {
            return Category::where('status', 1)->get();
        });
        $locations = Cache::remember('frontend_locations_active', self::CACHE_STATIC_TTL, function () {
            return Location::where('status', 1)->get();
        });

        return Inertia::render('Public/Listings', [
            'listings' => $listings,
            'categories' => $categories,
            'locations' => $locations,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'location' => $request->location,
            ],
        ]);
    }

    function showListing(string $slug): Response
    {
        $listing = Cache::remember("listing_detail:{$slug}", self::CACHE_LISTING_TTL, function () use ($slug) {
            return Listing::with(['socialNetworks', 'amenities.amenity', 'schedules', 'user', 'location', 'category'])
                ->where(['status' => 1])
                ->where('slug', $slug)
                ->firstOrFail();
        });

        $openStatus = $this->listingScheduleStatus($listing);

        $relatedListings = Cache::remember("listing_related:{$listing->category_id}_{$listing->id}", self::CACHE_LISTING_TTL, function () use ($listing) {
            return Listing::where('category_id', $listing->category_id)
                ->where('id', '!=', $listing->id)
                ->orderBy('id', 'DESC')
                ->take(4)
                ->get();
        });

        return Inertia::render('Public/ListingView', [
            'listing' => $listing,
            'relatedListings' => $relatedListings,
            'openStatus' => $openStatus,
        ]);
    }

    function listingScheduleStatus(Listing $listing): ?string
    {
        $openStatus = '';
        $day = ListingSchedule::where('listing_id', $listing->id)->where('day', \Str::lower(date('l')))->first();
        if ($day) {
            $startTime = strtotime($day->start_time);
            $endTime = strtotime($day->end_time);
            if (time() >= $startTime && time() <= $endTime) {
                $openStatus = 'open';
            } else {
                $openStatus = 'close';
            }
        }
        return $openStatus;
    }

    function blogShow(string $slug): Response
    {
        $blog = Blog::with('author')->where(['slug' => $slug, 'status' => 1])->firstOrFail();
        $popularBlogs = Cache::remember('blog_popular', self::CACHE_STATIC_TTL, function () {
            return Blog::select(['id', 'title', 'slug', 'created_at', 'image'])
                ->where('is_popular', 1)
                ->orderBy('id', 'DESC')
                ->take(5)
                ->get();
        });

        return Inertia::render('Public/BlogShow', [
            'blog' => $blog,
            'popularBlogs' => $popularBlogs,
        ]);
    }

    function aboutIndex(): Response
    {
        $aboutData = Cache::remember('about_page_data_v2', self::CACHE_STATIC_TTL, function () {
            return [
                'sectionTitle' => SectionTitle::first(),
                'about'        => AboutUs::first(),
                'categories'   => Category::withCount(['listings' => function ($query) {
                    $query->where('is_approved', 1);
                }])->where(['show_at_home' => 1, 'status' => 1])->take(6)->get(),
                'blogs'        => Blog::with('author')->where('status', 1)->orderBy('id', 'Desc')->take(2)->get(),
            ];
        });

        return Inertia::render('Public/About', array_merge($aboutData, [
            'aboutVideoThumbnail' => getYtThumbnail($aboutData['about']?->video_url),
        ]));
    }

    function contactIndex(): Response
    {
        $contact = Cache::remember('contact_info', self::CACHE_STATIC_TTL, function () {
            return Contact::first();
        });

        return Inertia::render('Public/Contact', ['contact' => $contact]);
    }

    function contactMessage(Request $request): RedirectResponse
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

    function privacyPolicy(): Response
    {
        $privacyPolicy = Cache::remember('privacy_policy', self::CACHE_STATIC_TTL, function () {
            return PrivacyPolicy::first();
        });

        return Inertia::render('Public/PrivacyPolicy', ['privacyPolicy' => $privacyPolicy]);
    }

    function termsAndCondition(): Response
    {
        $termsAndCondition = Cache::remember('terms_and_condition', self::CACHE_STATIC_TTL, function () {
            return TermsAndCondition::first();
        });

        return Inertia::render('Public/Terms', ['termsAndCondition' => $termsAndCondition]);
    }
}
