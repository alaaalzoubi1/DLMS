<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorResetPasswordCode extends Notification implements ShouldQueue
{
    use Queueable;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $logoUrl = asset('storage/app/private/logo.jpeg');

        return (new MailMessage)
            ->subject('رمز إعادة تعيين كلمة المرور - حساب الطبيب')
            ->greeting('مرحباً الدكتور/ة ' . ($notifiable->doctor->name ?? ''))
            ->line('لقد تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك.')
            ->line('رمز التحقق الخاص بك هو:')
            ->line('**' . $this->code . '**')
            ->line('هذا الرمز صالح لمدة 5 دقائق.')
            ->line('إذا لم تطلب إعادة التعيين، فلا حاجة لاتخاذ أي إجراء.')
            ->salutation('مع التحية، ' . config('app.name'));
    }

    // إذا أردت تضمين اللوجو في view مخصص:
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)->view('emails.doctor-reset-code', [
    //         'code' => $this->code,
    //         'doctor' => $notifiable->doctor,
    //         'logo' => asset('storage/logo.png')
    //     ]);
    // }
}
