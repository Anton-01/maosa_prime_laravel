<?php

namespace App\DataTables\Statistics;

use App\Models\UserSession;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class DeviceStatsDataTable extends AbstractBreakdownDataTable
{
    public function query(UserSession $model): QueryBuilder
    {
        [$from, $to] = $this->range();

        return $model->newQuery()
            ->selectRaw('device_type as label, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('device_type')
            ->groupBy('device_type');
    }

    protected function tableId(): string
    {
        return 'devices-stats-table';
    }

    protected function labelTitle(): string
    {
        return 'Dispositivo';
    }

    protected function searchColumn(): string
    {
        return 'device_type';
    }

    protected function ajaxUrl(): string
    {
        return route('admin.statistics.data', 'devices');
    }
}
