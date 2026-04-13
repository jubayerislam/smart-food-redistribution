<x-guest-layout maxWidth="sm:max-w-md">
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Access <span class="text-emerald-600">EcoFeed</span></h2>
        <p class="text-gray-500 mt-2 font-medium uppercase tracking-[0.2em] text-xs">Rescue Management Portal</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6 font-bold text-emerald-600" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-sm font-semibold mb-2 block text-gray-700 ml-1">Email Address</label>
            <div class="relative group">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                    class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium"
                    placeholder="name@example.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold text-red-500" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex justify-between items-center ml-1">
                <label for="password" class="text-sm font-semibold mb-2 block text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition" href="{{ route('password.request') }}">
                        Forgot?
                    </a>
                @endif
            </div>
            <div class="relative group">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                <input id="password" type="password" name="password" required 
                    class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium"
                    placeholder="Enter your password">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-bold text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center px-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="w-5 h-5 rounded-lg border-gray-200 text-emerald-600 focus:ring-emerald-500/20 transition cursor-pointer" name="remember">
                <span class="ms-3 text-sm font-semibold text-gray-500 group-hover:text-emerald-600 transition">Keep me logged in</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition duration-300 group">
                Log In <i class="fas fa-arrow-right ms-2 group-hover:translate-x-1 transition"></i>
            </button>
        </div>

        <div class="text-center pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500 font-medium">New to EcoFeed? 
                <a href="{{ route('register') }}" class="text-emerald-600 font-bold hover:underline ml-1">Create Account</a>
            </p>
        </div>
    </form>
</x-guest-layout>
