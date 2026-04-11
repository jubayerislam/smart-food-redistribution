<section class="max-w-xl">
    <header>
        <h2 class="text-2xl font-black text-slate-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-2 text-sm font-semibold text-slate-500 italic">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('put')

        <div class="space-y-2">
            <label for="update_password_current_password" class="field-label ml-1">Current Password</label>
            <input id="update_password_current_password" name="current_password" type="password" class="field-input" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-xs font-bold text-rose-500" />
        </div>

        <div class="space-y-2">
            <label for="update_password_password" class="field-label ml-1">New Password</label>
            <input id="update_password_password" name="password" type="password" class="field-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-xs font-bold text-rose-500" />
        </div>

        <div class="space-y-2">
            <label for="update_password_password_confirmation" class="field-label ml-1">Confirm New Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="field-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-xs font-bold text-rose-500" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="primary-btn py-3 px-8 text-sm shadow-xl active:scale-95 transition">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-bold text-emerald-600"
                >{{ __('Password changed successfully!') }}</p>
            @endif
        </div>
    </form>
</section>
