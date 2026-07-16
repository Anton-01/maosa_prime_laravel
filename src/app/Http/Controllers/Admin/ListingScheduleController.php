<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListingScheduleStoreRequest;
use App\Http\Requests\Admin\ListingScheduleUpdateRequest;
use App\Models\Listing;
use App\Models\ListingSchedule;
use App\Services\Admin\ListingScheduleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ListingScheduleController extends Controller
{
    public function __construct(private readonly ListingScheduleService $listingScheduleService)
    {
        $this->middleware(['permission:access management suppliers']);
    }

    public function index(string $listingId): Response
    {
        $listing = Listing::select('id', 'title')->findOrFail($listingId);

        return Inertia::render('Admin/Listings/Schedules', [
            'listing' => ['id' => $listing->id, 'title' => $listing->title],
            'schedules' => $this->listingScheduleService->listForListing((int) $listingId),
            'days' => config('listing-schedule.days', []),
            'urls' => [
                'store' => route('admin.listing-schedule.store', $listingId),
                'itemsBase' => url('admin/listing-schedule'),
                'listings' => route('admin.listing.index'),
            ],
        ]);
    }

    /**
     * Legacy URL kept for old bookmarks: schedules are now managed in a
     * modal inside the index screen.
     */
    public function create(string $listingId): RedirectResponse
    {
        return to_route('admin.listing-schedule.index', $listingId);
    }

    public function store(ListingScheduleStoreRequest $request, string $listingId): RedirectResponse
    {
        $this->listingScheduleService->create((int) $listingId, $request->validated());

        return back()->with('success', '¡Horario creado correctamente!');
    }

    public function edit(string $id): RedirectResponse
    {
        $schedule = ListingSchedule::findOrFail($id);

        return to_route('admin.listing-schedule.index', $schedule->listing_id);
    }

    public function update(ListingScheduleUpdateRequest $request, string $id): RedirectResponse
    {
        $this->listingScheduleService->update((int) $id, $request->validated());

        return back()->with('success', '¡Horario actualizado correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->listingScheduleService->delete((int) $id);

        return back()->with('success', '¡Horario eliminado correctamente!');
    }
}
