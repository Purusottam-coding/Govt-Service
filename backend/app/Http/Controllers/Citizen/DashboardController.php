<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Notice;
use App\Models\Service;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_applications' => $user->applications()->count(),
            'pending_applications' => $user->applications()->whereIn('status', ['pending', 'under_review'])->count(),
            'approved_applications' => $user->applications()->whereIn('status', ['approved', 'completed'])->count(),
            'rejected_applications' => $user->applications()->where('status', 'rejected')->count(),
        ];

        $recentApplications = $user->applications()
            ->with(['service.department', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        $featuredServices = Service::where('status', true)
            ->with('department')
            ->latest()
            ->take(4)
            ->get();

        $activeNotices = Notice::published()->latest()->take(3)->get();

        return view('citizen.dashboard', compact('stats', 'recentApplications', 'featuredServices', 'activeNotices'));
    }
}
