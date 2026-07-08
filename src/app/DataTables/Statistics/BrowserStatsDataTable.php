<?php

namespace App\DataTables\Statistics;

use App\Models\UserSession;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class BrowserStatsDataTable extends AbstractBreakdownDataTable
{
    public function query(UserSession $model): QueryBuilder
    {
        [$from, $to] = $this->range();

        return $model->newQuery()
            ->selectRaw('browser as label, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('browser')
            ->groupBy('browser');
    }

    protected function tableId(): string
    {
        return 'browsers-stats-table';
    }

    protected function labelTitle(): string
    {
        return 'Navegador';
    }

    protected function searchColumn(): string
    {
        return 'browser';
    }

    protected function ajaxUrl(): string
    {
        return route('admin.statistics.data', 'browsers');
    }
}
