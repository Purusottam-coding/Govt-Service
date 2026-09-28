<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->applications()->with(['service.department', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(10);

        return view('citizen.applications.index', compact('applications'));
    }

    public function create(Request $request)
    {
        $serviceId = $request->query('service_id');
        $service = null;

        if ($serviceId) {
            $service = Service::where('status', true)->findOrFail($serviceId);
        }

        $services = Service::where('status', true)->with('department')->get();
        $user = auth()->user();

        return view('citizen.applications.create', compact('service', 'services', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'nullable|string|max:20',
            'applicant_address' => 'nullable|string|max:500',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'document_names.*' => 'nullable|string|max:255',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $application = Application::create([
            'user_id' => auth()->id(),
            'service_id' => $service->id,
            'applicant_name' => $validated['applicant_name'],
            'applicant_email' => $validated['applicant_email'],
            'applicant_phone' => $validated['applicant_phone'],
            'applicant_address' => $validated['applicant_address'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Upload documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                if ($file->isValid()) {
                    $docName = $request->input("document_names.{$index}") ?? $file->getClientOriginalName();
                    $path = $file->store('application_documents/' . $application->id, 'public');

                    ApplicationDocument::create([
                        'application_id' => $application->id,
                        'document_name' => $docName,
                        'file_path' => $path,
                    ]);
                }
            }
        }

        // If service fee > 0, redirect to payment page
        if ($service->fee > 0) {
            return redirect()->route('citizen.payments.create', $application)
                ->with('success', 'Application submitted successfully! Please complete the payment to process your application.');
        }

        return redirect()->route('citizen.applications.show', $application)
            ->with('success', 'Application submitted successfully! Reference number: ' . $application->application_number);
    }

    public function show(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $application->load(['service.department', 'documents', 'payment']);
        return view('citizen.applications.show', compact('application'));
    }

    public function edit(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$application->canBeEdited()) {
            return redirect()->route('citizen.applications.show', $application)
                ->with('error', 'भुक्तानी भइसकेको वा प्रक्रिया अगाडि बढेको निवेदन सम्पादन गर्न मिल्दैन।');
        }

        $services = Service::where('status', true)->with('department')->get();
        $service = $application->service;
        $application->load(['documents', 'service.department']);

        return view('citizen.applications.edit', compact('application', 'services', 'service'));
    }

    public function update(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$application->canBeEdited()) {
            return redirect()->route('citizen.applications.show', $application)
                ->with('error', 'भुक्तानी भइसकेको वा प्रक्रिया अगाडि बढेको निवेदन सम्पादन गर्न मिल्दैन।');
        }

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'nullable|string|max:20',
            'applicant_address' => 'nullable|string|max:500',
            'delete_documents' => 'nullable|array',
            'delete_documents.*' => 'integer|exists:application_documents,id',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'document_names.*' => 'nullable|string|max:255',
        ]);

        $application->update([
            'service_id' => $validated['service_id'],
            'applicant_name' => $validated['applicant_name'],
            'applicant_email' => $validated['applicant_email'],
            'applicant_phone' => $validated['applicant_phone'],
            'applicant_address' => $validated['applicant_address'],
        ]);

        // Delete marked documents
        if (!empty($validated['delete_documents'])) {
            $docsToDelete = $application->documents()->whereIn('id', $validated['delete_documents'])->get();
            foreach ($docsToDelete as $doc) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }
        }

        // Upload new documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                if ($file->isValid()) {
                    $docName = $request->input("document_names.{$index}") ?? $file->getClientOriginalName();
                    $path = $file->store('application_documents/' . $application->id, 'public');

                    ApplicationDocument::create([
                        'application_id' => $application->id,
                        'document_name' => $docName,
                        'file_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('citizen.applications.show', $application)
            ->with('success', 'निवेदन सफलतापूर्वक अद्यावधिक गरियो।');
    }

    public function destroy(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$application->canBeDeleted()) {
            return redirect()->route('citizen.applications.show', $application)
                ->with('error', 'भुक्तानी भइसकेको वा प्रक्रिया अगाडि बढेको निवेदन हटाउन मिल्दैन।');
        }

        // Delete physical files
        foreach ($application->documents as $doc) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }
        Storage::disk('public')->deleteDirectory('application_documents/' . $application->id);

        if ($application->payment) {
            if ($application->payment->payment_statement && Storage::disk('public')->exists($application->payment->payment_statement)) {
                Storage::disk('public')->delete($application->payment->payment_statement);
            }
            $application->payment->delete();
        }

        $application->delete();

        return redirect()->route('citizen.applications.index')
            ->with('success', 'निवेदन सफलतापूर्वक हटाइयो।');
    }
}
