<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ResetPasswordCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $userName;
    public $logoContent;

    public function __construct($code, $userName)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    public function build()
    {
        // تحميل محتوى الصورة لاستخدامها كـ CID
        $logoPath = storage_path('app/private/logo.jpeg');
        if (file_exists($logoPath)) {
            $this->logoContent = $logoPath;
        }

        return $this->subject('رمز إعادة تعيين كلمة المرور')
            ->view('emails.reset-password-code')
            ->with([
                'code' => $this->code,
                'userName' => $this->userName,
            ])
            ->attachData(file_get_contents($logoPath), 'logo.jpeg', [
                'mime' => 'image/jpeg',
                'as' => 'logo.jpeg',
                'Content-ID' => '<logo>',  // المعرف الذي سنستخدمه في القالب
            ]);
    }
}
