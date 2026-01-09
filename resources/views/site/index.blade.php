<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $content['site_name'] ?? 'DLMS' }}</title>
    <meta name="description" content="{{ $content['about']['text'] ?? '' }}">

    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background-color: #f8f9fb;
            color: #333;
            line-height: 1.8;
        }

        header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }

        header h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
        }

        header p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 700px;
            margin: auto;
        }

        section {
            max-width: 1100px;
            margin: 60px auto;
            padding: 0 20px;
        }

        section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #1e40af;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .feature {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }

        .cta {
            background: #1e40af;
            color: white;
            text-align: center;
            padding: 60px 20px;
            margin-top: 80px;
        }

        .cta h3 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        footer {
            background: #0f172a;
            color: #cbd5f5;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<header>
    <h1>{{ $content['hero']['title'] ?? 'عنوان افتراضي' }}</h1>
    <p>{{ $content['hero']['subtitle'] ?? '' }}</p>
</header>

<section>
    <h2>عن النظام</h2>
    <p>{{ $content['about']['text'] ?? '' }}</p>
</section>

<section>
    <h2>المميزات</h2>
    <div class="features">
        @foreach(($content['features'] ?? []) as $feature)
            <div class="feature">
                {{ $feature }}
            </div>
        @endforeach
    </div>
</section>

<section class="cta">
    <h3>{{ $content['cta']['text'] ?? 'ابدأ الآن' }}</h3>
</section>

<footer>
    <p>{{ $content['footer']['text'] ?? '' }}</p>
</footer>

</body>
</html>
