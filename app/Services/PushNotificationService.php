<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class PushNotificationService
{
    public function __construct(private readonly Messaging $messaging) {}

    public function send(string $deviceToken, string $title, string $message, array $data = []): bool
    {
        if ($deviceToken === '') {
            return false;
        }

        try {
            $cloudMessage = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(FirebaseNotification::create($title, $message))
                ->withData($this->normalizeData($data));

            $this->messaging->send($cloudMessage);

            return true;
        } catch (\Throwable $exception) {
            Log::warning('Firebase Admin push send failed, falling back to legacy FCM.', [
                'error' => $exception->getMessage(),
            ]);

            return $this->sendLegacyFcm($deviceToken, $title, $message, $data);
        }
    }

    private function sendLegacyFcm(string $deviceToken, string $title, string $message, array $data = []): bool
    {
        $serverKey = (string) (config('services.firebase.server_key') ?: config('services.firebase.legacy_server_key'));

        if ($serverKey === '') {
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
            Log::warning('Legacy FCM push send failed.', [
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function normalizeData(array $data): array
    {
        $normalizedData = [];

        foreach ($data as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $normalizedData[(string) $key] = (string) $value;

                continue;
            }

            $normalizedData[(string) $key] = json_encode($value) ?: '';
        }

        return $normalizedData;
    }
}
