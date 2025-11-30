<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIHMS-vbeta Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-6">
                <h1 class="text-2xl font-bold">AIHMS-vbeta</h1>
            </div>
            <nav class="mt-6">
                <a href="/docs" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">Introduction</a>
                <a href="/docs/developer-setup" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">Developer Setup</a>
                <a href="/docs/api-reference" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">API Reference</a>
                <a href="/docs/database-schema" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">Database Schema</a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <div class="prose">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>