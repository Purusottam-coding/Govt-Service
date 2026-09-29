<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApprovedDocumentController extends Controller
{
    /**
     * Display a listing of citizen's approved documents and certificates with live search.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Query only applications belonging to the logged-in citizen that are approved or have an approved document
        $query = Application::with(['service.department'])
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'approved')
                    ->orWhereNotNull('approved_document_path');
            });

        // Search query across Application Number, Unique Certificate ID (e.g. ABC123), Document Title, or Service Name
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('certificate_number', 'like', "%{$search}%")
                    ->orWhere('approved_document_name', 'like', "%{$search}%")
                    ->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Department
        if ($request->filled('department_id')) {
            $departmentId = $request->department_id;
            $query->whereHas('service', function ($serviceQuery) use ($departmentId) {
                $serviceQuery->where('department_id', $departmentId);
            });
        }

        // Filter by Year
        if ($request->filled('year')) {
            $year = (int) $request->year;
            $query->whereYear('issued_at', $year);
        }

        $documents = $query->orderByDesc('issued_at')
            ->orderByDesc('processed_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $departments = Department::where('status', true)->get();

        // Quick stats
        $totalApprovedCount = Application::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'approved')
                    ->orWhereNotNull('approved_document_path');
            })->count();

        return view('citizen.approved-documents.index', compact('documents', 'departments', 'totalApprovedCount'));
    }

    /**
     * View the approved document inline in browser.
     */
    public function viewDocument(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403, 'अनधिकृत पहुँच: यो कागजात तपाईंको खातासँग सम्बन्धित छैन।');
        }

        if (!$application->hasApprovedDocument() || !Storage::disk('public')->exists($application->approved_document_path)) {
            return back()->with('error', 'प्रमाणित कागजात फेला परेन। कृपया कार्यालय वा प्रशासकलाई सम्पर्क गर्नुहोस्।');
        }

        return Storage::disk('public')->response($application->approved_document_path);
    }

    /**
     * Download the approved document.
     */
    public function download(Application $application): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        if ($application->user_id !== auth()->id()) {
            abort(403, 'अनधिकृत पहुँच: यो कागजात तपाईंको खातासँग सम्बन्धित छैन।');
        }

        if (!$application->hasApprovedDocument() || !Storage::disk('public')->exists($application->approved_document_path)) {
            return back()->with('error', 'प्रमाणित कागजात फेला परेन। कृपया कार्यालय वा प्रशासकलाई सम्पर्क गर्नुहोस्।');
        }

        $filename = Str::slug($application->approved_document_name ?: $application->service->name, '-') .
            '-' . ($application->certificate_number ?: $application->application_number) .
            '.' . ($application->approved_document_type ?: 'pdf');

        return Storage::disk('public')->download($application->approved_document_path, $filename);
    }
}
