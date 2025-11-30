<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
        <link rel="icon" type="image/png" href="{{ Storage::disk('central_public')->url('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <!-- Tailwind CSS CDN (if not using local build) -->

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #2a4365;
            /* Darker blue for track */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #63b3ed;
            /* Lighter blue for thumb */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #90cdf4;
            /* Even lighter blue on hover */
        }
    </style>
    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class=" antialiased bg-gray-100">
    <div x-data="{ open: false }" class="bg-gray-50 min-h-screen flex   ">
        <!-- Overlay for mobile sidebar -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
            class="fixed inset-10  bg-black/50 z-0 lg:hidden"></div>
        {{-- Sidebar --}}
      <livewire:land-lord.components.sidebar />
        {{ $slot }}
    </div>
    <!-- Livewire Scripts -->
    @livewireScripts
</body>

</html>
