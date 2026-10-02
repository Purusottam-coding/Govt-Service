<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Service;
use Illuminate\Http\Request;

use App\Notifications\PortalNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['user', 'service.department', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%")
                  ->orWhere('applicant_email', 'like', "%{$search}%")
                  ->orWhere('certificate_number', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(12);
        $services = Service::where('status', true)->get();

        return view('admin.applications.index', compact('applications', 'services'));
    }

    public function show(Application $application)
    {
        $application->load(['user', 'service.department', 'documents', 'payment']);
        $dept = $application->service?->department;
        $suggestedCertificateId = $application->certificate_number ?: Application::generateUniqueCertificateId($dept);
        return view('admin.applications.show', compact('application', 'suggestedCertificateId'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $application->load('service.department');
        $dept = $application->service?->department;

        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,completed',
            'admin_remarks' => 'nullable|string|max:1000',
            'approved_document_name' => [
                Rule::requiredIf(fn() => $request->input('status') === 'approved'),
                'nullable',
                'string',
                'max:255',
            ],
            'approved_document' => [
                Rule::requiredIf(function() use ($request, $application) {
                    if ($request->input('status') !== 'approved') {
                        return false;
                    }
                    return !$application->hasApprovedDocument() || $request->boolean('remove_approved_document');
                }),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:10240',
            ],
            'certificate_number' => 'nullable|string|max:50',
            'remove_approved_document' => 'nullable|boolean',
        ], [
            'approved_document_name.required' => 'निवेदन स्वीकृत गर्नका लागि कागजातको शीर्षक / नाम अनिवार्य छ।',
            'approved_document.required' => 'निवेदन स्वीकृत गर्नका लागि स्वीकृत कागजात / प्रमाणपत्र फाइल अपलोड गर्न अनिवार्य छ।',
            'approved_document.mimes' => 'कागजात केवल PDF, Word (doc/docx), वा फोटो (jpg, png) ढाँचामा हुनुपर्दछ।',
            'approved_document.max' => 'कागजातको साइज १० MB भन्दा कम हुनुपर्दछ।',
        ]);

        $data = [
            'status' => $validated['status'],
            'admin_remarks' => $validated['admin_remarks'],
        ];

        if (in_array($validated['status'], ['approved', 'rejected', 'completed'])) {
            $data['processed_at'] = now();
        }

        // Handle document removal if requested
        if ($request->boolean('remove_approved_document') && $application->approved_document_path) {
            Storage::disk('public')->delete($application->approved_document_path);
            $data['approved_document_path'] = null;
            $data['approved_document_name'] = null;
            $data['approved_document_type'] = null;
            $data['certificate_number'] = null;
            $data['issued_at'] = null;
        }

        // Auto-assign unique Certificate ID if status is approved
        if ($validated['status'] === 'approved') {
            $certNumber = strtoupper(trim((string) ($request->input('certificate_number', ''))));
            if (!$certNumber) {
                $certNumber = $application->certificate_number ?: Application::generateUniqueCertificateId($dept);
            }
            $data['certificate_number'] = $certNumber;
            if (empty($application->issued_at) && empty($data['issued_at'])) {
                $data['issued_at'] = now();
            }
        }

        // Handle new approved document upload
        if ($request->hasFile('approved_document')) {
            if ($application->approved_document_path) {
                Storage::disk('public')->delete($application->approved_document_path);
            }

            $file = $request->file('approved_document');
            $path = $file->store('approved_documents', 'public');
            $ext = strtolower($file->getClientOriginalExtension());

            // Use provided title or fallback to Service Name + प्रमाणपत्र
            $docName = trim((string) ($validated['approved_document_name'] ?? ''));
            if (!$docName) {
                $docName = ($application->service->name ?? 'सरकारी सेवा') . ' — स्वीकृत प्रमाणपत्र';
            }

            $data['approved_document_path'] = $path;
            $data['approved_document_name'] = $docName;
            $data['approved_document_type'] = $ext;
            if (empty($data['certificate_number'])) {
                $data['certificate_number'] = $application->certificate_number ?: Application::generateUniqueCertificateId($dept);
            }
            $data['issued_at'] = now();
        } elseif ($request->filled('approved_document_name') || $request->filled('certificate_number')) {
            // Updating existing document details without re-uploading file
            if ($request->filled('approved_document_name')) {
                $data['approved_document_name'] = trim($request->approved_document_name);
            }
            if ($request->filled('certificate_number')) {
                $data['certificate_number'] = strtoupper(trim($request->certificate_number));
            }
        }

        $application->update($data);

        // Notify Citizen
        if ($application->user) {
            $statusLabel = $application->getStatusLabel();
            $icon = match($application->status) {
                'approved' => 'check-circle',
                'rejected' => 'x-circle',
                'under_review' => 'search',
                default => 'file-text',
            };
            $color = match($application->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                'under_review' => 'info',
                default => 'primary',
            };
            $msg = "तपाईंको निवेदन #{$application->application_number} को स्थिति '{$statusLabel}' भएको छ।";
            if (!empty($application->certificate_number) && $application->status === 'approved') {
                $msg .= " (प्रमाणपत्र ID: {$application->certificate_number})";
            }
            $application->user->notify(new PortalNotification(
                title: "निवेदन स्थिति अद्यावधिक",
                message: $msg,
                link: route('citizen.applications.show', $application),
                icon: $icon,
                color: $color,
                category: 'application_status',
                meta: ['application_id' => $application->id, 'status' => $application->status]
            ));
        }

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'निवेदन स्थिति सफलतापूर्वक अद्यावधिक भयो (' . $application->getStatusLabel() . ')। ' . 
                (!empty($application->certificate_number) ? 'प्रमाणीकरण ID: ' . $application->certificate_number : ''));
    }

    public function requestDocumentReplacement(Request $request, Application $application, ApplicationDocument $document)
    {
        abort_if($document->application_id !== $application->id, 404);

        $request->validate([
            'admin_feedback' => 'required|string|max:500',
        ], [
            'admin_feedback.required' => 'कृपया कागजात प्रतिस्थापन गर्नुपर्ने कारण स्पष्ट लेख्नुहोस्।',
            'admin_feedback.max' => 'कैफियत ५०० अक्षर भन्दा कम हुनुपर्दछ।',
        ]);

        $document->update([
            'status' => 'replacement_needed',
            'admin_feedback' => trim($request->admin_feedback),
        ]);

        if ($application->status === 'pending') {
            $application->update(['status' => 'under_review']);
        }

        // Notify Citizen
        if ($application->user) {
            $application->user->notify(new PortalNotification(
                title: "कागजात पुनः पेश गर्न अनुरोध",
                message: "निवेदन #{$application->application_number} को कागजात '{$document->document_name}' पुनः अपलोड गर्न अनुरोध गरिएको छ: {$document->admin_feedback}",
                link: route('citizen.applications.show', $application),
                icon: 'alert-triangle',
                color: 'warning',
                category: 'replacement',
                meta: ['application_id' => $application->id, 'document_id' => $document->id]
            ));
        }

        return redirect()->route('admin.applications.show', $application)
            ->with('success', "'{$document->document_name}' प्रतिस्थापनको लागि निवेदकलाई सफलतापूर्वक अनुरोध पठाइयो।");
    }
}
