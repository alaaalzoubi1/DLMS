<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content['site_content']['site_name']['value'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>

<!-- Header -->
<header>
    <div class="container">
        <img src="{{ asset('images/logo.png') }}" class="logo-img" alt="Logo">
        <nav>
            <ul>
                <li><a href="#">الرئيسية</a></li>
                <li><a href="#">عن النظام</a></li>
                <li><a href="#">المميزات</a></li>
                <li><a href="#">اتصل بنا</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <h1>{{ $content['site_content']['hero']['value']['title']['value'] }}</h1>
            <p>{{ $content['site_content']['hero']['value']['subtitle']['value'] }}</p>
            <div class="hero-btns">
                <a href="#" class="btn-primary">ابدأ الآن</a>
                <a href="#" class="btn-secondary">تعرف على المزيد</a>
            </div>
        </div>
        <div class="hero-mockup">
            <img src="{{ asset('images/mockup.png') }}" class="mockup-laptop" alt="Mockup">
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['features_title']['value'] }}</h2>
        <div class="features-grid">
            @foreach($content['site_content']['features'] as $feature)
                <div class="feature-card">
                    <i class="{{ $feature['value']['icon']['value'] }}"></i>
                    <h3>{{ $feature['value']['title']['value'] }}</h3>
                    <p>{{ $feature['value']['description']['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Social Proof Section -->
<section class="social-proof">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['social_proof_title']['value'] }}</h2>
        <div class="stats-grid">
            @foreach($content['site_content']['social_proof']['value']['stats'] as $stat)
                <div class="stat-box">
                    <h3>{{ $stat['value']['number']['value'] }}</h3>
                    <p>{{ $stat['value']['label']['value'] }}</p>
                </div>
            @endforeach
        </div>
        <div class="testimonial">
            <p>"{{ $content['site_content']['social_proof']['value']['testimonial']['value']['quote']['value'] }}"</p>
            <strong>{{ $content['site_content']['social_proof']['value']['testimonial']['value']['author']['value'] }}</strong>
        </div>
    </div>
</section>

<!-- Workflow Section -->
<section class="workflow">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['workflow_title']['value'] }}</h2>
        <div class="workflow-steps">
            @foreach($content['site_content']['workflow_steps'] as $step)
                <div class="workflow-step">
                    <i class="{{ $step['value']['icon']['value'] }}"></i>
                    <h3>{{ $step['value']['title']['value'] }}</h3>
                    <p>{{ $step['value']['description']['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Platforms Section -->
<section class="platforms">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['platforms_title']['value'] }}</h2>
        <div class="platforms-grid">
            @foreach($content['site_content']['platforms'] as $platform)
                <div class="platform-card">
                    <h3>{{ $platform['value']['platform_name']['value'] }}</h3>
                    @if(isset($platform['value']['app_links']))
                        <ul class="app-links">
                            @foreach($platform['value']['app_links'] as $link)
                                <li>
                                    <a href="{{ $link['value']['url']['value'] }}" target="_blank">
                                        تحميل من {{ $link['value']['store_name']['value'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $platform['value']['url']['value'] }}" class="web-link" target="_blank">زيارة الموقع</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <p>&copy; 2026 LabBridge. جميع الحقوق محفوظة.</p>
    </div>
</footer>

</body>
</html>
