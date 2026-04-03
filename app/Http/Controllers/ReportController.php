<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function revenue(Request $request)
    {
        $data = $request->validate([
            'from' => ['required','date'],
            'to' => ['required','date','after_or_equal:from'],
            'group_by' => ['nullable', Rule::in(['day','month','year'])]
        ]);

        $subscriberId = auth('admin')->user()->subscriber_id;

        $report = $this->reportService->revenueReport(
            $subscriberId,
            $data['from'],
            $data['to'],
            $data['group_by'] ?? 'day'
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function doctorsDue(Request $request)
    {
        $request->validate([
            'doctor_id' => 'nullable|integer|exists:doctors,id'
        ]);
        $subscriberId = auth('admin')->user()->subscriber_id;

        $report = $this->reportService->doctorsDue(
            $subscriberId,
            $request->doctor_id
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}
