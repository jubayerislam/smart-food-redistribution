<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>EcoFeed | Join the Mission</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen">
    <div class="flex min-h-screen flex-col items-center justify-center py-12">
        <div class="mb-10 text-center">
            <a href="/" class="flex flex-col items-center gap-3">
                <div
                    class="rounded-3xl bg-emerald-700 p-5 text-white shadow-2xl shadow-emerald-900/30 transition hover:rotate-12 duration-500">
                    <i class="fas fa-leaf text-3xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-black tracking-tight text-slate-900">EcoFeed</p>
                    <p class="text-sm font-bold uppercase tracking-[0.3em] text-emerald-700">Food Rescue</p>
                </div>
            </a>
        </div>

        <div class="w-full {{ $maxWidth ?? 'sm:max-w-2xl' }} px-4">
            <div class="surface-card p-10">
                {{ $slot }}
            </div>
        </div>

        <p class="mt-12 text-center text-sm font-bold text-slate-400">
            &copy; {{ date('Y') }} EcoFeed Smart Systems.
        </p>
    </div>
</body>

</html>