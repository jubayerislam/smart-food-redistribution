<section>
    <header class="mb-8">
        <h3 class="text-xl font-bold text-gray-900">
            {{ __('Profile Information') }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-2">
            <label for="name" class="block text-sm font-semibold text-gray-700 ml-1">Full Name</label>
            <input id="name" name="name" type="text" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium" value="{{ old('name', $user->name) }}" required autofocus />
            <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <label for="email" class="block text-sm font-semibold text-gray-700 ml-1">Email Address</label>
            <input id="email" name="email" type="email" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium" value="{{ old('email', $user->email) }}" required />
            <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('email')" />
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="space-y-2">
                <label for="organization_name" class="block text-sm font-semibold text-gray-700 ml-1">Organization Name</label>
                <input id="organization_name" name="organization_name" type="text" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-medium" value="{{ old('organization_name', $user->organization_name) }}" />
                <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('organization_name')" />
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 ml-1">Account Type</label>
                <div class="w-full rounded-xl border border-gray-200 bg-gray-100 p-3 font-semibold capitalize text-gray-600">
                    {{ $user->role }}
                </div>
                <p class="text-xs font-medium text-gray-400">Role changes stay locked to keep donation history consistent.</p>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-50">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs font-bold text-emerald-600">
                    {{ __('Saved successfully!') }}
                </p>
            @endif
        </div>
    </form>
</section>
