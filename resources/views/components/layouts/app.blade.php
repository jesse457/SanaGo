<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  
      @googlefonts

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

    {{ $slot }}

    <!-- Livewire Scripts -->
    @livewireScripts
</body>

</html>
