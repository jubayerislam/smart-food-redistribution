@extends('layouts.app')

@section('content')
<section id="home" class="pt-20">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-white py-24 sm:py-32">
        <div class="page-shell">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="relative z-10">
                    <h1 class="text-5xl font-extrabold tracking-tight sm:text-7xl mb-6">
                        Smart Redistribution for a <span class="gradient-text">Zero Waste</span> Future.
                    </h1>
                    <p class="text-lg leading-8 text-gray-600 mb-10">
                        Our platform connects restaurants and grocery stores with surplus food to local charities and communities in need. Real-time tracking, impact metrics, and zero hunger.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @auth
                            <a href="{{ route('donations.create') }}" class="primary-btn px-8 py-4 text-lg font-bold">
                                Start Donating
                            </a>
                            <a href="{{ route('donations.index') }}" class="secondary-btn px-8 py-4 text-lg font-bold">
                                Browse Food
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="primary-btn px-8 py-4 text-lg font-bold">
                                Create Account
                            </a>
                            <a href="{{ route('login') }}" class="secondary-btn px-8 py-4 text-lg font-bold">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="relative mt-8 lg:mt-0">
                    <div class="bg-emerald-100 rounded-3xl p-8 aspect-square flex items-center justify-center relative overflow-hidden shadow-inner">
                        <i class="fas fa-shipping-fast text-[12rem] text-emerald-500 opacity-20 absolute -bottom-10 -right-10 pointer-events-none"></i>
                        <div class="bg-white p-6 rounded-2xl shadow-2xl relative z-10 w-full border border-gray-50 max-w-sm">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-bold text-emerald-600 uppercase tracking-wider">Live Activity</span>
                                <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                            </div>
                            <div class="space-y-4">
                                @forelse($recentDonations as $donation)
                                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg transition hover:bg-emerald-50 border border-gray-100/50">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-sm">
                                            <i class="fas fa-{{ $donation->food_category === 'Cooked Meals' ? 'utensils' : 'shopping-basket' }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800 leading-tight">
                                                {{ $donation->donor->organization_name ?? $donation->donor->name }} donated {{ $donation->quantity }}
                                            </p>
                                            <p class="text-xs text-gray-500 font-medium mt-1">{{ $donation->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 italic p-3 text-center">No recent activity yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-emerald-900 py-16">
        <div class="page-shell">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <p class="text-4xl font-bold">{{ number_format($stats['estimated_meals']) }}</p>
                    <p class="text-emerald-300">Meals Saved</p>
                </div>
                <div>
                    <p class="text-4xl font-bold">{{ $stats['donor_count'] }}</p>
                    <p class="text-emerald-300">Active Donors</p>
                </div>
                <div>
                    <p class="text-4xl font-bold">{{ $stats['receiver_count'] }}</p>
                    <p class="text-emerald-300">NGO Partners</p>
                </div>
                <div>
                    <p class="text-4xl font-bold">{{ number_format($stats['co2_offset_kg'], 1) }}</p>
                    <p class="text-emerald-300">CO2 Offset (kg)</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
