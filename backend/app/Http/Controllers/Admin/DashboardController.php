<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Department;
use App\Models\Feedback;
use App\Models\Service;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_departments' => Department::count(),
            'total_services' => Service::count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
            'approved_applications' => Application::where('status', 'approved')->count(),
            'rejected_applications' => Application::where('status', 'rejected')->count(),
            'completed_applications' => Application::where('status', 'completed')->count(),
            'total_citizens' => User::where('role', 'citizen')->count(),
            'pending_feedback' => Feedback::where('status', 'open')->count(),
        ];

        $recentApplications = Application::with(['user', 'service'])
            ->latest()
            ->take(8)
            ->get();

        $recentFeedback = Feedback::with(['user', 'service'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'recentFeedback'));
    }
}