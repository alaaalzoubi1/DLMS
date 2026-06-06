<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendResetPasswordCode extends Notification implements ShouldQueue
{
    use Queueable;

    public $code;
    public $userName;

    public function __construct($code, $userName)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('رمز إعادة تعيين كلمة المرور')
            ->view('emails.reset-password-code', [
                'code' => $this->code,
                'userName' => $this->userName,
            ]);
    }
}
