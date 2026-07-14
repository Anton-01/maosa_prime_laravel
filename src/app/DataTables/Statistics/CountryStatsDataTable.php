<?php

namespace App\DataTables\Statistics;

use App\Models\UserSession;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class CountryStatsDataTable extends AbstractBreakdownDataTable
{
    public function query(UserSession $model): QueryBuilder
    {
        [$from, $to] = $this->range();

        return $model->newQuery()
            ->selectRaw('country as label, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('country')
            ->groupBy('country');
    }

    protected function tableId(): string
    {
        return 'countries-stats-table';
    }

    protected function labelTitle(): string
    {
        return 'País';
    }

    protected function searchColumn(): string
    {
        return 'country';
    }

    protected function ajaxUrl(): string
    {
        return route('admin.statistics.data', 'countries');
    }
}
