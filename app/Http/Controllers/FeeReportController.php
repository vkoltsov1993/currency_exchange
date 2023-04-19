<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeeReportRequest;
use App\Http\Resources\FeeResource;
use App\Repositories\FeeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeeReportController extends Controller
{
    public function __invoke(FeeRepository $feeRepository, FeeReportRequest $request)
    {
        $data = $request->validated();
        $dateFrom = Carbon::parse($data['date_from']);
        $dateTo = Carbon::parse($data['date_to']);

        $feeCollection = $feeRepository->getReportByDateInterval($dateFrom, $dateTo);
        return FeeResource::collection($feeCollection);
    }
}
