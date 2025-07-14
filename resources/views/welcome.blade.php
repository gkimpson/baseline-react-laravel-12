<!doctype html>
<html lang="en" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Get started with a collection of over 53 page templates based on Tailwind CSS for Marketing UI purposes including landing pages, contact pages, about pages, and more">
    <meta name="author" content="Flowbite">
    <meta name="generator" content="Hugo 0.148.1">
    <title>Tailwind CSS Mobile Application Landing Page - Flowbite</title>
    <link rel="canonical" href="http://localhost:1313/landing/mobile-application/">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flowbite.css') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="http://localhost:1313/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="http://localhost:1313/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="http://localhost:1313/favicon-16x16.png">
    <link rel="icon" type="image/png" href="http://localhost:1313/favicon.ico">
    <link rel="manifest" href="http://localhost:1313/site.webmanifest">
    <link rel="mask-icon" href="http://localhost:1313/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@">
    <meta name="twitter:creator" content="@">
    <meta name="twitter:title" content="Tailwind CSS Mobile Application Landing Page - Flowbite">
    <meta name="twitter:description" content="Get started with a collection of over 53 page templates based on Tailwind CSS for Marketing UI purposes including landing pages, contact pages, about pages, and more">
    <meta name="twitter:image" content="http://localhost:1313/marketing-ui/demo/images/og-image.jpg">
    <!-- Facebook -->
    <meta property="og:url" content="http://localhost:1313/landing/mobile-application/">
    <meta property="og:title" content="Tailwind CSS Mobile Application Landing Page - Flowbite">
    <meta property="og:description" content="Get started with a collection of over 53 page templates based on Tailwind CSS for Marketing UI purposes including landing pages, contact pages, about pages, and more">
    <meta property="og:type" content="article">
    <meta property="og:image" content="http://localhost:1313/marketing-ui/demo/images/og-image.jpg">
    <meta property="og:image:type" content="image/png">
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>


<body class="bg-gray-50 dark:bg-gray-800">

@include('index-header')



<main class="w-full antialiased">
    @include('index-hero')
    @include('index-features')
    @include('index-device-showcase')
    @include('index-testimonials')
    @include('index-cta')
    @include('index-stats')
    @include('index-faq')
    @include('index-newsletter')
    @include('index-footer')
</main>

<script src="{{ asset('js/flowbite.js') }}"></script>
</body>

</html>
