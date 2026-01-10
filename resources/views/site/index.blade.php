<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $content['site_content']['site_name'] ?? 'DLMS' }}</title>
    <meta name="description" content="{{ $content['site_content']['hero']['subtitle'] ?? '' }}">

    <style>
        body {
            margin: 0;
            font-family: 'Tahoma', sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }

        header {
            background: linear-gradient(135deg, #2563eb, #1e3a8a);
            color: white;
            padding: 90px 20px;
            text-align: center;
        }

        header h1 {
            font-size: 42px;
            margin-bottom: 15px;
        }

        header p {
            font-size: 20px;
            opacity: 0.95;
        }

        section {
            max-width: 1000px;
            margin: 70px auto;
            padding: 0 20px;
        }

        section h2 {
            font-size: 32px;
            color: #1e40af;
            margin-bottom: 20px;
            text-align: center;
        }

        .about {
            font-size: 18px;
            line-height: 1.9;
            text-align: center;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .feature {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            font-size: 16px;
        }

        .cta {
            text-align: center;
            margin-top: 80px;
        }

        .cta a {
            display: inline-block;
            background: #22c55e;
            color: white;
            padding: 16px 40px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 20px;
        }

        .cta a:hover {
            background: #16a34a;
        }

        footer {
            background: #020617;
            color: #9ca3af;
            text-align: center;
            padding: 25px;
            margin-top: 100px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <h1>{{ $content['site_content']['hero']['title'] ?? 'إدارة مختبر الأسنان' }}</h1>
    <p>{{ $content['site_content']['hero']['subtitle'] ?? '' }}</p>
</header>

<section>
    <h2>عن النظام</h2>
    <p class="about">{{ $content['site_content']['about']['text'] ?? '' }}</p>
</section>

<section>
    <h2>المميزات</h2>
    <div class="features">
        @foreach($content['site_content']['features'] ?? [] as $feature)
            <div class="feature">
                <h3>{{ $feature['title'] ?? 'ميزة جديدة' }}</h3>
                <p>{{ $feature['description'] ?? 'وصف الميزة هنا' }}</p>
                @if(!empty($feature['icon']))
                    <i class="{{ $feature['icon'] }}"></i>
                @endif
            </div>
        @endforeach
    </div>
</section>

<div class="cta">
    <a href="{{ $content['site_content']['cta']['link'] ?? '#' }}">
        {{ $content['site_content']['cta']['text'] ?? 'ابدأ الآن' }}
    </a>
</div>

<footer>
    <p>{{ $content['site_content']['footer']['text'] ?? '' }}</p>
</footer>

</body>
</html>
