<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationRemark;
use App\Models\User;
use App\Notifications\PortalNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationRemarkController extends Controller
{
    /**
     * Authorize user access to the application remarks.
     */
    protected function authorizeAccess(Application $application): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        // Allow Admins or the Citizen who owns this application
        if (!$user->isAdmin() && $application->user_id !== $user->id) {
            abort(403, 'अनधिकृत पहुँच: तपाईं यस निवेदनको टिप्पणी हेर्न वा पठाउन सक्नुहुन्न।');
        }
    }

    /**
     * Fetch list of remarks for an application (AJAX).
     */
    public function index(Application $application): JsonResponse
    {
        $this->authorizeAccess($application);

        $currentUser = auth()->user();

        $remarks = $application->remarks()
            ->with('user')
            ->oldest()
            ->get()
            ->map(function (ApplicationRemark $remark) use ($currentUser) {
                return [
                    'id' => $remark->id,
                    'sender_role' => $remark->sender_role,
                    'sender_name' => $remark->sender_name,
                    'is_me' => $remark->user_id === $currentUser->id,
                    'is_admin' => $remark->isAdmin(),
                    'message' => $remark->message,
                    'created_at_human' => $remark->created_at ? $remark->created_at->diffForHumans() : '',
                    'created_at_formatted' => $remark->created_at ? $remark->created_at->format('M d, Y h:i A') : '',
                ];
            });

        return response()->json([
            'success' => true,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Store a new remark (AJAX).
     */
    public function store(Request $request, Application $application): JsonResponse
    {
        $this->authorizeAccess($application);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ], [
            'message.required' => 'कृपया सन्देश वा टिप्पणी लेख्नुहोस्।',
            'message.max' => 'टिप्पणी १००० अक्षर भन्दा बढी हुन सक्दैन।',
        ]);

        $user = auth()->user();

        $remark = $application->remarks()->create([
            'user_id' => $user->id,
            'sender_role' => $user->isAdmin() ? 'admin' : 'citizen',
            'sender_name' => $user->name,
            'message' => trim($validated['message']),
        ]);

        // Send Notification to recipient
        if ($user->isAdmin()) {
            if ($application->user) {
                $application->user->notify(new PortalNotification(
                    title: "नयाँ सन्देश प्राप्त भयो",
                    message: "निवेदन #{$application->application_number} मा प्रशासकबाट नयाँ सन्देश: \"" . Str::limit($remark->message, 60) . "\"",
                    link: route('citizen.applications.show', $application),
                    icon: 'message-square',
                    color: 'primary',
                    category: 'remark',
                    meta: ['application_id' => $application->id, 'remark_id' => $remark->id]
                ));
            }
        } else {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PortalNotification(
                    title: "निवेदकबाट नयाँ सन्देश",
                    message: "निवेदन #{$application->application_number} मा {$user->name} बाट नयाँ सन्देश: \"" . Str::limit($remark->message, 60) . "\"",
                    link: route('admin.applications.show', $application),
                    icon: 'message-square',
                    color: 'info',
                    category: 'remark',
                    meta: ['application_id' => $application->id, 'remark_id' => $remark->id]
                ));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'टिप्पणी सफलतापूर्वक पठाइयो।',
            'remark' => [
                'id' => $remark->id,
                'sender_role' => $remark->sender_role,
                'sender_name' => $remark->sender_name,
                'is_me' => true,
                'is_admin' => $remark->isAdmin(),
                'message' => $remark->message,
                'created_at_human' => $remark->created_at->diffForHumans(),
                'created_at_formatted' => $remark->created_at->format('M d, Y h:i A'),
            ],
        ]);
    }
}
