<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="globalApp" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <!-- 1. Critical Dark Mode Script (Prevents White Flash) -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        /* Global Background Animations */
        .circles { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; margin: 0; padding: 0; }
        .circles li { position: absolute; display: block; list-style: none; width: 20px; height: 20px; background: linear-gradient(to top right, rgba(59, 130, 246, 0.2), rgba(168, 85, 247, 0.2)); filter: blur(8px); animation: circleAnimate 25s linear infinite; bottom: -150px; border-radius: 50%; }
        .dark .circles li { background: linear-gradient(to top right, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0)); }
        @keyframes circleAnimate {
            0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.8; }
            100% { transform: translateY(-1000px) rotate(720deg) scale(1.5); opacity: 0; }
        }
        /* Scroll Reveal */
        .reveal-on-scroll { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
        .reveal-on-scroll.animate-in { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900 dark:bg-[#0B1120] dark:text-slate-50 min-h-screen">

  {{ $slot }}

    @livewireScripts

</body>
</html>
