<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Barbershop Mr. Klimis - Booking Online</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 dark:bg-gray-900 font-sans antialiased">

    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-black tracking-wider text-gray-900 dark:text-white uppercase">
                    ✂️ Mr. Klimis
                </span>
            </div>
            <div>
                <a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 underline font-medium">
                    Login Admin
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl tracking-tight">
                Sistem Informasi Booking Online
            </h1>
            <p class="mt-3 text-base text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Pangkas rambut tanpa antre lama. Pindai QR Code, tentukan jadwal terbaik Anda, dan rasakan pelayanan pangkas yang nyaman serta teratur.
            </p>
        </div>

        <div class="max-w-2xl mx-auto">
            <livewire:customer.booking-form />
        </div>
    </main>

    <footer class="mt-20 py-6 border-t border-gray-200 dark:border-gray-800 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} Barbershop Mr. Klimis Medan. Hak Cipta Dilindungi.
    </footer>

</body>
</html>
