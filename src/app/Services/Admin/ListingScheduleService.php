<?php

namespace App\Services\Admin;

use App\Models\ListingSchedule;
use Illuminate\Support\Collection;

class ListingScheduleService
{
    /**
     * Schedules of a listing shaped for the frontend table, ordered by
     * the configured week days.
     */
    public function listForListing(int $listingId): Collection
    {
        $dayOrder = array_flip(config('listing-schedule.days', []));

        return ListingSchedule::where('listing_id', $listingId)
            ->get()
            ->sortBy(fn (ListingSchedule $schedule) => $dayOrder[$schedule->day] ?? 99)
            ->values()
            ->map(fn (ListingSchedule $schedule) => [
                'id' => $schedule->id,
                'day' => $schedule->day,
                'startTime' => $schedule->start_time,
                'endTime' => $schedule->end_time,
                'status' => (int) $schedule->status,
            ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(int $listingId, array $data): ListingSchedule
    {
        $schedule = new ListingSchedule();
        $schedule->listing_id = $listingId;
        $this->fill($schedule, $data);
        $schedule->save();

        return $schedule;
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(int $id, array $data): ListingSchedule
    {
        $schedule = ListingSchedule::findOrFail($id);
        $this->fill($schedule, $data);
        $schedule->save();

        return $schedule;
    }

    public function delete(int $id): void
    {
        ListingSchedule::findOrFail($id)->delete();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fill(ListingSchedule $schedule, array $data): void
    {
        $schedule->day = $data['day'];
        $schedule->start_time = $data['start_time'];
        $schedule->end_time = $data['end_time'];
        $schedule->status = $data['status'];
    }
}
