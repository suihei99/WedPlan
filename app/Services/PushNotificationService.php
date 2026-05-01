<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    public function send(string $deviceToken, string $title, string $message, array $data = []): bool
    {
        $serverKey = (string) config('services.firebase.server_key');

        if ($serverKey === '' || $deviceToken === '') {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key='.$serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                ],
                'data' => $data,
                'priority' => 'high',
            ]);

            return $response->successful();
        } catch (\Throwable $exception) {
            Log::warning('FCM push send failed.', [
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
