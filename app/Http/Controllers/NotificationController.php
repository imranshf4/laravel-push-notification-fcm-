<?php

namespace App\Http\Controllers;

use App\Services\FirebasePushService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $firebasePush;
    public function __construct(FirebasePushService $firebasePush)
    {
        $this->firebasePush = $firebasePush;
    }
    public function sendTest(Request $request)
    {
        $response = $this->firebasePush->sendNotification(
            'token',
            'Hello from Laravel',
            'This is a test push notification from your Laravel app.',
        );
        return response()->json($response);
    }
}
