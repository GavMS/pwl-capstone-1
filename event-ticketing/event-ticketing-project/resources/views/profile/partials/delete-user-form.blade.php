<section class="space-y-6">
    <header>
        <p class="mt-1 text-sm text-[#777777] font-medium lowercase mb-8 leading-relaxed">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-10 py-4 bg-red-500 text-white rounded-2xl font-bold text-sm hover:bg-red-600 transition lowercase shadow-lg shadow-red-50"
    >{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-10 bg-white rounded-[2.5rem]">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-extrabold text-[#444444] tracking-tight lowercase">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-3 text-sm text-[#777777] font-medium lowercase leading-relaxed">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
            </p>

            <div class="mt-8 flex flex-col gap-2">
                <x-input-label for="password" value="{{ __('Password') }}" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner"
                    placeholder="{{ __('••••••••') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 bg-[#F4F4F4] text-[#777777] rounded-xl font-bold text-sm hover:bg-gray-200 transition lowercase">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="px-8 py-4 bg-red-500 text-white rounded-xl font-bold text-sm hover:bg-red-600 transition lowercase">
                    {{ __('Permanently Delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
