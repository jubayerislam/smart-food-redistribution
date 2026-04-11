<x-guest-layout maxWidth="registration-card">
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Create <span class="text-emerald-600">Account</span></h2>
        <p class="text-gray-500 mt-2 font-medium uppercase tracking-[0.2em] text-xs">Start Your Food Rescue Journey</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="name" class="text-sm font-semibold mb-2 block text-gray-700 ml-1">Full Name</label>
                <div class="relative group">
                    <i
                        class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium"
                        placeholder="John Doe">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs font-bold text-red-500" />
            </div>

            <div class="space-y-2">
                <label for="email" class="text-sm font-semibold mb-2 block text-gray-700 ml-1">Email Address</label>
                <div class="relative group">
                    <i
                        class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium"
                        placeholder="john@example.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold text-red-500" />
            </div>
        </div>

        <div class="space-y-2">
            <label for="role" class="text-sm font-semibold mb-2 block text-gray-700 ml-1">I am a...</label>
            <div class="relative group">
                <i
                    class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                <select id="role" name="role" required
                    class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-bold appearance-none cursor-pointer">
                    <option value="donor">Donor (Restaurant/Store)</option>
                    <option value="receiver">Receiver (NGO/Charity)</option>
                </select>
                <i
                    class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition pointer-events-none"></i>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-xs font-bold text-red-500" />
        </div>

        <div class="space-y-2">
            <label for="organization_name" class="text-sm font-semibold mb-2 block text-gray-700 ml-1">Organization Name
                (Optional)</label>
            <div class="relative group">
                <i
                    class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                <input id="organization_name" type="text" name="organization_name"
                    value="{{ old('organization_name') }}"
                    class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium"
                    placeholder="e.g. Green Grocers">
            </div>
            <x-input-error :messages="$errors->get('organization_name')" class="mt-2 text-xs font-bold text-red-500" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="password" class="text-sm font-semibold mb-2 block text-gray-700 ml-1">Password</label>
                <div class="relative group">
                    <i
                        class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                    <input id="password" type="password" name="password" required
                        class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-bold text-red-500" />
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-semibold mb-2 block text-gray-700 ml-1">Confirm
                    Password</label>
                <div class="relative group">
                    <i
                        class="fas fa-shield-check absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full p-3 pl-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium"
                        placeholder="••••••••">
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition duration-300 group">
                Create Account <i class="fas fa-user-plus ms-2 group-hover:scale-110 transition"></i>
            </button>
        </div>

        <div class="text-center pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Already have an account?
                <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline ml-1">Log In</a>
            </p>
        </div>
    </form>
</x-guest-layout>