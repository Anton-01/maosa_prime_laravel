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
use Illuminate\View\View;
use Mail;
use Illuminate\Support\Facades\Auth;
class FrontendController extends Controller
{
    function index() : View
    {
        $sectionTitle = SectionTitle::first();
        $hero = null;
        if (Auth::check()) {
            $hero = Hero::where(['type' => 'private', 'active' => 1])->first();
        } else {
            $hero = Hero::where(['type' => 'public', 'active' => 1])->first();
        }
        $ourFeatures = OurFeature::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        $locations = Location::where('status', 1)->get();
        $userCount = User::where(['user_type' => 'user'])->count();
        $suppliersCount = Listing::count();

        $featuredCategories = Category::withCount(['listings'=> function($query){
            $query->where('is_approved', 1);
        }])->where(['show_at_home' => 1, 'status' => 1])->take(6)->get();

        // Featured location
        $featuredLocations = Location::with(['listings' => function($query) {
            $query->where(['status' => 1, 'is_approved' => 1])->orderBy('id', 'desc');
        }])->where(['show_at_home' => 1, 'status' => 1])->get();

        // featured listings
        $featuredListings = Listing::where(['status' => 1, 'is_approved' => 1, 'is_featured' => 1])
            ->orderBy('id', 'desc')->limit(10)->get();

        return view('frontend.home.index',
            compact('hero', 'categories', 'featuredCategories', 'featuredLocations', 'featuredListings', 'locations',
                'ourFeatures', 'sectionTitle', 'userCount', 'suppliersCount'
            ));
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

        $categories = Category::where('status', 1)->get();
        $locations = Location::where('status', 1)->get();

        return view('frontend.pages.listings', compact('listings', 'categories', 'locations'));
    }

    function listingModal(string $id): string
    {
        $listing = Listing::findOrFail($id);

        return view('frontend.layouts.ajax-listing-modal', compact('listing'))->render();
    }

    function showListing(string $slug) : View {

        $listing = Listing::where(['status' => 1])->where('slug', $slug)->firstOrFail();

        $openStatus = $this->listingScheduleStatus($listing);
        $smellerListings = Listing::where('category_id', $listing->category_id)->where('id', '!=', $listing->id)->orderBy('id', 'DESC')->take(4)->get();

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
        $popularBlogs = Blog::select(['id', 'title', 'slug', 'created_at', 'image'])->where('id', '!=', $blog->id)
            ->where('is_popular', 1)->orderBy('id', 'DESC')->take(5)->get();
        $categories = BlogCategory::withCount(['blogs' => function($query){
            $query->where('status', 1);
        }])->where('status', 1)->get();

        return view('frontend.pages.blog-show', compact('blog', 'categories', 'popularBlogs'));
    }

    function aboutIndex() : View {
        $sectionTitle = SectionTitle::first();
        $about = AboutUs::first();
        $categories= Category::withCount(['listings'=> function($query){
            $query->where('is_approved', 1);
        }])->where(['show_at_home' => 1, 'status' => 1])->take(6)->get();
        $blogs = Blog::where('status', 1)->orderBy('id', 'Desc')->take(2)->get();

        return view('frontend.pages.about', compact('about', 'blogs', 'categories', 'sectionTitle'));
    }

    function contactIndex() : View {
        $contact = Contact::first();
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
        $privacyPolicy = PrivacyPolicy::first();
        return view('frontend.pages.privacy-policy', compact('privacyPolicy'));
    }

    function termsAndCondition() : View {
        $termsAndCondition = TermsAndCondition::first();
        return view('frontend.pages.terms-and-condition', compact('termsAndCondition'));
    }
}
