<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $content['site_content']['site_name']['value'] ?? 'LabBridge' }}</title>
    <meta name="description" content="{{ $content['site_content']['hero']['value']['subtitle']['value'] ?? '' }}">

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>

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

<section class="features">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['features_title']['value'] ?? 'المميزات' }}</h2>
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

<section class="social-proof">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['social_proof_title']['value'] ?? 'موثوق به من معامل أسنان حقيقية' }}</h2>
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

<section class="workflow">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['workflow_title']['value'] ?? 'كيف تُدار الطلبات في LabBridge؟' }}</h2>
        <div class="workflow-line">
            @foreach($content['site_content']['workflow_steps'] as $step)
                <div class="workflow-step">
                    <div class="step-icon-wrapper">
                        <i class="{{ $step['value']['icon']['value'] }}"></i>
                    </div>
                    <h4>{{ $step['value']['title']['value'] }}</h4>
                    <p>{{ $step['value']['description']['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="platforms-download">
    <div class="container">
        <h2 class="section-title">{{ $content['site_content']['platforms_title']['value'] ?? 'نظام واحد.. منصات متعددة' }}</h2>
        <div class="platform-cards-container">
            @foreach($content['site_content']['platforms'] as $platform)
                <div class="platform-card">
                    <h3>{{ $platform['value']['platform_name']['value'] }}</h3>
                    <div class="app-buttons">
                        @foreach($platform['value']['app_links'] ?? [] as $link)
                            <a href="{{ $link['value']['url']['value'] }}" class="app-btn">
                                <i class="platform-icons fab fa-{{ strtolower($link['value']['platform']['value']) }}"></i>
                                <div>
                                    <strong>{{ $link['value']['store_name']['value'] }}</strong>
                                    <span>احصل عليه من {{ $link['value']['platform']['value'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<footer>
    <p>&copy; 2026 LabBridge. جميع الحقوق محفوظة.</p>
    <div class="footer-links">
        <a href="#">اتصل بنا</a>
        <a href="#">سياسة الخصوصية</a>
        <a href="#">الشروط والأحكام</a>
    </div>
</footer>

</body>
</html>
