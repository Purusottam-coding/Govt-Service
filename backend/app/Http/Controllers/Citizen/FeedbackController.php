<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Service;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbackList = auth()->user()->feedback()
            ->with('service')
            ->latest()
            ->paginate(10);

        return view('citizen.feedback.index', compact('feedbackList'));
    }

    public function create()
    {
        $services = Service::where('status', true)->get();
        return view('citizen.feedback.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'service_id' => $validated['service_id'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return redirect()->route('citizen.feedback.index')
            ->with('success', 'Thank you! Your feedback has been submitted to system administration.');
    }
}
