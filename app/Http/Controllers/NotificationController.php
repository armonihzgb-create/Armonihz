<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the authenticated user's notifications.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Retrieve notifications paginated and ordered by newest first
        $notifications = $user->notifications()->paginate(15);

        // Format to standard json, mapping to the desired columns
        $formatted = $notifications->through(function ($notification) {
            return [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
            ];
        });

        // Use response()->getData(true) on the paginated resource to keep metadata
        return $this->successResponse(
            $formatted,
            'Notifications retrieved successfully'
        );
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return $this->errorResponse('Notification not found or does not belong to you', null, 404);
        }

        $notification->markAsRead();

        return $this->successResponse(null, 'Notification marked as read successfully');
    }
}
