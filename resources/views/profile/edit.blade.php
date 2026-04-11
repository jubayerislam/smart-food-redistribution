@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="mb-10">
        <h2 class="text-4xl font-extrabold mb-4">Account <span class="text-emerald-600">Settings</span></h2>
        <p class="text-gray-600">Manage your profile and security preferences.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1 space-y-4">
            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
                <nav class="space-y-2">
                    <a href="#profile" class="block px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 font-bold border border-emerald-100 text-sm">Profile Information</a>
                    <a href="#security" class="block px-4 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50 transition text-sm">Security & Password</a>
                </nav>
            </div>
            
            <div class="bg-amber-50 p-6 rounded-3xl border border-amber-100">
                <p class="text-xs font-bold text-amber-700 uppercase tracking-widest mb-2">Need assistance?</p>
                <p class="text-xs text-amber-600 leading-relaxed font-medium">To change your organization type or role, please contact our support team at <span class="font-bold">support@ecofeed.com</span></p>
            </div>
        </div>

        <!-- Form Sections -->
        <div class="md:col-span-2 space-y-8">
            <div id="profile" class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div id="security" class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-xl border border-red-50">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
