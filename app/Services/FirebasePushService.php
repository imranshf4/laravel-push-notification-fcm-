<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class FirebasePushService
{
    protected $projectId;
    protected $accessToken;
    protected $endpoint;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');

        $jsonKeyPath = config('services.firebase.credentials');
        $jsonKey = json_decode(file_get_contents($jsonKeyPath), true);

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $jsonKey
        );

        $token = $credentials->fetchAuthToken();
        $this->accessToken = $token['access_token'];

        $this->endpoint = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';
    }

    public function sendNotification(string $deviceToken, string $title, string $body): array
    {
        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ]
            ],
        ];

        $response = Http::withToken($this->accessToken)
            ->post($this->endpoint, $payload);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }

    public function sendNotifications(array $deviceTokens, string $title, string $body): array
    {
        $responses = [];

        foreach ($deviceTokens as $token) {
            $responses[$token] = $this->sendNotification($token, $title, $body);
        }

        return $responses;
    }
}