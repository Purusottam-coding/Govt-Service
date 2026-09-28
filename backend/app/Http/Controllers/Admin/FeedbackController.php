<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $feedbackList = $query->latest()->paginate(10);

        return view('admin.feedback.index', compact('feedbackList'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load(['user', 'service']);
        return view('admin.feedback.show', compact('feedback'));
    }

    public function reply(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:1000',
            'status' => 'required|in:replied,closed',
        ]);

        $feedback->update([
            'admin_reply' => $validated['admin_reply'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.feedback.show', $feedback)
            ->with('success', 'Reply submitted successfully.');
    }
}
