<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Aplikasi - Mr. Klimis</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-900 via-gray-900 to-slate-900">

        <div class="mb-4">
            <a href="/" class="text-2xl font-black tracking-widest text-white uppercase flex items-center gap-2">
                ✂️ MR. KLIMIS
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-gray-800/40 backdrop-blur-sm shadow-xl border border-gray-700/40 sm:rounded-xl overflow-hidden">
            {{ $slot }}
        </div>

    </div>
</body>
</html>
