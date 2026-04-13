<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EcoFeed | Join the Mission</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ mobileMenuOpen: false }">
    <!-- Navigation (Glass Nav from Mockup) -->
    <nav class="fixed w-full z-50 glass-nav h-16">
        <div class="page-shell">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 cursor-pointer">
                    <div class="bg-emerald-500 p-2 rounded-lg">
                        <i class="fas fa-leaf text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-emerald-600">EcoFeed</span>
                </a>
                
                <div class="hidden md:flex space-x-8 font-medium">
                    <a href="{{ route('home') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('home') ? 'text-emerald-600' : 'text-gray-600' }}">Home</a>
                    <a href="{{ route('donations.index') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('donations.index') ? 'text-emerald-600' : 'text-gray-600' }}">Marketplace</a>
                    <a href="{{ route('impact') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('impact') ? 'text-emerald-600' : 'text-gray-600' }}">Our Impact</a>
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.index') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('admin.*') ? 'text-emerald-600' : 'text-gray-600' }}">Admin</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-gray-600' }}">Dashboard</a>
                        @endif
                        <a href="{{ route('notifications.index') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('notifications.*') ? 'text-emerald-600' : 'text-gray-600' }}">Notifications</a>
                    @endauth
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('notifications.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-500 shadow-sm ring-1 ring-gray-200 transition hover:text-emerald-600">
                            <i class="fas fa-bell"></i>
                            @if(Auth::user()->unreadNotifications()->count() > 0)
                                <span class="absolute -right-1 -top-1 min-w-5 rounded-full bg-rose-500 px-1.5 py-0.5 text-center text-[10px] font-bold leading-none text-white">
                                    {{ Auth::user()->unreadNotifications()->count() }}
                                </span>
                            @endif
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-full font-semibold hover:bg-gray-300 transition text-sm">
                                Sign Out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-emerald-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                            Sign In
                        </a>
                    @endauth
                    
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600">
                        <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-white border-b border-gray-100 shadow-xl fixed w-full z-40">
            <div class="px-6 py-4 space-y-4">
                <a href="{{ route('home') }}" class="block font-bold text-gray-600">Home</a>
                <a href="{{ route('donations.index') }}" class="block font-bold text-gray-600">Marketplace</a>
                <a href="{{ route('impact') }}" class="block font-bold text-gray-600">Our Impact</a>
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.index') }}" class="block font-bold text-gray-600">Admin</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="block font-bold text-gray-600">Dashboard</a>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="block font-bold text-gray-600">Notifications</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block font-bold text-rose-600">Sign Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block font-bold text-emerald-600">Sign In</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="min-h-screen pt-4">
        <!-- Mockup Toast Notification -->
        @if(session('success'))
            <div x-data="{ show: true }" 
                 x-init="setTimeout(() => show = false, 4000)" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-10"
                 class="toast-popup">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-10"
                 class="toast-popup bg-rose-500">
                {{ session('error') }}
            </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Footer (Mockup Fidelity) -->
    <footer class="bg-white py-12 border-t border-gray-100 mt-20">
        <div class="page-shell text-center">
            <div class="flex items-center justify-center gap-2 mb-6">
                <div class="bg-emerald-500 p-2 rounded-lg">
                    <i class="fas fa-leaf text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold tracking-tight text-emerald-600">EcoFeed</span>
            </div>
            <p class="text-gray-500 max-w-md mx-auto mb-8">
                Building a more sustainable world through smart food distribution technology.
            </p>
            <div class="flex justify-center gap-6 mb-8 text-gray-400">
                <i class="fab fa-twitter hover:text-emerald-600 cursor-pointer"></i>
                <i class="fab fa-linkedin hover:text-emerald-600 cursor-pointer"></i>
                <i class="fab fa-instagram hover:text-emerald-600 cursor-pointer"></i>
            </div>
            <p class="text-sm font-bold text-gray-400">&copy; 2024 EcoFeed Smart Systems. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
