<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class InactiveUserService
{
    /**
     * Inactive users (no session activity since N days) shaped for the
     * frontend table.
     */
    public function list(int $days): Collection
    {
        return $this->query($days)
            ->orderBy('users.name')
            ->get()
            ->map(function (User $user) {
                $lastSession = $user->last_session_at ? Carbon::parse($user->last_session_at) : null;
                $reference = $lastSession ?? $user->created_at;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                    'lastSessionAt' => $lastSession?->format('d/m/Y H:i'),
                    'lastSessionLocation' => $user->last_session_location ?: null,
                    'daysInactive' => (int) $reference->diffInDays(now()),
                    'createdAt' => $user->created_at?->format('d/m/Y'),
                ];
            });
    }

    /**
     * Delete the given users, skipping the current admin and Super Admins.
     *
     * @param array<int, int> $userIds
     * @return array{deleted: int, skipped: array<int, string>}
     */
    public function destroy(array $userIds): array
    {
        $users = User::whereIn('id', $userIds)->get();

        $deleted = 0;
        $skipped = [];

        foreach ($users as $user) {
            if ($user->id === auth()->id() || $user->hasRole('Super Admin')) {
                $skipped[] = $user->name;

                continue;
            }

            $user->delete();
            $deleted++;
        }

        return ['deleted' => $deleted, 'skipped' => $skipped];
    }

    /**
     * Base query for inactive users, shared with the Excel export.
     */
    public function query(int $days): Builder
    {
        $cutoff = now()->subDays($days);

        return User::query()
            ->select('users.*')
            ->selectRaw('last_session.last_session_at')
            ->selectRaw("NULLIF(CONCAT_WS(', ', last_session.city, last_session.region, last_session.country), '') as last_session_location")
            ->withLastSession()
            ->inactiveSince($cutoff)
            ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'Super Admin'))
            ->where('users.id', '!=', auth()->id());
    }
}
