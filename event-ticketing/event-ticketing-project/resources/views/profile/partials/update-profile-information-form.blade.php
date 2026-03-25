<section>
    <header>
        <p class="mt-1 text-sm text-[#777777] font-medium lowercase mb-8">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

<section x-data="{ isEditing: false }">
    <form id="profile-info-form" method="post" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex flex-col gap-2">
                <x-input-label for="name" :value="__('Full Name')" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1" />
                <x-text-input id="name" name="name" type="text" 
                    class="w-full px-6 py-4 border-none rounded-2xl font-bold text-[#444444] lowercase focus:ring-2 focus:ring-[#555555] shadow-inner transition-all disabled:opacity-60" 
                    x-bind:class="isEditing ? 'bg-[#F4F4F4]' : 'bg-transparent shadow-none cursor-default'"
                    x-bind:readonly="!isEditing"
                    :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="flex flex-col gap-2">
                <x-input-label for="username" :value="__('Username')" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1" />
                <x-text-input id="username" name="username" type="text"
                    class="w-full px-6 py-4 border-none rounded-2xl font-bold text-[#444444] lowercase focus:ring-2 focus:ring-[#555555] shadow-inner transition-all disabled:opacity-60"
                    x-bind:class="isEditing ? 'bg-[#F4F4F4]' : 'bg-transparent shadow-none cursor-default'"
                    x-bind:readonly="!isEditing"
                    :value="old('username', $user->username)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <x-input-label for="email" :value="__('Email Address')" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1" />
            <x-text-input id="email" name="email" type="email" 
                class="w-full px-6 py-4 border-none rounded-2xl font-bold text-[#444444] lowercase focus:ring-2 focus:ring-[#555555] shadow-inner transition-all disabled:opacity-60" 
                x-bind:class="isEditing ? 'bg-[#F4F4F4]' : 'bg-transparent shadow-none cursor-default'"
                x-bind:readonly="!isEditing"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <template x-if="!isEditing">
                <button type="button" x-on:click="isEditing = true" class="px-10 py-4 bg-[#F4F4F4] text-[#555555] rounded-2xl font-bold text-sm hover:bg-[#EBEBEB] transition-all lowercase border border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    edit profile
                </button>
            </template>

            <template x-if="isEditing">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="confirmUpdate('profile-info-form')" class="px-10 py-4 bg-[#555555] text-white rounded-2xl font-bold text-sm hover:bg-black transition-all lowercase shadow-lg shadow-gray-100">
                        save changes
                    </button>
                    <button type="button" x-on:click="isEditing = false" class="px-6 py-4 text-[#777777] font-bold text-sm lowercase hover:text-black transition">
                        cancel
                    </button>
                </div>
            </template>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-green-600 lowercase">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
