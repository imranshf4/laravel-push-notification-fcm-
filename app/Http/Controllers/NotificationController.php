<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use App\Services\FirebasePushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function saveFcmToken(Request $request)
    {
        try {

            $request->validate([
                'fcm_token' => 'required|string'
            ]);

            FcmToken::updateOrCreate(
                ['fcm_token' => $request->fcm_token],
                ['user_id' => Auth::guard('web')->user()->id]
            );

            return response()->json([
                'success' => true,
                'message' => 'FCM Token saved successfully!'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ]);
        }
    }
}
