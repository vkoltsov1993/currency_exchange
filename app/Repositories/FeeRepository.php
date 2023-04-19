<?php

namespace App\Repositories;

use App\Models\ExchangeFee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FeeRepository
{
    public function getReportByDateInterval(Carbon $from, Carbon $to): Collection
    {
        return ExchangeFee::query()->whereBetween('created_at', [$from, $to])
            ->select(DB::raw("SUM(fee) as amount, currency"))
            ->groupBy('currency')
            ->get();
    }
}
