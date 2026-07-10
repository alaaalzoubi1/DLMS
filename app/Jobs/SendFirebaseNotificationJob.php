<?php

namespace App\Jobs;

use App\Enums\NotificationType;
use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $token;
    protected string $title;
    protected string $body;
    protected ?NotificationType $type;
    protected array $data;

    /**
     * @param array $data Additional key/value pairs merged into the FCM `data` payload.
     */
    public function __construct(
        string $token,
        string $title,
        string $body,
        ?NotificationType $type = null,
        array $data = [],
    ) {
        $this->token = $token;
        $this->title = $title;
        $this->body = $body;
        $this->type = $type;
        $this->data = $data;
    }

    public function handle(FirebaseService $firebaseService): void
    {
        $firebaseService->sendNotification(
            $this->token,
            $this->title,
            $this->body,
            $this->type,
            $this->data,
        );
    }
}
