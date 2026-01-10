<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $content['site_content']['site_name']['value'] ?? 'LabBridge' }}</title>
    <meta name="description" content="{{ $content['site_content']['hero']['value']['subtitle']['value'] ?? '' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            max-width: 600px;
            margin: 0 auto;
        }
        section {
            max-width: 1000px;
            margin: 70px auto;
            padding: 0 20px;
        }
        section h2 {
            font-size: 32px;
            color: #1e40af;
            margin-bottom: 40px;
            text-align: center;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            text-align: center;
        }
        .card i {
            font-size: 2.5rem;
            color: #2563eb;
            margin-bottom: 15px;
        }
        .card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #1f2937;
        }
        .card p {
            font-size: 16px;
            line-height: 1.7;
            color: #4b5563;
        }
        .social-proof {
            text-align: center;
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }
        .stat h3 {
            font-size: 36px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .stat p {
            font-size: 16px;
            color: #4b5563;
        }
        .testimonial {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        }
        .testimonial p {
            font-size: 18px;
            font-style: italic;
            color: #374151;
            line-height: 1.8;
        }
        .testimonial span {
            display: block;
            margin-top: 20px;
            font-weight: bold;
            color: #1e40af;
        }
        .platforms a {
            text-decoration: none;
        }
        .platforms .card:hover {
            transform: translateY(-5px);
            transition: transform 0.2s;
        }
        .platform-links a {
            display: inline-block;
            margin: 5px;
            padding: 8px 15px;
            background: #eef2ff;
            color: #3730a3;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .platform-links a:hover {
            background: #c7d2fe;
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
    <h1>{{ $content['site_content']['hero']['value']['title']['value'] ?? 'إدارة مختبر الأسنان' }}</h1>
    <p>{{ $content['site_content']['hero']['value']['subtitle']['value'] ?? '' }}</p>
</header>

<section>
    <h2>{{ $content['site_content']['features_title']['value'] ?? 'المميزات' }}</h2>
    <div class="grid-container">
        @foreach($content['site_content']['features'] ?? [] as $feature)
            <div class="card">
                @if(!empty($feature['value']['icon']['value']))
                    <i class="{{ $feature['value']['icon']['value'] }}"></i>
                @endif
                <h3>{{ $feature['value']['title']['value'] ?? 'ميزة جديدة' }}</h3>
                <p>{{ $feature['value']['description']['value'] ?? 'وصف الميزة هنا' }}</p>
            </div>
        @endforeach
    </div>
</section>

<section>
    <h2>{{ $content['site_content']['workflow_title']['value'] ?? 'كيف نعمل؟' }}</h2>
    <div class="grid-container">
        @foreach($content['site_content']['workflow_steps'] ?? [] as $step)
            <div class="card">
                @if(!empty($step['value']['icon']['value']))
                    <i class="{{ $step['value']['icon']['value'] }}"></i>
                @endif
                <h3>{{ $step['value']['title']['value'] ?? '' }}</h3>
                <p>{{ $step['value']['description']['value'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="social-proof">
    <h2>{{ $content['site_content']['social_proof_title']['value'] ?? 'موثوق به' }}</h2>
    <div class="stats">
        @foreach($content['site_content']['social_proof']['value']['stats'] ?? [] as $stat)
            <div class="stat">
                <h3>{{ $stat['value']['number']['value'] ?? '0' }}</h3>
                <p>{{ $stat['value']['label']['value'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
    @if(!empty($content['site_content']['social_proof']['value']['testimonial']))
        <div class="testimonial">
            <p>{{ $content['site_content']['social_proof']['value']['testimonial']['value']['quote']['value'] ?? '' }}</p>
            <span>- {{ $content['site_content']['social_proof']['value']['testimonial']['value']['author']['value'] ?? '' }}</span>
        </div>
    @endif
</section>

<section class="platforms">
    <h2>{{ $content['site_content']['platforms_title']['value'] ?? 'منصاتنا' }}</h2>
    <div class="grid-container">
        @foreach($content['site_content']['platforms'] ?? [] as $platform)
            @if($platform['key'] == 'platform_mobile')
                <div class="card">
                    <h3>{{ $platform['value']['platform_name']['value'] ?? '' }}</h3>
                    <div class="platform-links">
                        @foreach($platform['value']['app_links'] as $link)
                            <a href="{{ $link['url']['value'] }}" target="_blank">
                                {{ $link['store_name']['value'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @elseif($platform['key'] == 'platform_web')
                <a href="{{ $platform['value']['url']['value'] }}" target="_blank" class="card">
                    <h3>{{ $platform['value']['platform_name']['value'] ?? '' }}</h3>
                    <p>اضغط هنا للوصول إلى المنصة</p>
                </a>
            @endif
        @endforeach
    </div>
</section>

<footer>
    <p>&copy; {{ date('Y') }} {{ $content['site_content']['site_name']['value'] ?? 'LabBridge' }}. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
