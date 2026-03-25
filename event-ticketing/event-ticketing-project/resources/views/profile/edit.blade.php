<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10 transition-all">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        profile settings.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        manage your account & personal details
                    </p>
                </div>
                <div class="flex gap-2">
                    <span class="px-4 py-2 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold text-xs lowercase border border-gray-200">
                        {{ auth()->user()->role }} account
                    </span>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Left Sidebar: Profile Summary & Action -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="bg-white rounded-[2.5rem] shadow-sm overflow-hidden border border-gray-100 flex flex-col items-center p-10 relative">
                        <!-- Decorative background -->
                        <div class="absolute inset-x-0 top-0 h-32 bg-[#F4F4F4]"></div>
                        
                        <!-- Avatar Placeholder -->
                        <div class="relative mt-8 group">
                            <div class="w-32 h-32 rounded-[2rem] bg-white p-1 shadow-md border border-gray-100 relative z-10">
                                <div class="w-full h-full rounded-[1.8rem] bg-[#E5E5E3] flex items-center justify-center text-[#999999] overflow-hidden group-hover:bg-gray-200 transition duration-500">
                                    <svg class="w-16 h-16 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            </div>
                            <button class="absolute -right-2 -bottom-2 w-10 h-10 bg-[#555555] text-white rounded-xl shadow-lg flex items-center justify-center hover:bg-black transition-all z-20 group-hover:scale-110 active:scale-90">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </button>
                        </div>

                        <div class="mt-8 text-center mb-8">
                            <h4 class="text-2xl font-extrabold text-[#444444] tracking-tight lowercase line-clamp-1">{{ auth()->user()->name }}</h4>
                            <p class="text-sm font-bold text-[#777777] lowercase tracking-wide mt-1">{{ auth()->user()->email }}</p>
                        </div>

                        <!-- Action Button (Delete) -->
                        <div class="w-full pt-8 border-t border-gray-50 flex flex-col gap-4">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <!-- Right Column: Unified Personal Info Card -->
                <div class="lg:col-span-8 flex flex-col gap-8">
                    
                    <div class="p-10 bg-white shadow-sm rounded-[2.5rem] border border-gray-100 animate-fade-in relative overflow-hidden group">
                        <div class="absolute -right-16 -top-16 w-48 h-48 bg-[#F9F9F8] rounded-full group-hover:scale-110 transition duration-1000 rotate-12"></div>
                        
                        <div class="relative z-10 w-full flex flex-col gap-12">
                            
                            <!-- Part 1: Unified Flow Header -->
                            <div>
                                <h3 class="text-2xl font-extrabold text-[#444444] mb-8 lowercase tracking-tight border-b border-gray-50 pb-6 flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-[#555555] rounded-full"></div>
                                    personal information
                                </h3>
                                
                                <div class="flex flex-col gap-6">
                                    <!-- Name, Username, Email -->
                                    @include('profile.partials.update-profile-information-form')

                                    <!-- Password integrated in same flow -->
                                    <div class="pt-6 border-t border-gray-50">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </div>
            </div>
        </main>
    @push('scripts')
    <script>
        function confirmUpdate(formId) {
            Swal.fire({
                title: 'are you sure?',
                text: "do you want to save these changes?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#555555',
                cancelButtonColor: '#F4F4F4',
                confirmButtonText: 'yes, save it!',
                cancelButtonText: 'cancel',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl font-bold px-6 py-3',
                    cancelButton: 'rounded-xl font-bold px-6 py-3 text-[#777777]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
