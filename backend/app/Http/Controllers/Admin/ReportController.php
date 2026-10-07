<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Department;
use App\Models\Payment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $departmentId = $request->input('department_id');
        $status = $request->input('status');

        // Base application query filtered by date and optional params
        $appQuery = Application::with(['service.department', 'payment'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($departmentId) {
            $appQuery->whereHas('service', fn($q) => $q->where('department_id', $departmentId));
        }

        if ($status) {
            $appQuery->where('status', $status);
        }

        $filteredApplications = (clone $appQuery)->get();

        // 1. Core KPIs
        $totalApps = $filteredApplications->count();
        $approvedApps = $filteredApplications->whereIn('status', ['approved', 'completed'])->count();
        $pendingApps = $filteredApplications->whereIn('status', ['pending', 'under_review'])->count();
        $rejectedApps = $filteredApplications->where('status', 'rejected')->count();
        $completionRate = $totalApps > 0 ? round(($approvedApps / $totalApps) * 100, 1) : 0;

        // Revenue queries
        $revenueQuery = Payment::query()
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        $pendingRevenueQuery = Payment::query()
            ->where('status', 'pending')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($departmentId) {
            $revenueQuery->whereHas('application.service', fn($q) => $q->where('department_id', $departmentId));
            $pendingRevenueQuery->whereHas('application.service', fn($q) => $q->where('department_id', $departmentId));
        }

        $totalRevenue = (float) $revenueQuery->sum('amount');
        $pendingRevenue = (float) $pendingRevenueQuery->sum('amount');

        // 2. Department-wise Performance Breakdown
        $allDepartments = Department::orderBy('name')->get();
        $departmentStats = [];

        foreach ($allDepartments as $dept) {
            $deptApps = $filteredApplications->filter(fn($app) => $app->service && $app->service->department_id === $dept->id);
            $deptAppCount = $deptApps->count();
            $deptApproved = $deptApps->whereIn('status', ['approved', 'completed'])->count();
            $deptPending = $deptApps->whereIn('status', ['pending', 'under_review'])->count();
            $deptRejected = $deptApps->where('status', 'rejected')->count();

            $deptRevenue = Payment::where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->whereHas('application.service', fn($q) => $q->where('department_id', $dept->id))
                ->sum('amount');

            $departmentStats[] = [
                'id' => $dept->id,
                'name' => $dept->name,
                'code' => $dept->code ?? 'N/A',
                'total_applications' => $deptAppCount,
                'approved' => $deptApproved,
                'pending' => $deptPending,
                'rejected' => $deptRejected,
                'revenue' => (float) $deptRevenue,
            ];
        }

        // 3. Service-wise Breakdown (Top Services)
        $serviceStats = [];
        $services = Service::with('department')->get();
        foreach ($services as $srv) {
            $srvApps = $filteredApplications->filter(fn($app) => $app->service_id === $srv->id);
            $srvCount = $srvApps->count();
            $srvRevenue = Payment::where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->whereHas('application', fn($q) => $q->where('service_id', $srv->id))
                ->sum('amount');

            if ($srvCount > 0 || $srvRevenue > 0) {
                $serviceStats[] = [
                    'name' => $srv->name,
                    'department' => $srv->department ? $srv->department->name : 'N/A',
                    'fee' => (float) $srv->fee,
                    'count' => $srvCount,
                    'revenue' => (float) $srvRevenue,
                ];
            }
        }
        usort($serviceStats, fn($a, $b) => $b['count'] <=> $a['count']);

        // 4. Daily Trends for Chart.js (Applications & Revenue)
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        $trendDates = [];
        $trendAppCounts = [];
        $trendRevenues = [];

        // Group by day for ranges up to 60 days
        $period = $start->diffInDays($end);
        $step = $period > 60 ? 'month' : 'day';

        $current = $start->copy();
        while ($current->lte($end)) {
            $dStr = $current->toDateString();
            $trendDates[] = $current->format('M d');

            $dayApps = $filteredApplications->filter(fn($app) => $app->created_at->toDateString() === $dStr)->count();
            $trendAppCounts[] = $dayApps;

            $dayRev = Payment::where('status', 'completed')
                ->whereDate('created_at', $dStr)
                ->when($departmentId, fn($q) => $q->whereHas('application.service', fn($sq) => $sq->where('department_id', $departmentId)))
                ->sum('amount');
            $trendRevenues[] = (float) $dayRev;

            $current->addDay();
        }

        // 5. Status Distribution for Donut Chart
        $statusDistribution = [
            'pending' => $filteredApplications->where('status', 'pending')->count(),
            'under_review' => $filteredApplications->where('status', 'under_review')->count(),
            'approved' => $filteredApplications->where('status', 'approved')->count(),
            'rejected' => $filteredApplications->where('status', 'rejected')->count(),
            'completed' => $filteredApplications->where('status', 'completed')->count(),
        ];

        return view('admin.reports.index', compact(
            'dateFrom',
            'dateTo',
            'departmentId',
            'status',
            'allDepartments',
            'totalApps',
            'approvedApps',
            'pendingApps',
            'rejectedApps',
            'completionRate',
            'totalRevenue',
            'pendingRevenue',
            'departmentStats',
            'serviceStats',
            'trendDates',
            'trendAppCounts',
            'trendRevenues',
            'statusDistribution'
        ));
    }

    /**
     * Export analytics data as UTF-8 BOM CSV (Excel & Nepali Unicode compatible).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $exportType = $request->input('type', 'applications');
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $departmentId = $request->input('department_id');
        $status = $request->input('status');

        $filename = 'barhadashi_report_' . $exportType . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($exportType, $dateFrom, $dateTo, $departmentId, $status) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM to ensure proper Devanagari character display in Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            if ($exportType === 'departments') {
                // Department summary CSV
                fputcsv($file, [
                    'क्र.सं.',
                    'शाखाको नाम (Department Name)',
                    'शाखा कोड (Code)',
                    'कुल निवेदन (Total Applications)',
                    'स्वीकृत (Approved)',
                    'पेन्डिङ (Pending)',
                    'अस्वीकृत (Rejected)',
                    'संकलित राजस्व रु. (Revenue NPR)',
                ]);

                $departments = Department::orderBy('name')->get();
                $i = 1;
                foreach ($departments as $dept) {
                    $apps = Application::whereDate('created_at', '>=', $dateFrom)
                        ->whereDate('created_at', '<=', $dateTo)
                        ->whereHas('service', fn($q) => $q->where('department_id', $dept->id));

                    $total = (clone $apps)->count();
                    $approved = (clone $apps)->whereIn('status', ['approved', 'completed'])->count();
                    $pending = (clone $apps)->whereIn('status', ['pending', 'under_review'])->count();
                    $rejected = (clone $apps)->where('status', 'rejected')->count();

                    $revenue = Payment::where('status', 'completed')
                        ->whereDate('created_at', '>=', $dateFrom)
                        ->whereDate('created_at', '<=', $dateTo)
                        ->whereHas('application.service', fn($q) => $q->where('department_id', $dept->id))
                        ->sum('amount');

                    fputcsv($file, [
                        $i++,
                        $dept->name,
                        $dept->code ?? '-',
                        $total,
                        $approved,
                        $pending,
                        $rejected,
                        number_format($revenue, 2),
                    ]);
                }
            } else {
                // Applications detailed CSV
                fputcsv($file, [
                    'क्र.सं.',
                    'निवेदन नं. (Application No)',
                    'प्रमाणपत्र नं. (Certificate No)',
                    'आवेदकको नाम (Applicant Name)',
                    'सम्पर्क नम्बर (Phone)',
                    'शाखा (Department)',
                    'सेवाको नाम (Service)',
                    'दस्तुर रु. (Fee NPR)',
                    'भुक्तानी स्थिति (Payment Status)',
                    'निवेदन स्थिति (Application Status)',
                    'दर्ता मिति (Submission Date)',
                ]);

                $query = Application::with(['service.department', 'payment'])
                    ->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo);

                if ($departmentId) {
                    $query->whereHas('service', fn($q) => $q->where('department_id', $departmentId));
                }
                if ($status) {
                    $query->where('status', $status);
                }

                $i = 1;
                $query->chunk(100, function ($applications) use ($file, &$i) {
                    foreach ($applications as $app) {
                        fputcsv($file, [
                            $i++,
                            $app->application_number,
                            $app->certificate_number ?? '-',
                            $app->applicant_name,
                            $app->applicant_phone,
                            $app->service?->department?->name ?? '-',
                            $app->service?->name ?? '-',
                            $app->service?->fee ?? '0.00',
                            $app->payment?->status ? ucfirst($app->payment->status) : 'Pending',
                            $app->getStatusLabel(),
                            $app->created_at->format('Y-m-d H:i'),
                        ]);
                    }
                });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
