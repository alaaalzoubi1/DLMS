{{-- resources/views/emails/reset-password.blade.php --}}
    <!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور</title>
    <style>
        /* أنماط أساسية للتوافق مع جميع عملاء البريد */
        body, table, td, p, a {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            text-align: right;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .content {
            padding: 30px;
        }
        .code-box {
            background-color: #f0f7ff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            border: 1px solid #046494;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #046494;
            direction: ltr;
            display: inline-block;
            background: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-family: monospace;
        }
        .footer {
            font-size: 12px;
            color: #888888;
            text-align: center;
            border-top: 1px solid #eeeeee;
            padding: 20px;
            background-color: #f9f9f9;
        }
        hr {
            border: none;
            border-top: 1px solid #eeeeee;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <h2 style="color: #046494; margin-top: 0;">مرحباً {{ $userName }},</h2>
        <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p>
        <p>استخدم الرمز التالي لإكمال العملية. هذا الرمز صالح لمدة 5 دقائق فقط:</p>

        <div class="code-box">
            <span class="code">{{ $code }}</span>
        </div>

        <p>إذا لم تقم بطلب إعادة التعيين، يرجى تجاهل هذا البريد.</p>
        <hr>
        <p style="color: #B8860B;">مع التحية،<br>فريق {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.
    </div>
</div>
</body>
</html>
