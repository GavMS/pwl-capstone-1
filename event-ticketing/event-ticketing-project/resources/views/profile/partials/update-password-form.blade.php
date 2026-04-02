<section x-data="{ showFields: false }">
    <form id="password-update-form" method="post" action="{{ route('password.update') }}" class="space-y-8">
        @csrf
        @method('put')

        <div class="flex flex-col gap-2 relative">
            <div class="flex justify-between items-center mb-1">
                <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1" />
                <button type="button"
                    x-on:click="showFields = !showFields"
                    class="text-xs font-extrabold text-[#555555] lowercase hover:text-black transition flex items-center gap-1">
                    <span x-text="showFields ? 'cancel' : 'change password'"></span>
                </button>
            </div>
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner transition-all"
                autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- Hidden Fields with Animation -->
        <div x-show="showFields" x-collapse x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-50 pt-8 animate-fade-in">
            <div class="flex flex-col gap-2">
                <x-input-label for="update_password_password" :value="__('New Password')" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1" />
                <x-text-input id="update_password_password" name="password" type="password"
                    class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner"
                    autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-2">
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner"
                    autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4" x-show="showFields" x-transition>
            <button type="button" onclick="confirmUpdate('password-update-form')"
                class="px-10 py-4 bg-[#555555] text-white rounded-2xl font-bold text-sm hover:bg-black transition-all lowercase shadow-lg shadow-gray-100">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-green-600 lowercase"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
