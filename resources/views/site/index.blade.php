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
        /* VARIABLES            */
        /* ============================= */
        html { scroll-behavior: smooth; }
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #0f172a;
            --accent: #38bdf8;
            --light: #f8fafc;
            --text: #334155;
            --border: #e5e7eb;
            --glass: rgba(255, 255, 255, 0.9);
        }

        /* ============================= */
        /* JAVASCRIPT EFFECTS      */
        /* ============================= */
        .hidden-anim {
            opacity: 0;
            transform: translateY(30px);
            filter: blur(5px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .visible {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }

        /* ============================= */
        /* GLOBAL STYLES         */
        /* ============================= */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            color: var(--text);
            line-height: 1.7;
            background-color: #fff;
            overflow-x: hidden;
        }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 20px; }
        .section-title { text-align: center; margin-bottom: 20px; font-size: 32px; font-weight: 800; color: var(--secondary); }
        .section-subtitle { text-align: center; max-width: 600px; margin: 0 auto 50px auto; font-size: 18px; color: #64748b; }

        /* ============================= */
        /* HEADER            */
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
        header .container { display: flex; justify-content: space-between; align-items: center; }
        .logo-img { height: 50px; }
        nav ul { display: flex; list-style: none; gap: 10px; }
        nav ul li a {
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            transition: all 0.3s;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 15px;
        }
        nav ul li a:hover { color: var(--primary); background-color: #f0f4ff; }

        /* ============================= */
        /* BUTTONS            */
        /* ============================= */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff !important;
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
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(37, 99, 235, 0.3); }

        /* ============================= */
        /* HERO             */
        /* ============================= */
        .hero { padding: 80px 0; background: radial-gradient(circle at 80% 0%, #e0f2fe 0%, transparent 40%), #fff; overflow: hidden; }
        .hero-grid { display: flex; align-items: center; gap: 40px; }
        .hero-content { flex: 1; text-align: right; }
        .hero-content h1 { font-size: 42px; font-weight: 800; line-height: 1.2; color: var(--secondary); margin-bottom: 20px; }
        .hero-content h1 span { color: var(--primary); }
        .hero-content p { font-size: 18px; margin-bottom: 35px; color: #64748b; max-width: 550px; }
        .hero-btns { display: flex; gap: 15px; }
        .hero-mockup { flex: 1; position: relative; text-align: center; }
        .mockup-laptop { width: 100%; max-width: 600px; filter: drop-shadow(0px 20px 40px rgba(0, 0, 0, 0.1)); }

        /* ============================= */
        /* FEATURES            */
        /* ============================= */
        .features { padding: 80px 0; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .feature-card { padding: 40px 30px; background: #fff; border: 1px solid var(--border); border-radius: 24px; transition: all 0.3s ease; text-align: center; }
        .feature-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05); }
        .feature-card i { font-size: 40px; color: var(--primary); margin-bottom: 20px; display: block; }
        .feature-card h3 { margin-bottom: 15px; font-size: 20px; color: var(--secondary); }

        /* ============================= */
        /* SOCIAL PROOF          */
        /* ============================= */
        .social-proof { padding: 80px 0; background-color: var(--light); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 50px; }
        .stat-box { text-align: center; }
        .stat-box h3 { font-size: 42px; font-weight: 800; color: var(--primary); margin-bottom: 5px; }
        .stat-box p { font-weight: 600; color: #64748b; }

        /* ============================= */
        /* PLATFORMS            */
        /* ============================= */
        .platforms-download { padding: 80px 0; background: #fff; }
        .platforms-two-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 40px; }
        .platform-box { background: #f8fafc; border: 1px solid var(--border); border-radius: 24px; padding: 40px; text-align: center; transition: 0.3s; }
        .platform-box:hover { border-color: var(--primary); background: #fff; }
        .platform-box h3 { margin-bottom: 25px; font-size: 22px; font-weight: 800; }
        .app-buttons { display: flex; flex-direction: column; gap: 12px; }
        .app-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 12px 20px;
            background-color: var(--secondary);
            color: #fff !important;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .app-btn:hover { background-color: var(--primary); transform: translateY(-3px); }
        .app-btn i { font-size: 24px; }
        .app-btn div { text-align: right; }
        .app-btn span { display: block; font-size: 11px; opacity: 0.8; }
        .app-btn strong { font-size: 17px; }

        /* ============================= */
        /* PRICING            */
        /* ============================= */
        .pricing { padding: 80px 0; background: var(--light); }
        .price-cards-container { display: flex; justify-content: center; gap: 25px; flex-wrap: wrap; }
        .price-card {
            flex: 1;
            min-width: 300px;
            max-width: 350px;
            background: #fff;
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }
        .price-card:hover { transform: scale(1.02); border-color: var(--primary); }
        .price { font-size: 40px; font-weight: 800; color: var(--primary); margin: 20px 0; }
        .price-card ul { list-style: none; margin: 20px 0; text-align: right; flex-grow: 1; }
        .price-card ul li { margin-bottom: 12px; font-size: 15px; }
        .price-card ul li i { color: #10b981; margin-left: 8px; }

        /* ============================= */
        /* FAQ               */
        /* ============================= */
        .faq { padding: 80px 0; }
        .faq-container { max-width: 800px; margin: 0 auto; }
        .faq-item { border-bottom: 1px solid var(--border); padding: 10px 0; }
        .faq-question { padding: 15px; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center; list-style: none; }
        .faq-question::-webkit-details-marker { display: none; }
        .faq-question::after { content: '\f078'; font-family: 'Font Awesome 6 Free'; font-weight: 900; color: var(--primary); transition: 0.3s; }
        .faq-item[open] .faq-question::after { transform: rotate(180deg); }
        .faq-answer { padding: 0 15px 15px; color: #64748b; }

        /* ============================= */
        /* MODALS             */
        /* ============================= */
        .legal-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .legal-modal.active { display: flex; }
        .legal-box {
            background: #fff;
            width: 100%;
            max-width: 750px;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 20px;
            padding: 40px;
            position: relative;
            animation: modalFade 0.3s ease;
        }
        @keyframes modalFade { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .close-btn { position: absolute; top: 20px; left: 20px; border: none; background: #f1f5f9; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-size: 20px; }
        .law-item { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
        .law-item:last-child { border: none; }
        .law-item h4 { margin-bottom: 10px; color: var(--secondary); }

        /* ============================= */
        /* FOOTER             */
        /* ============================= */
        footer { padding: 60px 0; background: var(--secondary); color: #94a3b8; text-align: center; }
        .footer-links { margin: 20px 0; }
        .footer-links a { color: #fff; text-decoration: none; margin: 0 15px; font-size: 14px; opacity: 0.7; transition: 0.3s; }
        .footer-links a:hover { opacity: 1; }

        /* ============================= */
        /* MEDIA QUERIES          */
        /* ============================= */
        @media (max-width: 992px) {
            .hero-grid { flex-direction: column; text-align: center; }
            .hero-content { text-align: center; order: 1; }
            .hero-content p { margin-left: auto; margin-right: auto; }
            .hero-btns { justify-content: center; }
            .hero-mockup { order: 2; width: 100%; }
        }
        @media (max-width: 768px) {
            nav { display: none; }
            .hero-content h1 { font-size: 32px; }
            .platforms-two-columns { grid-template-columns: 1fr; }
            .section-title { font-size: 26px; }
        }
    </style>
</head>

<body>

    <header>
        <div class="container">
            <a href="#" class="logo">
                <img src="https://labbridge.sarfle.com/images/logowithname_outnew.png" alt="LabBridge Logo" class="logo-img">
            </a>
            <nav>
                <ul>
                    <li><a href="#features">{{ $content['header']['nav_links']['features'] ?? 'المميزات' }}</a></li>
                    <li><a href="#platforms">{{ $content['header']['nav_links']['platforms'] ?? 'المنصات' }}</a></li>
                    <li><a href="#pricing">{{ $content['header']['nav_links']['pricing'] ?? 'الأسعار' }}</a></li>
                </ul>
            </nav>
            <div class="header-cta">
                <a href="#platforms" class="btn-primary">{{ __('حمل التطبيق') }}</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-grid">
                    <div class="hero-content">
                        <h1>
                            {!! str_replace('معمل الأسنان', '<br><span>معمل الأسنان', $content['site_content']['hero']['title'] ?? 'مستقبل إدارة معمل الأسنان') !!}</span>
                        </h1>
                        <p>{{ $content['site_content']['hero']['subtitle'] ?? 'نظام متكامل يربط الطبيب، الفني، والمندوب لإدارة الحالات والطلبات بذكاء.' }}</p>
                        <div class="hero-btns">
                            <a href="#platforms" class="btn-primary">{{ __('ابدأ الآن مجاناً') }}</a>
                        </div>
                    </div>
                    <div class="hero-mockup">
                        <img src="images/mockup.png" alt="LabBridge Mockup" class="mockup-laptop">
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features">
            <div class="container">
                <h2 class="section-title">{{ $content['site_content']['features_title'] ?? 'كل ما تحتاجه في منصة واحدة' }}</h2>
                <div class="features-grid">
                    @if(isset($content['site_content']['features_section']['features']))
                        @foreach ($content['site_content']['features_section']['features'] as $feature)
                            <div class="feature-card hidden-anim">
                                <i class="{{ $feature['icon'] }}"></i>
                                <h3>{{ $feature['title'] }}</h3>
                                <p>{{ $feature['description'] }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        <section class="social-proof">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-box">
                        <h3>+120</h3>
                        <p>{{ $content['site_content']['stat_1'] ?? 'معمل نشط' }}</p>
                    </div>
                    <div class="stat-box">
                        <h3>+18k</h3>
                        <p>{{ $content['site_content']['stat_2'] ?? 'حالة منجزة' }}</p>
                    </div>
                    <div class="stat-box">
                        <h3>40%</h3>
                        <p>{{ $content['site_content']['stat_3'] ?? 'توفير في الوقت' }}</p>
                    </div>
                </div>
                <div class="testimonial">
                    <p>“{{ $content['site_content']['testimonial'] ?? 'نظام رائع سهل علينا متابعة المندوبين والحالات اليومية بدقة متناهية.' }}”</p>
                    <strong>— {{ $content['site_content']['testimonial_author'] ?? 'إدارة معمل تكنودينت' }}</strong>
                </div>
            </div>
        </section>

        <section id="platforms" class="platforms-download">
            <div class="container">
                <h2 class="section-title">{{ $content['site_content']['platforms_section']['title'] ?? 'متوفر على جميع أجهزتك' }}</h2>
                <div class="platforms-two-columns">
                    @foreach($content['site_content']['platforms_section']['platforms'] ?? [] as $platform)
                        <div class="platform-box hidden-anim">
                            <h3>{{ $platform['platform_name'] }}</h3>
                            <div class="app-buttons">
                                @if(!empty($platform['google_play_link']))
                                    <a href="{{ $platform['google_play_link'] }}" class="app-btn" target="_blank">
                                        <i class="fab fa-google-play"></i>
                                        <div><span>تحميل من</span><strong>Google Play</strong></div>
                                    </a>
                                @endif
                                @if(!empty($platform['app_store_link']))
                                    <a href="{{ $platform['app_store_link'] }}" class="app-btn" target="_blank">
                                        <i class="fab fa-apple"></i>
                                        <div><span>تحميل من</span><strong>App Store</strong></div>
                                    </a>
                                @endif
                                @if(!empty($platform['windows_link']))
                                    <a href="{{ $platform['windows_link'] }}" class="app-btn" target="_blank">
                                        <i class="fab fa-windows"></i>
                                        <div><span>تحميل لنسخة</span><strong>Windows PC</strong></div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="pricing" class="pricing">
            <div class="container">
                <h2 class="section-title">{{ $content['site_content']['pricing_section']['title'] ?? 'خطط الأسعار' }}</h2>
                <div class="price-cards-container">
                    @foreach($content['site_content']['pricing_section']['plans'] ?? [] as $plan)
                        <div class="price-card hidden-anim">
                            <h3>{{ $plan['name'] }}</h3>
                            <p class="price">{{ $plan['price'] }}</p>
                            <ul>
                                @foreach($plan['features'] as $feature)
                                    <li><i class="fas fa-check-circle"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="#" class="btn-primary">اشترك الآن</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="faq">
            <div class="container">
                <h2 class="section-title">الأسئلة الشائعة</h2>
                <div class="faq-container">
                    @foreach($content['site_content']['faq_section']['items'] ?? [] as $item)
                        <details class="faq-item">
                            <summary class="faq-question">{{ $item['question'] }}</summary>
                            <div class="faq-answer">{{ $item['answer'] }}</div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <div id="privacyModal" class="legal-modal">
        <div class="legal-box">
            <button class="close-btn" onclick="closeModal('privacyModal')">×</button>
            <h2 class="section-title">سياسة الخصوصية</h2>
            <div class="legal-content">
                @foreach(($content['Privacy_Policy']['laws'] ?? []) as $law)
                    <div class="law-item">
                        <h4>{{ $law['title'] }}</h4>
                        <p>{{ implode(' ', $law['content'] ?? []) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="termsModal" class="legal-modal">
        <div class="legal-box">
            <button class="close-btn" onclick="closeModal('termsModal')">×</button>
            <h2 class="section-title">شروط الخدمة</h2>
            <div class="legal-content">
                @foreach(($content['Terms_Of_Service']['terms'] ?? []) as $term)
                    <div class="law-item">
                        <h4>{{ $term['title'] }}</h4>
                        <p>{{ implode(' ', $term['content'] ?? []) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>{{ $content['site_content']['footer']['copyright'] ?? '© 2026 LabBridge - جميع الحقوق محفوظة' }}</p>
            <div class="footer-links">
                <a href="javascript:void(0)" onclick="openModal('privacyModal')">سياسة الخصوصية</a>
                <a href="javascript:void(0)" onclick="openModal('termsModal')">شروط الخدمة</a>
            </div>
            <p class="security-note"><i class="fas fa-shield-alt"></i> جميع بياناتكم مشفرة ومحمية بالكامل.</p>
        </div>
    </footer>

    <script>
        // 1. Animation on Scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.hidden-anim').forEach(el => observer.observe(el));

        // 2. Modals Control
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden'; // منع التمرير عند فتح المودال
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // إغلاق المودال عند الضغط خارج الصندوق
        window.onclick = function(event) {
            if (event.target.classList.contains('legal-modal')) {
                event.target.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        // 3. FAQ Single Open Logic
        document.querySelectorAll('details').forEach((item) => {
            item.addEventListener('click', (e) => {
                document.querySelectorAll('details').forEach((other) => {
                    if (other !== item) other.removeAttribute('open');
                });
            });
        });
    </script>
</body>
</html>