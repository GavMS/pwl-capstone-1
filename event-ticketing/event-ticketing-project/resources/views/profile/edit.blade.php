<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3]">
        <x-slot name="header">
            <div class="max-w-7xl mx-auto py-8 px-6 lg:px-12">
                <h2 class="text-3xl font-bold text-[#555555] leading-tight lowercase">
                    {{ __('profile settings') }}
                </h2>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-12 space-y-10">

                <div class="p-10 bg-white shadow-sm rounded-[2.5rem]">
                    <div class="max-w-xl">
                        <h3 class="text-xl font-bold text-[#444444] mb-6 lowercase">Profile information</h3>
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-10 bg-white shadow-sm rounded-[2.5rem]">
                    <div class="max-w-xl">
                        <h3 class="text-xl font-bold text-[#444444] mb-6 lowercase">Update password</h3>
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-10 bg-white shadow-sm rounded-[2.5rem] border border-red-50">
                    <div class="max-w-xl">
                        <h3 class="text-xl font-bold text-red-500 mb-6 lowercase">Delete account</h3>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
