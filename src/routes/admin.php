<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DefaultPriceLegendController;
use App\Http\Controllers\Admin\FooterInfoController;
use App\Http\Controllers\Admin\FuelTerminalController;
use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Admin\ListingAmenityController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\ListingScheduleController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MenuBuilderController;
use App\Http\Controllers\Admin\OurFeatureController;
use App\Http\Controllers\Admin\PriceImportController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\RoleUserController;
use App\Http\Controllers\Admin\SectionTitleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TermsAndConditionController;
use App\Http\Controllers\Admin\TinyMCEController;
use App\Http\Controllers\Admin\UserImportController;
use App\Http\Controllers\Admin\UserPriceController;
use App\Http\Controllers\Admin\UserPriceLegendController;
use App\Http\Controllers\Admin\UserStatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login')->middleware('guest');
Route::get('/admin/forgot-password', [AdminAuthController::class, 'PasswordRequest'])->name('admin.password.request')->middleware('guest');

Route::group(['middleware' => ['auth', 'user.type:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function(){

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    /** TinyMCE Image Upload Route */
    Route::post('/upload-image', [TinyMCEController::class, 'uploadImage'])->name('upload-image');

    /** Profile Routes */
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile-password', [ProfileController::class, 'passwordUpdate'])->name('profile-password.update');

    /** Hero Routes */
    Route::get('/hero', [HeroController::class, 'index'])->name('hero.index');
    Route::put('/hero', [HeroController::class, 'update'])->name('hero.update');

    Route::get('/hero-public', [HeroController::class, 'indexPublic'])->name('hero.public.index');
    Route::put('/hero-public', [HeroController::class, 'updatePublic'])->name('hero.public.update');

    /** section title routes */
    Route::get('/section-titles', [SectionTitleController::class, 'index'])->name('section-title.index');
    Route::post('/section-titles', [SectionTitleController::class, 'update'])->name('section-title.update');


    /** Category Routes */
    Route::resource('/category', CategoryController::class);

    /** Location Routes */
    Route::resource('/location', LocationController::class);

    /** Amenity Routes */
    Route::resource('/amenity', AmenityController::class);

    /** Listing Routes */
    Route::resource('/listing', ListingController::class);

    /** Listing Schedule Routes */
    Route::get('/listing-schedule/{listing_id}', [ListingScheduleController::class, 'index'])->name('listing-schedule.index');
    Route::get('/listing-schedule/{listing_id}/create', [ListingScheduleController::class, 'create'])->name('listing-schedule.create');
    Route::post('/listing-schedule/{listing_id}', [ListingScheduleController::class, 'store'])->name('listing-schedule.store');
    Route::get('/listing-schedule/{id}/edit', [ListingScheduleController::class, 'edit'])->name('listing-schedule.edit');
    Route::put('/listing-schedule/{id}', [ListingScheduleController::class, 'update'])->name('listing-schedule.update');
    Route::delete('/listing-schedule/{id}', [ListingScheduleController::class, 'destroy'])->name('listing-schedule.destroy');

    /** Listing Amenities Routes */
    Route::get('/listing/{listing_id}/amenities', [ListingAmenityController::class, 'index'])->name('listing.amenities.index');
    Route::put('/listing/{listing_id}/amenities', [ListingAmenityController::class, 'update'])->name('listing.amenities.update');
    Route::post('/listing/{listing_id}/amenities/add', [ListingAmenityController::class, 'addAmenity'])->name('listing.amenities.add');
    Route::delete('/listing/{listing_id}/amenities/{amenity_id}', [ListingAmenityController::class, 'removeAmenity'])->name('listing.amenities.remove');
    Route::post('/listing/{listing_id}/amenities/create', [ListingAmenityController::class, 'createAmenity'])->name('listing.amenities.create');

    /** Our Feature Routes */
    Route::resource('our-features', OurFeatureController::class);

    /** Blog Routes */
    Route::resource('blog', BlogController::class);

    /** About Route */
    Route::get('about-us', [AboutController::class, 'index'])->name('about-us.index');
    Route::post('about-us', [AboutController::class, 'update'])->name('about-us.update');

    /** Contact Route */
    Route::get('contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('contact', [ContactController::class, 'update'])->name('contact.update');

    /** Privacy Policy Route */
    Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy.index');
    Route::post('privacy-policy', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update');
    /** Terms and Condition Route */
    Route::get('terms-and-condition', [TermsAndConditionController::class, 'index'])->name('terms-and-condition.index');
    Route::post('terms-and-condition', [TermsAndConditionController::class, 'update'])->name('terms-and-condition.update');
    /** Privacy Policy Route */
    Route::get('menu-builder', [MenuBuilderController::class, 'index'])->name('menu-builder.index');

    /** Footer Info Route */
    Route::get('footer-info', [FooterInfoController::class, 'index'])->name('footer-info.index');
    Route::post('footer-info', [FooterInfoController::class, 'update'])->name('footer-info.update');

    /** Role Route */
    Route::resource('role', RolePermissionController::class);
    /** Role Users Routes */
    Route::resource('role-user', RoleUserController::class);

    /** User Import Routes */
    Route::get('user-import', [UserImportController::class, 'index'])->name('user-import.index');
    Route::post('user-import', [UserImportController::class, 'import'])->name('user-import.store');
    Route::get('user-import/layout', [UserImportController::class, 'downloadLayout'])->name('user-import.layout');
    Route::get('user-import/result', [UserImportController::class, 'result'])->name('user-import.result');
    Route::get('user-import/download', [UserImportController::class, 'downloadResult'])->name('user-import.download');

    /** Settings Routes */
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/general-settings', [SettingController::class, 'updateGeneralSetting'])->name('general-settings.update');
    Route::post('/logo-settings', [SettingController::class, 'logoSettings'])->name('logo-settings.update');
    Route::post('/appearance-settings', [SettingController::class, 'appearanceSetting'])->name('appearance-settings.update');

    /** Fuel Terminal Routes */
    Route::resource('fuel-terminal', FuelTerminalController::class);

    /** User Price Routes */
    Route::resource('user-price', UserPriceController::class);

    /** Price Import Routes */
    Route::get('price-import', [PriceImportController::class, 'index'])->name('price-import.index');
    Route::post('price-import', [PriceImportController::class, 'import'])->name('price-import.store');
    Route::get('price-import/layout', [PriceImportController::class, 'downloadLayout'])->name('price-import.layout');

    /** Default Price Legend Routes */
    Route::resource('default-legend', DefaultPriceLegendController::class);

    /** User Price Legend Routes */
    Route::get('user-legend', [UserPriceLegendController::class, 'index'])->name('user-legend.index');
    Route::post('user-legend', [UserPriceLegendController::class, 'store'])->name('user-legend.store');
    Route::put('user-legend/{id}', [UserPriceLegendController::class, 'update'])->name('user-legend.update');
    Route::delete('user-legend/{id}', [UserPriceLegendController::class, 'destroy'])->name('user-legend.destroy');
    Route::post('user-legend/copy-defaults', [UserPriceLegendController::class, 'copyDefaults'])->name('user-legend.copy-defaults');

    /** User Statistics Routes */
    Route::get('statistics', [UserStatisticsController::class, 'index'])->name('statistics.index');
    Route::get('statistics/user/{userId}', [UserStatisticsController::class, 'show'])->name('statistics.show');
    Route::get('statistics/user/{userId}/sessions', [UserStatisticsController::class, 'sessions'])->name('statistics.sessions');
    Route::get('statistics/session/{sessionId}', [UserStatisticsController::class, 'sessionDetail'])->name('statistics.session-detail');
    Route::get('statistics/user/{userId}/activities', [UserStatisticsController::class, 'activities'])->name('statistics.activities');

    /** Toggle User Price Table Access */
    Route::post('role-user/{id}/toggle-price-table', [RoleUserController::class, 'togglePriceTable'])->name('role-user.toggle-price-table');
});
