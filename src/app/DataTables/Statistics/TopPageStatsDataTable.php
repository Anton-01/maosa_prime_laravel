<?php

namespace App\DataTables\Statistics;

use App\Models\PageVisit;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TopPageStatsDataTable extends AbstractBreakdownDataTable
{
    public function query(PageVisit $model): QueryBuilder
    {
        [$from, $to] = $this->range();

        return $model->newQuery()
            ->selectRaw('url as label, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('url')
            ->groupBy('url');
    }

    protected function tableId(): string
    {
        return 'pages-stats-table';
    }

    protected function labelTitle(): string
    {
        return 'Página (URL)';
    }

    protected function searchColumn(): string
    {
        return 'url';
    }

    protected function ajaxUrl(): string
    {
        return route('admin.statistics.data', 'pages');
    }
}
