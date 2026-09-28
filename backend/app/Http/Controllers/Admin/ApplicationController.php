<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Service;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,completed',
            'admin_remarks' => 'nullable|string|max:1000',
            'approved_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'approved_document_name' => 'nullable|string|max:255',
            'certificate_number' => 'nullable|string|max:50',
            'remove_approved_document' => 'nullable|boolean',
        ], [
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

            // Use provided Certificate ID or auto-generate unique ABC123 format
            $certNumber = strtoupper(trim((string) ($validated['certificate_number'] ?? '')));
            if (!$certNumber) {
                $certNumber = $application->certificate_number ?: Application::generateUniqueCertificateId();
            }

            $data['approved_document_path'] = $path;
            $data['approved_document_name'] = $docName;
            $data['approved_document_type'] = $ext;
            $data['certificate_number'] = $certNumber;
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

        // Auto-assign unique Certificate ID if status is approved and none assigned yet
        if ($validated['status'] === 'approved' && empty($application->certificate_number) && empty($data['certificate_number'])) {
            $data['certificate_number'] = Application::generateUniqueCertificateId();
            if (empty($data['issued_at'])) {
                $data['issued_at'] = now();
            }
        }

        $application->update($data);

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'निवेदन स्थिति सफलतापूर्वक अद्यावधिक भयो (' . $application->getStatusLabel() . ')। ' . 
                (!empty($application->certificate_number) ? 'प्रमाणीकरण ID: ' . $application->certificate_number : ''));
    }
}
