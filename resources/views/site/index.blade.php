<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ $content['site_content']['site_name'] ?? 'LabBridge' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="icon" href="https://labbridge.sarfle.com/images/favicon_out.ico" type="image/x-icon">
    <style>
        /* ============================= */
        /*           VARIABLES           */
        /* ============================= */
        html {
            scroll-behavior: smooth;
        }
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #0f172a;
            --accent: #38bdf8;
            --light: #f8fafc;
            --text: #334155;
            --border: #e5e7eb;
            --glass: rgba(255, 255, 255, 0.85);
        }

        /* ============================= */
        /*       JAVASCRIPT EFFECTS      */
        /* ============================= */
        .hidden-anim {
            opacity: 0;
            transform: translateY(40px);
            filter: blur(5px);
            transition: opacity 0.6s ease-out, transform 0.8s ease-out, filter 0.6s ease-out;
        }
        .visible {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }

        /* ============================= */
        /*         GLOBAL STYLES         */
        /* ============================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', sans-serif;
            color: var(--text);
            line-height: 1.7;
            background-color: #fff;
            overflow-x: hidden;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .section-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: 800;
            color: var(--secondary);
        }
        .section-subtitle {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 50px auto;
            font-size: 18px;
            color: #64748b;
        }

        /* ============================= */
        /*            HEADER             */
        /* ============================= */
        header {
            background: var(--glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-img {
            height: 55px;
        }
        nav ul {
            display: flex;
            list-style: none;
            gap: 15px;
        }
        nav ul li a {
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            transition: color 0.3s;
            padding: 8px 12px;
            border-radius: 8px;
        }
        nav ul li a:hover {
            color: var(--primary);
            background-color: #f0f4ff;
        }

        /* ============================= */
        /*            BUTTONS            */
        /* ============================= */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 25px rgba(37, 99, 235, 0.3);
        }
        .btn-secondary {
            background: var(--light);
            border: 1px solid var(--border);
            color: var(--secondary);
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .btn-secondary:hover {
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        /* ============================= */
        /*              HERO             */
        /* ============================= */
        .hero {
            padding: 80px 0;
            background: radial-gradient(circle at 80% 0%, #e0f2fe 0%, transparent 40%), linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            overflow: hidden;
        }
        .hero-grid {
            display: flex;
            align-items: center;
            gap: 50px;
        }
        .hero-content {
            flex: 1;
            text-align: right;
        }
        .hero-content h1 {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        .hero-content h1 span { color: var(--primary); }
        .hero-content p {
            font-size: 18px;
            margin: 0 0 40px;
            color: #64748b;
            max-width: 500px;
        }
        .hero-btns {
            display: flex;
            justify-content: flex-start;
            gap: 15px;
            flex-wrap: wrap;
        }
        .hero-mockup {
            flex: 1.2;
            position: relative;
        }
        .mockup-laptop {
            width: 120%;
            height: auto;
            filter: drop-shadow(0px 25px 40px rgba(0, 0, 0, 0.1));
        }
        .mockup-phone {
            position: absolute;
            width: 40%;
            height: auto;
            top: 35%;
            right: 5%;
            filter: drop-shadow(0px 15px 25px rgba(0, 0, 0, 0.15));
            transition: transform 0.3s ease;
            z-index: 10;
        }
        .mockup-phone:hover {
            transform: translateY(-10px) scale(1.05);
        }

        /* ============================= */
        /*           FEATURES            */
        /* ============================= */
        .features { padding: 80px 0; }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .feature-card {
            padding: 35px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 20px;
            transition: all 0.35s ease;
            text-align: center;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
        }
        .feature-card h3 {
            margin: 20px 0 10px;
            font-size: 20px;
        }
        .feature-card i {
            font-size: 36px;
            color: var(--primary);
        }

        /* ============================= */
        /*         SOCIAL PROOF          */
        /* ============================= */
        .social-proof { padding: 80px 0; background-color: var(--light); }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        .stat-box {
            text-align: center;
            padding: 30px;
        }
        .stat-box h3 {
            font-size: 42px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
        }
        .stat-box p {
            font-size: 16px;
            font-weight: 600;
            color: #64748b;
        }
        .testimonial {
            max-width: 700px;
            margin: auto;
            text-align: center;
            font-size: 18px;
            color: var(--secondary);
            padding: 30px;
        }
        .testimonial strong {
            display: block;
            margin-top: 15px;
            color: #64748b;
            font-size: 14px;
        }

        /* ============================= */
        /*           WORKFLOW            */
        /* ============================= */
        .workflow { padding: 80px 0; }
        .workflow-line {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-top: 40px;
        }
        .workflow-line::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 5%;
            right: 5%;
            height: 2px;
            background-color: #cbd5e1;
            z-index: 1;
        }
        .workflow-step {
            text-align: center;
            width: 15%;
            position: relative;
            z-index: 2;
            background-color: #fff; /* Use page background to hide line */
            padding: 0 5px;
        }
        .step-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #fff;
            border: 3px solid #cbd5e1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            transition: all 0.3s ease;
        }
        .workflow-step:hover .step-icon-wrapper {
            transform: scale(1.1);
            border-color: var(--primary);
        }
        .step-icon-wrapper i { font-size: 32px; color: var(--primary); }
        .workflow-step h4 { font-size: 16px; margin-bottom: 8px; font-weight: 700; }
        .workflow-step p { font-size: 14px; color: #64748b; }


        /* ============================================= */
        /*         UNIFIED DOWNLOAD PLATFORMS SECTION      */
        /* ============================================= */
        .platforms-download {
            background-color: #f8fafc;
            padding: 80px 0;
        }
        .platform-cards-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 50px;
        }
        .platform-card {
            text-align: center;
            padding: 40px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: #fff;
            max-width: 400px;
            flex: 1;
            min-width: 300px;
        }
        .platform-icons {
            font-size: 48px;
            color: var(--secondary);
        }
        .platform-icons i {
            margin: 0 10px;
        }
        .platform-card p {
            margin: 20px 0;
            font-weight: 600;
            min-height: 40px;
        }
        .app-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        .app-btn {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 20px;
            background-color: var(--secondary);
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid var(--secondary);
        }
        .app-btn:hover {
            background-color: #fff;
            color: var(--secondary);
        }
        .app-btn i {
            font-size: 28px;
        }
        .app-btn div {
            text-align: right;
        }
        .app-btn span {
            display: block;
            font-size: 12px;
            line-height: 1;
        }
        .app-btn strong {
            font-size: 18px;
            line-height: 1;
        }

        /* ============================= */
        /*            PRICING            */
        /* ============================= */
        .pricing { padding: 80px 0; background: var(--light); }
        .price-cards-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        .price-card {
            flex: 1;
            min-width: 300px;
            max-width: 380px;
            background: #fff;
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 20px 50px -20px rgba(0,0,0,0.1);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .price-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }
        .price {
            font-size: 48px;
            font-weight: 800;
            margin: 15px 0;
            color: var(--primary);
        }
        .price span {
            font-size: 18px;
            color: #64748b;
            font-weight: 400;
        }
        .price-card ul {
            list-style: none;
            margin: 25px 0;
            padding: 0;
            text-align: right;
        }
        .price-card ul li {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .price-card ul li i {
            color: #10b981;
            margin-left: 10px;
        }
        .price-card .btn-primary {
            margin-top: auto; /* Push button to the bottom */
        }

        /* ============================= */
        /*         OTHER SECTIONS        */
        /* ============================= */
        .compliance { padding: 60px 0; }
        .compliance p { text-align: center; max-width: 800px; margin: auto; }

        .faq { padding: 80px 0; }
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
            border-top: 1px solid var(--border);
        }
        .faq-item {
            border-bottom: 1px solid var(--border);
            padding: 15px 0;
        }
        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .faq-question::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--primary);
            transition: transform 0.3s ease;
        }

        /* دوران السهم عند الفتح */
        .faq-item[open] > .faq-question::after {
            transform: rotate(180deg);
        }

        .faq-answer { padding: 20px 10px 0; line-height: 1.8; }

        /* ============================= */
        /*            FOOTER             */
        /* ============================= */
        footer {
            padding: 60px 0;
            background: var(--secondary);
            color: #e5e7eb;
            text-align: center;
        }
        .footer-links { margin: 20px 0; }
        .footer-links a { color: #94a3b8; text-decoration: none; margin: 0 10px; transition: color 0.3s; }
        .footer-links a:hover { color: #fff; }
        .security-note { margin-top: 20px; color: #94a3b8; font-size: 14px; }
        .security-note i { color: var(--accent); margin-right: 5px; }

        /* =================================================================== */
        /* =================== RESPONSIVE MEDIA QUERIES ====================== */
        /* =================================================================== */

        /* For Tablets and Smaller Desktops */
        .platforms-two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
        }

        .platform-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
        }

        .platform-box h3 {
            font-size: 22px;
            margin-bottom: 25px;
            font-weight: 800;
            color: var(--secondary);
        }

        /* ترتيب RTL */
        .platform-box.mobile {
            order: 1; /* يمين */
        }

        .platform-box.windows {
            order: 2; /* يسار */
        }

        /* موبايل */
        @media (max-width: 768px) {
            .platforms-two-columns {
                grid-template-columns: 1fr;
            }
        }
        /* خلفية المودال */
        .legal-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }.legal-modal.active {
             display: flex;
         }

        /* الصندوق */
        .legal-box {
            background: #ffffff;
            width: 90%;
            max-width: 800px;
            max-height: 85vh;
            overflow-y: auto;
            border-radius: 16px;
            padding: 40px 30px;
            position: relative;
            direction: rtl;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            animation: fadeUp 0.3s ease;
        }

        /* زر الإغلاق */
        .close-btn {
            position: absolute;
            top: 18px;
            left: 18px;
            width: 40px;
            height: 40px;
            border: none;
            background: #f1f5f9;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            line-height: 1;
            transition: 0.2s;
        }

        .close-btn:hover {
            background: #e11d48;
            color: #fff;
        }

        /* العناوين */
        .legal-title {
            font-size: 28px;
            margin-bottom: 10px;
            color: #1e3a8a;
        }

        .legal-date {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 20px;
        }

        .legal-desc {
            font-size: 17px;
            margin-bottom: 25px;
        }

        /* القوانين */
        .law-item {
            margin-bottom: 25px;
        }

        .law-item h4 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .law-item ul {
            padding-right: 20px;
        }

        .law-item li {
            margin-bottom: 6px;
            line-height: 1.7;
        }

        /* Contact */
        .legal-contact {
            margin-top: 30px;
            font-weight: bold;
        }

        /* أنيميشن */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        .law-item.highlight {
            background: #f8fafc;
            border-right: 4px solid #2563eb;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .law-item.warning {
            background: #fff7ed;
            border-right: 4px solid #f97316;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        /* ============================= */
        /*        MOBILE FIXES           */
        /* ============================= */

        @media (max-width: 768px) {

            /* ---------- HEADER ---------- */
            header .container {
                flex-direction: row;
                justify-content: space-between;
            }

            nav {
                display: none; /* إخفاء القائمة على الموبايل */
            }

            .header-cta {
                margin: 0;
            }

            .logo-img {
                height: 45px;
            }

            /* ---------- HERO SECTION ---------- */
            .hero {
                padding: 60px 0 40px;
                text-align: center;
            }

            .hero-grid {
                flex-direction: column;
                gap: 30px;
            }

            .hero-content {
                text-align: center;
            }

            .hero-content h1 {
                font-size: 32px;
                line-height: 1.3;
            }

            .hero-content p {
                font-size: 16px;
                margin: 15px auto 30px;
                max-width: 100%;
            }

            .hero-btns {
                justify-content: center;
            }

            /* ---------- HERO IMAGE ---------- */
            .hero-mockup {
                order: 2;
                width: 100%;
                text-align: center;
            }

            .mockup-laptop {
                width: 100%;
                max-width: 320px;
                margin: 0 auto;
            }

            /* إذا رغبت إخفاء الصورة نهائياً على الموبايل */
            /*
            .hero-mockup {
                display: none;
            }
            */
        }


    </style>
</head>
<div id="privacyModal" class="legal-modal">
    <div class="legal-box">
        <button class="close-btn" onclick="closePrivacy()" aria-label="إغلاق">×</button>

        <h2 class="legal-title">سياسة الخصوصية</h2>

        <p class="legal-date">
            آخر تحديث:
            {{ $content['Privacy_Policy']['last_updated'] ?? '' }}
        </p>

        <p class="legal-desc">
            {{ $content['Privacy_Policy']['description']['title'] ?? '' }}
        </p>

        @foreach(($content['Privacy_Policy']['laws'] ?? []) as $law)
            <div class="law-item">
                <h4>{{ $law['id'] }}. {{ $law['title'] }}</h4>
                <ul>
                    @foreach(($law['content'] ?? []) as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <p class="legal-contact">
            {{ $content['Privacy_Policy']['contact_email'] ?? '' }}
        </p>
    </div>
</div>
<div id="termsModal" class="legal-modal" onclick="closeTerms()">
    <div class="legal-box" onclick="event.stopPropagation()">
        <button class="close-btn" onclick="closeTerms()">×</button>

        <h2 class="legal-title">شروط الخدمة</h2>

        <p class="legal-date">
            آخر تحديث: {{ $content['Terms_Of_Service']['last_updated'] ?? '' }}
        </p>

        <p class="legal-desc">
            {{ $content['Terms_Of_Service']['description']['title'] ?? '' }}
        </p>

        {{-- الشروط الأساسية --}}
        @foreach(($content['Terms_Of_Service']['terms'] ?? []) as $term)
            <div class="law-item">
                <h4>{{ $term['id'] }}. {{ $term['title'] }}</h4>
                <ul>
                    @foreach(($term['content'] ?? []) as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        {{-- Subscription & Billing Policy --}}
        @if(isset($content['Terms_Of_Service']['subscription_policy']))
            <div class="law-item highlight">
                <h4>{{ $content['Terms_Of_Service']['subscription_policy']['title'] }}</h4>
                <ul>
                    @foreach($content['Terms_Of_Service']['subscription_policy']['content'] as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Compliance Disclaimer --}}
        @if(isset($content['Terms_Of_Service']['compliance_disclaimer']))
            <div class="law-item warning">
                <h4>{{ $content['Terms_Of_Service']['compliance_disclaimer']['title'] }}</h4>
                <ul>
                    @foreach($content['Terms_Of_Service']['compliance_disclaimer']['content'] as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="legal-contact">
            {{ $content['Terms_Of_Service']['contact_email'] ?? '' }}
        </p>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const privacyLink = document.querySelector('.legal-link');

        if (privacyLink) {
            privacyLink.addEventListener('click', function (e) {
                e.preventDefault();
                openPrivacy();
            });
        }
    });

    function openTerms() {
        document.getElementById('termsModal').classList.add('active');
    }

    function closeTerms() {
        document.getElementById('termsModal').classList.remove('active');
    }
    function openPrivacy() {
        document.getElementById('privacyModal').classList.add('active');
    }
    function closePrivacy() {
        document.getElementById('privacyModal').classList.remove('active');
    }

</script>



<body>
<!-- SCRIPT (لا تقم بتغييره) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });
        const hiddenElements = document.querySelectorAll('.hidden-anim');
        hiddenElements.forEach(el => observer.observe(el));

        const allFaqItems = document.querySelectorAll('.faq-item');
        allFaqItems.forEach(item => {
            item.addEventListener('toggle', (event) => {
                if (item.open) {
                    allFaqItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.open) {
                            otherItem.removeAttribute('open');
                        }
                    });
                }
            });
        });
    });
</script>

<header>
    <div class="container">
        <a href="#" class="logo">
            <img src="https://labbridge.sarfle.com/images/logowithname_outnew.png" alt="LabBridge Logo" class="logo-img">
        </a>
        <nav>
            <ul>
                <li><a href="#features">{{ $content['header']['nav_links']['features'] ?? 'المميزات' }}</a></li>
                <!--<li><a href="#social-proof">{{ $content['site_content']['hero']['workflow'] ?? 'دورة العمل' }}</a></li>-->
                <li><a href="#platforms">{{$content['header']['nav_links']['platforms'] ?? 'المنصات' }}</a></li>
                <li><a href="#pricing">{{ $content['header']['nav_links']['pricing'] ?? 'الأسعار' }}</a></li>
            </ul>
        </nav>
        <div class="header-cta">
            <a href="#platforms" class="btn-primary">{{ __('حمل التطبيق الآن') }}</a>
        </div>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1>
                        {!! str_replace('معمل الأسنان', '<br><span>معمل الأسنان', $content['site_content']['hero']['title']) !!}</span>
                    </h1>


                    <p>{{ $content['site_content']['hero']['subtitle'] ?? 'وداعًا للفوضى، التأخير، وضياع الحالات. نظام واحد يربط الطبيب، الفني، المندوب والمحاسب لإدارة الطلبات، التتبع، والفوترة بوضوح كامل.' }}</p>
                    <div class="hero-btns">
                        <a href="#platforms" class="btn-primary">{{ __('تحميل الآن') }}</a>
                        <!-- <a href="#workflow" class="btn-secondary">{{ __('كيف يعمل؟') }}</a>-->
                    </div>
                </div>
                <div class="hero-mockup">
                    <img src="images/mockup.png" alt="تطبيق LabBridge على الاجهزة" class="mockup-laptop">
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title">{{ $content['site_content']['features_title'] ?? 'وداعًا للمكالمات والواتساب… كل شغل المعمل في مكان واحد' }}</h2>
            <div class="features-grid">
                @if(isset($content['site_content']['features_section']['features']) && is_array($content['site_content']['features_section']['features']))
                    @foreach ($content['site_content']['features_section']['features'] as $feature)
                        <div class="feature-card hidden-anim">
                            <i class="{{ $feature['icon'] }}"></i>
                            <h3>{{ $feature['title'] }}</h3>
                            <p>{{ $feature['description'] }}</p>
                        </div>
                    @endforeach
                @else
                    <p>لا توجد مميزات حالياً لعرضها.</p>
                @endif

            </div>
        </div>
    </section>

    <section class="social-proof">
        <div class="container">
            <h2 class="section-title">{{ $content['site_content']['social_proof_title'] ?? 'موثوق به من معامل أسنان حقيقية' }}</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <h3>+120</h3>
                    <p>{{ $content['site_content']['stat_1'] ?? 'معمل أسنان نشط' }}</p>
                </div>
                <div class="stat-box">
                    <h3>+18,000</h3>
                    <p>{{ $content['site_content']['stat_2'] ?? 'حالة تم تتبعها عبر النظام' }}</p>
                </div>
                <div class="stat-box">
                    <h3>40%</h3>
                    <p>{{ $content['site_content']['stat_3'] ?? 'تقليل وقت المتابعة اليومية' }}</p>
                </div>
            </div>
            <div class="testimonial">
                <p>“{{ $content['site_content']['testimonial'] ?? 'LabBridge غيّر طريقة إدارتنا للطلبات. لا فقدان، لا تأخير، كل شيء واضح من أول لحظة.' }}”</p>
                <strong>— {{ $content['site_content']['testimonial_author'] ?? 'مدير معمل أسنان، الرياض' }}</strong>
            </div>
        </div>
    </section>
    <section id="platforms" class="platforms-download">
        <div class="container">
            <h2 class="section-title">
                {{ $content['site_content']['platforms_section']['title'] }}
            </h2>
            <p class="section-subtitle">
                {{ $content['site_content']['platforms_section']['subtitle'] }}
            </p>

            <div class="platforms-two-columns">

                {{-- ====== قسم الجوال (يمين) ====== --}}
                @foreach($content['site_content']['platforms_section']['platforms'] as $platform)
                    @if(!empty($platform['google_play_link']) || !empty($platform['app_store_link']))
                        <div class="platform-box mobile">
                            <h3>{{ $platform['platform_name'] }}</h3>

                            <div class="app-buttons">
                                @if(!empty($platform['google_play_link']))
                                    <a href="{{ $platform['google_play_link'] }}" class="app-btn" target="_blank">
                                        <i class="fab fa-google-play"></i>
                                        <div>
                                            <span>تحميل التطبيق</span>
                                            <strong>Google Play</strong>
                                        </div>
                                    </a>
                                @endif

                                @if(!empty($platform['app_store_link']))
                                    <a href="{{ $platform['app_store_link'] }}" class="app-btn" target="_blank">
                                        <i class="fab fa-apple"></i>
                                        <div>
                                            <span>تحميل التطبيق</span>
                                            <strong>App Store</strong>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- ====== قسم Windows (يسار) ====== --}}
                @foreach($content['site_content']['platforms_section']['platforms'] as $platform)
                    @if(!empty($platform['windows_link']))
                        <div class="platform-box windows">
                            <h3>{{ $platform['platform_name'] }}</h3>

                            <div class="app-buttons">
                                <a href="{{ $platform['windows_link'] }}" class="app-btn" target="_blank">
                                    <i class="fab fa-windows"></i>
                                    <div>
                                        <span>تحميل البرنامج</span>
                                        <strong>Windows</strong>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </section>

    <section id="pricing" class="pricing">
        <div class="container">
            <h2 class="section-title">{{ $content['site_content']['pricing_section']['title'] ?? 'خطط تناسب طموحك' }}</h2>
            <div class="price-cards-container">
                @if(isset($content['site_content']['pricing_section']['plans']) && is_array($content['site_content']['pricing_section']['plans']))
                    @foreach($content['site_content']['pricing_section']['plans'] as $plan)
                        <div class="price-card hidden-anim">
                            <h3>{{ $plan['name'] }}</h3>
                            <p class="price">{{ $plan['price'] }}</p>
                            <ul>
                                @foreach($plan['features'] as $feature)
                                    <li><i class="fas fa-check-circle"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="#pricing" class="btn-primary">{{ __('اشترك الآن') }}</a>
                        </div>
                    @endforeach
                @else
                    <p>لا توجد خطط حالياً لعرضها.</p>
                @endif
            </div>
        </div>
    </section>
    <section class="compliance">
        <div class="container">
            <p>{{ $content['site_content']['compliance_section']['text'] ?? 'LabBridge هو نظام إداري تقني لتنظيم العمليات. النظام لا يقدم أي خدمات طبية أو تشخيصية. جميع المسؤوليات الطبية والقانونية تقع على عاتق الأطباء والمعامل المسجلة.' }}</p>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2 class="section-title">{{ $content['site_content']['faq_section']['title'] ?? 'الأسئلة الشائعة' }}</h2>
            <div class="faq-container">
                @if(isset($content['site_content']['faq_section']['items']) && is_array($content['site_content']['faq_section']['items']))
                    @foreach($content['site_content']['faq_section']['items'] as $item)
                        <details class="faq-item hidden-anim">
                            <summary class="faq-question">
                                {{ $item['question'] }}
                            </summary>

                            <p class="faq-answer">{{ $item['answer'] }}</p>
                        </details>
                    @endforeach
                @else
                    <p>لا توجد أسئلة شائعة حالياً لعرضها.</p>
                @endif
            </div>
        </div>
    </section>

    <!-- Other sections (workflow, platforms, pricing) go here similarly... -->

</main>

<footer>
    <div class="container">
        <p>© 2026 {{ $content['site_content']['site_name'] ?? 'LabBridge' }} - جميع الحقوق محفوظة</p>
        <div class="footer-links">
            <a href="#" class="legal-link">سياسة الخصوصية</a>






            <a href="javascript:void(0)" onclick="openTerms()">شروط الخدمة</a>

        </div>
        <p class="security-note">
            <i class="fas fa-shield-alt"></i>
            جميع المدفوعات مشفرة ومؤمنة.
        </p>
    </div>
</footer>

</body>
</html>
