<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'citizen')->withCount('applications');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $citizens = $query->latest()->paginate(10);

        return view('admin.citizens.index', compact('citizens'));
    }

    public function show(User $citizen)
    {
        if ($citizen->role !== 'citizen') {
            abort(404);
        }

        $citizen->load(['applications.service', 'feedback']);
        return view('admin.citizens.show', compact('citizen'));
    }
}
