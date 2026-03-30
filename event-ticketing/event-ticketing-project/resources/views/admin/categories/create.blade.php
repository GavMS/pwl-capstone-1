<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10 transition-all">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.categories.index') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#555555] hover:bg-black hover:text-white transition-all shadow-sm border border-gray-100 group">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div>
                        <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                            new category.
                        </h2>
                        <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                            create a new classification for events
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-3xl mx-auto px-6 lg:px-8 mt-12 animate-fade-in-up">
            <div class="bg-white rounded-[2.5rem] shadow-sm overflow-hidden border border-gray-100 p-10 relative overflow-hidden group">
                <div class="absolute -right-16 -top-16 w-48 h-48 bg-[#F9F9F8] rounded-full group-hover:scale-110 transition duration-1000 rotate-12"></div>
                
                <div class="relative z-10 text-center mb-12">
                    <div class="w-20 h-20 bg-[#F4F4F4] text-[#555555] rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-sm border border-gray-50 group-hover:rotate-12 transition-transform duration-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-[#444444] tracking-tight lowercase">category details</h3>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" id="create-category-form" class="relative z-10">
                    @csrf
                    
                    <div class="space-y-10">
                        <!-- Name Field -->
                        <div class="relative">
                            <label for="name" class="block text-xs font-bold text-[#777777] uppercase tracking-widest mb-4 ml-1">category name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. music festival, tech workshop..." 
                                class="w-full px-8 py-5 bg-[#F4F4F4] border-none rounded-2xl focus:ring-4 focus:ring-[#555555]/10 focus:bg-white transition-all text-[#444444] font-bold text-lg lowercase tracking-tight placeholder:text-[#999999]/50" required>
                            @error('name')
                                <p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description Field -->
                        <div class="relative">
                            <label for="description" class="block text-xs font-bold text-[#777777] uppercase tracking-widest mb-4 ml-1">description (optional)</label>
                            <textarea name="description" id="description" rows="4" placeholder="briefly describe this category..." 
                                class="w-full px-8 py-5 bg-[#F4F4F4] border-none rounded-2xl focus:ring-4 focus:ring-[#555555]/10 focus:bg-white transition-all text-[#444444] font-medium text-lg lowercase tracking-tight placeholder:text-[#999999]/50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6">
                            <button type="button" onclick="confirmUpdate('create-category-form')" class="w-full py-5 bg-[#555555] text-white rounded-[2rem] font-bold text-lg lowercase hover:bg-black transition-all shadow-lg active:scale-95 group">
                                save category.
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    @push('scripts')
    <script>
        function confirmUpdate(formId) {
            Swal.fire({
                title: 'save new category?',
                text: "do you want to apply these details?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#555555',
                cancelButtonColor: '#F4F4F4',
                confirmButtonText: 'yes, save!',
                cancelButtonText: 'cancel',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl font-bold px-6 py-3 text-white',
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
