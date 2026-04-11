<section class="max-w-xl">
    <header>
        <h2 class="text-2xl font-black text-rose-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-sm font-semibold text-rose-600 italic">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <button
        class="secondary-btn border-rose-200 bg-rose-50 text-rose-700 py-3 px-8 text-xs font-black shadow-xl mt-8 transition active:scale-95"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account Permanently') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-10 text-center">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-black text-slate-900">
                Are you absolutely sure?
            </h2>

            <p class="mt-4 text-sm font-semibold text-slate-500 leading-relaxed">
                {{ __('Once your account is deleted, all of its data will be gone forever. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-8 space-y-2 text-left">
                <label for="password" class="field-label ml-1">Password</label>
                <input id="password" name="password" type="password" class="field-input" placeholder="••••••••" required />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs font-bold text-rose-500" />
            </div>

            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <button type="button" x-on:click="$dispatch('close')" class="secondary-btn py-3 px-10 text-xs font-bold">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="primary-btn bg-rose-600 py-3 px-10 text-xs font-black hover:bg-rose-700">
                    {{ __('Permanently Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
