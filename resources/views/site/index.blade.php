<!DOCTYPE html>
@php
    $lang = request('lang', 'ar');

    $pageContent = $lang === 'en'
        ? ($content['site_content_en'] ?? [])
        : ($content['site_content'] ?? []);
@endphp

<html lang="{{ $lang }}" dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $pageContent['site_name'] ?? 'LabBridge' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: {{ $lang === 'ar' ? "'Cairo'" : "'Inter'" }}, sans-serif;
            background: #ffffff;
            color: #111;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            border-bottom: 1px solid #eee;
        }

        nav a {
            margin-inline: 12px;
            text-decoration: none;
            color: #111;
            font-weight: 600;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            background: #111;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        section {
            padding: 80px 60px;
        }

        .hero h1 {
            font-size: 44px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            max-width: 700px;
            line-height: 1.7;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .card {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 25px;
        }

        .card i {
            font-size: 26px;
            margin-bottom: 10px;
        }

        footer {
            padding: 40px 60px;
            background: #111;
            color: #fff;
        }

        footer a {
            color: #ccc;
            margin-inline-end: 15px;
            text-decoration: none;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<header>
    <strong>{{ $pageContent['site_name'] }}</strong>

    <nav>
        <a href="#features">{{ $pageContent['header']['nav_links']['features'] }}</a>
        <a href="#workflow">{{ $pageContent['header']['nav_links']['workflow'] }}</a>
        <a href="#platforms">{{ $pageContent['header']['nav_links']['platforms'] }}</a>
        <a href="#pricing">{{ $pageContent['header']['nav_links']['pricing'] }}</a>
    </nav>

    <div style="display:flex; gap:10px">
        <a href="#pricing" class="btn">{{ $pageContent['header']['cta_text'] }}</a>

        @if($lang === 'ar')
            <a href="?lang=en" class="btn">EN</a>
        @else
            <a href="?lang=ar" class="btn">عربي</a>
        @endif
    </div>
</header>

<!-- HERO -->
<section class="hero">
    <h1>{{ $pageContent['hero']['title'] }}</h1>
    <p>{{ $pageContent['hero']['subtitle'] }}</p>
</section>

<!-- FEATURES -->
<section id="features">
    <h2>{{ $pageContent['features_section']['title'] }}</h2>

    <div class="grid">
        @foreach($pageContent['features_section']['features'] as $feature)
            <div class="card">
                <i class="{{ $feature['icon'] }}"></i>
                <h3>{{ $feature['title'] }}</h3>
                <p>{{ $feature['description'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<!-- WORKFLOW -->
<section id="workflow">
    <h2>{{ $pageContent['workflow_section']['title'] }}</h2>

    <div class="grid">
        @foreach($pageContent['workflow_section']['steps'] as $step)
            <div class="card">
                <i class="{{ $step['icon'] }}"></i>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['description'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<!-- PLATFORMS -->
<section id="platforms">
    <h2>{{ $pageContent['platforms_section']['title'] }}</h2>
    <p>{{ $pageContent['platforms_section']['subtitle'] }}</p>

    <div class="grid">
        @foreach($pageContent['platforms_section']['platforms'] as $platform)
            <div class="card">
                <h3>{{ $platform['platform_name'] }}</h3>
            </div>
        @endforeach
    </div>
</section>

<!-- PRICING -->
<section id="pricing">
    <h2>{{ $pageContent['pricing_section']['title'] }}</h2>

    <div class="grid">
        @foreach($pageContent['pricing_section']['plans'] as $plan)
            <div class="card">
                <h3>{{ $plan['name'] }}</h3>
                <strong>{{ $plan['price'] }}</strong>
                <ul>
                    @foreach($plan['features'] as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</section>

<!-- FAQ -->
<section>
    <h2>{{ $pageContent['faq_section']['title'] }}</h2>

    @foreach($pageContent['faq_section']['items'] as $faq)
        <div class="card">
            <strong>{{ $faq['question'] }}</strong>
            <p>{{ $faq['answer'] }}</p>
        </div>
    @endforeach
</section>

<!-- FOOTER -->
<footer>
    <p>{{ $pageContent['footer']['copyright_text'] }}</p>

    <div>
        <a href="#">{{ $pageContent['footer']['links']['terms_of_service'] }}</a>
        <a href="#">{{ $pageContent['footer']['links']['privacy_policy'] }}</a>
    </div>

    <small>{{ $pageContent['footer']['security_note'] }}</small>
</footer>

</body>
</html>
