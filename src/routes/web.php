<?php

use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Frontend\PrecioPemexController;
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

// Selector de idioma: disponible con o sin sesión iniciada.
Route::post('locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

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

/*
 * Submódulo "Precios PEMEX" (REQ-04 a REQ-07).
 *
 * Los endpoints de layout se sirven desde la aplicación (el middleware
 * `precios.pemex` filtra por el permiso) y trabajan por lote: reciben
 * `estaciones[]` y `fecha_vigencia`, resuelven todas las estaciones contra la
 * API externa y devuelven el resultado ya armado en una sola respuesta.
 *
 *   GET /api/precio_pemex/layout/{HTML|Excel|pdf|imagen}
 *       ?estaciones[]=1&estaciones[]=2&fecha_vigencia=YYYY-MM-DD
 */
Route::middleware(['auth', 'precios.pemex'])->group(function () {
    Route::get('user/precios-pemex', [PrecioPemexController::class, 'index'])
        ->middleware('track.user.activity')
        ->name('user.precio-pemex.index');

    Route::prefix('api/precio_pemex/layout')->group(function () {
        Route::get('HTML', [PrecioPemexController::class, 'html'])->name('user.precio-pemex.html');
        Route::get('Excel', [PrecioPemexController::class, 'excel'])->name('user.precio-pemex.excel');
        Route::get('pdf', [PrecioPemexController::class, 'pdf'])->name('user.precio-pemex.pdf');
        Route::get('imagen', [PrecioPemexController::class, 'imagen'])->name('user.precio-pemex.imagen');
    });
});

require __DIR__.'/auth.php';
