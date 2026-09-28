<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('department')->withCount('applications');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->latest()->paginate(10);
        $departments = Department::where('status', true)->get();

        return view('admin.services.index', compact('services', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('status', true)->get();
        return view('admin.services.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fee' => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
            'required_documents' => 'nullable|string', // comma-separated input from user
        ]);

        $documents = array_filter(array_map('trim', explode(',', $request->input('required_documents', ''))));

        Service::create([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'fee' => $validated['fee'],
            'processing_days' => $validated['processing_days'],
            'required_documents' => $documents,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $service->load(['department', 'applications.user']);
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $departments = Department::where('status', true)->get();
        return view('admin.services.edit', compact('service', 'departments'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fee' => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
            'required_documents' => 'nullable|string',
        ]);

        $documents = array_filter(array_map('trim', explode(',', $request->input('required_documents', ''))));

        $service->update([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'fee' => $validated['fee'],
            'processing_days' => $validated['processing_days'],
            'required_documents' => $documents,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->applications()->count() > 0) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Cannot delete service with existing applications.');
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
