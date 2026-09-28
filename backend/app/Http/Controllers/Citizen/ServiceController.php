<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('status', true)->with('department');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $services = $query->paginate(9);
        $departments = Department::where('status', true)->get();

        return view('citizen.services.index', compact('services', 'departments'));
    }

    public function show(Service $service)
    {
        if (!$service->status) {
            abort(404);
        }

        $service->load('department');
        return view('citizen.services.show', compact('service'));
    }
}
