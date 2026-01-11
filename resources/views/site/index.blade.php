<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content['site_name']['value'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet"> <!-- لجلب أيقونات FontAwesome -->
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
        <h1>{{ $content['hero']['value']['title']['value'] }}</h1>
        <h2>{{ $content['hero']['value']['subtitle']['value'] }}</h2>
    </header>

    <section>
        <h3>{{ $content['features_title']['value'] }}</h3>
        <div class="features">
            @foreach ($content['features'] as $feature)
                <div class="feature-item">
                    <i class="{{ $feature['value']['icon']['value'] }}"></i>
                    <h4>{{ $feature['value']['title']['value'] }}</h4>
                    <p>{{ $feature['value']['description']['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <h3>{{ $content['social_proof_title']['value'] }}</h3>
        <div class="social-proof">
            @foreach ($content['social_proof']['value']['stats'] as $stat)
                <div>
                    <strong>{{ $stat['value']['number']['value'] }}</strong> - {{ $stat['value']['label']['value'] }}
                </div>
            @endforeach
            <blockquote>
                <p>{{ $content['social_proof']['value']['testimonial']['value']['quote']['value'] }}</p>
                <footer>- {{ $content['social_proof']['value']['testimonial']['value']['author']['value'] }}</footer>
            </blockquote>
        </div>
    </section>

    <section>
        <h3>{{ $content['workflow_title']['value'] }}</h3>
        <div class="workflow">
            @foreach ($content['workflow_steps'] as $step)
                <div class="workflow-step">
                    <i class="{{ $step['value']['icon']['value'] }}"></i>
                    <h4>{{ $step['value']['title']['value'] }}</h4>
                    <p>{{ $step['value']['description']['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <h3>{{ $content['platforms_title']['value'] }}</h3>
        <div class="platforms">
            @foreach ($content['platforms'] as $platform)
                <div class="platform-item">
                    <h4>{{ $platform['value']['platform_name']['value'] }}</h4>
                    @if (isset($platform['value']['app_links']))
                        @foreach ($platform['value']['app_links'] as $app_link)
                            <a href="{{ $app_link['url']['value'] }}" target="_blank">{{ $app_link['store_name']['value'] }} - {{ $app_link['platform']['value'] }}</a><br>
                        @endforeach
                    @else
                        <a href="{{ $platform['value']['url']['value'] }}" target="_blank">زيارة الموقع</a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

</body>
</html>
