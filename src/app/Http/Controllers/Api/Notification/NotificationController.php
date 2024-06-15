<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payday\Notification\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $notifications = resolve(\App\Http\Controllers\Core\Notification\NotificationController::class)->index();
            $resourceCollection = new NotificationResource($notifications);

            return success_response('Notifications fetched successfully', $resourceCollection);
        } catch (\Exception $exception) {
            return error_response('Server error', [], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
             resolve(\App\Http\Controllers\Core\Notification\NotificationController::class)->markAsRead($id);

            return success_response('Notification marked as read');
        } catch (\Exception $exception) {
            return error_response('Server error', [], 500);
        }
    }

    public function markAsAllRead()
    {
        try {
             resolve(\App\Http\Controllers\Core\Notification\NotificationController::class)->markAsReadAll();

            return success_response('All notifications marked as read');
        } catch (\Exception $exception) {
            return error_response('Server error', [], 500);
        }
    }
}
