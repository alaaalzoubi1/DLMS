<?php

namespace App\Services;

use App\Enums\NotificationType;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected \Kreait\Firebase\Contract\Messaging $messaging;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!file_exists(storage_path('firebase/dlms-604e0-firebase-adminsdk-t4ils-ca01a50a1e.json'))) {
            throw new Exception('Firebase credentials file not found!');
        }

        $factory = (new Factory)->withServiceAccount(
            storage_path('firebase/dlms-604e0-firebase-adminsdk-t4ils-ca01a50a1e.json')
        );

        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send an FCM push notification.
     *
     * @param array $extraData Additional key/value pairs merged into the FCM
     *                         `data` payload (merged on top of the type payload).
     */
    public function sendNotification(
        string $deviceToken,
        string $title,
        string $body,
        ?NotificationType $type = null,
        array $extraData = [],
    ): void {
        try {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));

            $data = $extraData;
            if ($type !== null) {
                $data = array_merge($type->toData(), $data);
            }
            if (!empty($data)) {
                // FCM data values must be strings.
                $message = $message->withData(array_map('strval', $data));
            }

            $this->messaging->send($message);
        } catch (\Throwable $e) {
            Log::error('FCM send failed: ' . $e->getMessage(), [
                'token' => $deviceToken,
                'title' => $title,
                'body'  => $body,
                'type'  => $type?->value,
            ]);
        }
    }
}
