<?php

use App\Http\Controllers\Frontend\AgentListingController;
use App\Http\Controllers\Frontend\AgentListingImageGalleryController;
use App\Http\Controllers\Frontend\AgentListingScheduleController;
use App\Http\Controllers\Frontend\AgentListingVideoGalleryController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FrontendController::class, 'index'])->name('start');

Route::middleware(['log.page.visit'])->group(function () {
    Route::get('/', [FrontendController::class, 'index'])->name('home');
    Route::get('suppliers', [FrontendController::class, 'listings'])->name('listings');
    Route::get('suppliers-modal/{id}', [FrontendController::class, 'listingModal'])->name('listing-modal');
    Route::get('suppliers/{slug}', [FrontendController::class, 'showListing'])->name('listing.show');
    Route::get('information/{slug}', [FrontendController::class, 'blogShow'])->name('blog.show');
    Route::get('about-us', [FrontendController::class, 'aboutIndex'])->name('about.index');
    Route::get('contact', [FrontendController::class, 'contactIndex'])->name('contact.index');
    Route::get('privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy.index');
    Route::get('terms-and-condition', [FrontendController::class, 'termsAndCondition'])->name('terms-and-condition.index');
    Route::post('contact', [FrontendController::class, 'contactMessage'])->name('contact.message')->middleware('honeypot');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('home', [FrontendController::class, 'index'])->name('home');
    Route::get('suppliers', [FrontendController::class, 'listings'])->name('listings');
    Route::get('suppliers-modal/{id}', [FrontendController::class, 'listingModal'])->name('listing-modal');
    Route::get('suppliers/{slug}', [FrontendController::class, 'showListing'])->name('listing.show');
    Route::get('information/{slug}', [FrontendController::class, 'blogShow'])->name('blog.show');
    Route::get('about-us', [FrontendController::class, 'aboutIndex'])->name('about.index');
    Route::get('contact', [FrontendController::class, 'contactIndex'])->name('contact.index');
    Route::get('privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy.index');
    Route::get('terms-and-condition', [FrontendController::class, 'termsAndCondition'])->name('terms-and-condition.index');
    Route::post('contact', [FrontendController::class, 'contactMessage'])->name('contact.message')->middleware('honeypot');
});

Route::group(['middleware' => 'auth', 'prefix' => 'user', 'as' => 'user.'], function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile-password', [ProfileController::class, 'updatePassword'])->name('profile-password.update');

    Route::resource('/listing', AgentListingController::class);

    Route::resource('/listing-image-gallery', AgentListingImageGalleryController::class);
    Route::resource('/listing-video-gallery', AgentListingVideoGalleryController::class);

    Route::get('/listing-schedule/{listing_id}', [AgentListingScheduleController::class, 'index'])->name('listing-schedule.index');
    Route::get('/listing-schedule/{listing_id}/create', [AgentListingScheduleController::class, 'create'])->name('listing-schedule.create');
    Route::post('/listing-schedule/{listing_id}', [AgentListingScheduleController::class, 'store'])->name('listing-schedule.store');
    Route::get('/listing-schedule/{id}/edit', [AgentListingScheduleController::class, 'edit'])->name('listing-schedule.edit');
    Route::put('/listing-schedule/{id}', [AgentListingScheduleController::class, 'update'])->name('listing-schedule.update');
    Route::delete('/listing-schedule/{id}', [AgentListingScheduleController::class, 'destroy'])->name('listing-schedule.destroy');

});


Route::get('/clear-config-maosa-prime-admin-adjust', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:cache');
    Artisan::call('view:cache');
    return 'Configuración limpia';
});



require __DIR__.'/auth.php';
