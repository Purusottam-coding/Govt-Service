<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('services')->latest()->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:departments,code',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');

        if (!empty($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        } else {
            // Auto generate code from department name (3 uppercase letters)
            $codeSeed = preg_replace('/[^a-zA-Z0-9]/', '', $validated['name']);
            $validated['code'] = strtoupper(substr($codeSeed ?: 'DEPT', 0, 3));
        }

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'शाखा/विभाग सफलतापूर्वक सिर्जना भयो।');
    }

    public function show(Department $department)
    {
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');

        if (!empty($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'शाखा/विभाग जानकारी सफलतापूर्वक अद्यावधिक भयो।');
    }

    public function destroy(Department $department)
    {
        if ($department->services()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department with associated services.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}