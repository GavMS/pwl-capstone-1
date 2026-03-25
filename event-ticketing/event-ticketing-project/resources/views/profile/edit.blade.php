<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        profile settings.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        manage your account details
                    </p>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 flex flex-col gap-10">

            <div class="p-8 lg:p-10 bg-white shadow-sm rounded-[2.5rem]">
                <div class="max-w-xl">
                    <h3 class="text-2xl font-bold text-[#444444] mb-2 lowercase tracking-tight">profile information</h3>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-8 lg:p-10 bg-white shadow-sm rounded-[2.5rem]">
                <div class="max-w-xl">
                    <h3 class="text-2xl font-bold text-[#444444] mb-2 lowercase tracking-tight">update password</h3>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-8 lg:p-10 bg-white shadow-sm rounded-[2.5rem] border border-red-50 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full opacity-50"></div>
                <div class="max-w-xl relative w-full z-10">
                    <h3 class="text-2xl font-bold text-red-500 mb-2 lowercase tracking-tight">delete account</h3>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </main>
    </div>
</x-app-layout>
