<?php

use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\UserPriceTableController;
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

Route::middleware(['auth', 'track.user.activity'])->group(function () {
    Route::get('home', [FrontendController::class, 'index'])->name('home');
    Route::get('suppliers', [FrontendController::class, 'listings'])->name('listings');
    Route::get('suppliers/{slug}', [FrontendController::class, 'showListing'])->name('listing.show');
    Route::get('information/{slug}', [FrontendController::class, 'blogShow'])->name('blog.show');
    Route::get('about-us', [FrontendController::class, 'aboutIndex'])->name('about.index');
    Route::get('contact', [FrontendController::class, 'contactIndex'])->name('contact.index');
    Route::get('privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy.index');
    Route::get('terms-and-condition', [FrontendController::class, 'termsAndCondition'])->name('terms-and-condition.index');
    Route::post('contact', [FrontendController::class, 'contactMessage'])->name('contact.message')->middleware(['honeypot', 'throttle:contact']);
});

Route::group(['middleware' => ['auth', 'track.user.activity'], 'prefix' => 'user', 'as' => 'user.'], function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile-password', [ProfileController::class, 'updatePassword'])->name('profile-password.update');

    /** User Price Table Routes */
    Route::get('/price-table', [UserPriceTableController::class, 'index'])->name('price-table.index');
    Route::get('/price-table/stations', [UserPriceTableController::class, 'loadStations'])->name('price-table.stations');
    Route::get('/price-table/pdf', [UserPriceTableController::class, 'exportPdf'])->name('price-table.pdf');
    Route::get('/price-table/html', [UserPriceTableController::class, 'loadPriceHtml'])->name('price-table.html');
});

require __DIR__.'/auth.php';
