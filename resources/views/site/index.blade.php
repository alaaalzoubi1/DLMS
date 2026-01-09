<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ $content['site_name'] ?? 'My App' }}</title>
    <meta name="description" content="{{ $content['site_description'] ?? '' }}">
</head>
<body>

<header>
    <h1>{{ $content['hero_title'] ?? 'عنوان افتراضي' }}</h1>
    <p>{{ $content['hero_subtitle'] ?? '' }}</p>
</header>

<section>
    <h2>عن التطبيق</h2>
    <p>{{ $content['about_text'] ?? '' }}</p>
</section>

<footer>
    <p>© {{ date('Y') }} {{ $content['site_name'] ?? '' }}</p>
</footer>

</body>
</html>
