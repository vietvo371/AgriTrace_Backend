<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unreadCount(Request $request): JsonResponse
    {
        // Giả sử bạn có một bảng notifications với trường read_at
        $unreadCount = $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }
}
