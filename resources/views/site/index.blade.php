<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content['site_name'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background-color: #f4f4f9;
            color: #333;
        }
        h1, h2 {
            color: #333;
        }
        .feature-item, .workflow-step, .platform-item {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>{{ $content['hero']['title'] }}</h1>
        <h2>{{ $content['hero']['subtitle'] }}</h2>
    </header>

    <section>
        <h3>{{ $content['features_title'] }}</h3>
        <div class="features">
            @foreach ($content['features'] as $feature)
                <div class="feature-item">
                    <i class="{{ $feature['icon'] }}"></i>
                    <h4>{{ $feature['title'] }}</h4>
                    <p>{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <h3>{{ $content['social_proof_title'] }}</h3>
        <div class="social-proof">
            @foreach ($content['social_proof']['stats'] as $stat)
                <div>
                    <strong>{{ $stat['number'] }}</strong> - {{ $stat['label'] }}
                </div>
            @endforeach
            <blockquote>
                <p>{{ $content['social_proof']['testimonial']['quote'] }}</p>
                <footer>- {{ $content['social_proof']['testimonial']['author'] }}</footer>
            </blockquote>
        </div>
    </section>

    <section>
        <h3>{{ $content['workflow_title'] }}</h3>
        <div class="workflow">
            @foreach ($content['workflow_steps'] as $step)
                <div class="workflow-step">
                    <i class="{{ $step['icon'] }}"></i>
                    <h4>{{ $step['title'] }}</h4>
                    <p>{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <h3>{{ $content['platforms_title'] }}</h3>
        <div class="platforms">
            @foreach ($content['platforms'] as $platform)
                <div class="platform-item">
                    <h4>{{ $platform['platform_name'] }}</h4>
                    @if (isset($platform['app_links']))
                        @foreach ($platform['app_links'] as $app_link)
                            <a href="{{ $app_link['url'] }}" target="_blank">{{ $app_link['store_name'] }} - {{ $app_link['platform'] }}</a><br>
                        @endforeach
                    @else
                        <a href="{{ $platform['url'] }}" target="_blank">زيارة الموقع</a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

</body>
</html>
