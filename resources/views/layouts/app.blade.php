<!DOCTYPE html>
<html lang="en" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Academia Training Solutions — Professional Training Across Europe' }}</title>

    <meta name="description" content="{{ $description ?? 'Practical, expert-led professional training delivered online, onsite and in classrooms across Europe. Over 500 courses in business, finance, technology, supply chain, HR and compliance.' }}">
    <meta name="robots" content="{{ $robots ?? 'index,follow' }}">
    @isset($canonical)<link rel="canonical" href="{{ $canonical }}">@endisset

    <meta property="og:site_name" content="Academia Training Solutions">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $title ?? 'Academia Training Solutions' }}">
    <meta property="og:url" content="{{ url()->current() }}">

    {{--
        Fonts are self-hosted via @fontsource, not loaded from Google's CDN.
        Requesting them from Google transmits the visitor's IP to a US server
        without consent — a German court awarded damages for exactly that
        (LG München I, 20 Jan 2022, 3 O 17493/20).
    --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('schema')
</head>
<body class="min-h-screen bg-white">

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-3 focus:rounded-lg focus:bg-sand-900 focus:px-4 focus:py-2 focus:text-white">
        Skip to content
    </a>

    <x-promo-bar />
    <x-site-header />

    <main id="main">
        {{ $slot }}
    </main>

    <x-site-footer />

    @livewireScripts
</body>
</html>
