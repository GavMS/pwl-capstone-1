<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10 text-[#555555]">
            <div class="max-w-4xl mx-auto py-6 px-6 lg:px-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight lowercase">
                        create event.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        fill in details to publish a new experience.
                    </p>
                </div>
                <a href="{{ route($prefix . '.events.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#777777] hover:text-black transition transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    back to list
                </a>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-6 lg:px-8 mt-12">
            <div class="bg-white rounded-[2.5rem] shadow-sm p-10 border border-gray-100">
                <form id="event-form" action="{{ route($prefix . '.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf

                    <!-- Title & Description -->
                    <div class="space-y-6">
                        <div class="flex flex-col gap-2">
                            <label for="title" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Event Title</label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}" 
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner" placeholder="name of your event...">
                            @error('title') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="description" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Description</label>
                            <textarea name="description" id="description" rows="5" required 
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner" placeholder="tell them what to expect...">{{ old('description') }}</textarea>
                            @error('description') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Date & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-50 pt-10">
                        <div class="flex flex-col gap-2">
                            <label for="date" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Date & Time</label>
                            <input type="datetime-local" name="date" id="date" required value="{{ old('date') }}" 
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner">
                            @error('date') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="location" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Location</label>
                            <input type="text" name="location" id="location" required value="{{ old('location') }}" 
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner" placeholder="where's the fun at?">
                            @error('location') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Banner & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-50 pt-10">
                        <div class="flex flex-col gap-2">
                            <label for="banner" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Banner Image</label>
                            <div class="relative group">
                                <input type="file" name="banner" id="banner" accept="image/*" 
                                    class="w-full text-xs font-bold text-[#777777] file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:uppercase file:bg-[#555555] file:text-white hover:file:bg-black transition">
                            </div>
                            <p class="text-[9px] text-[#999999] font-bold mt-1 uppercase tracking-wider ml-1">max size: 2mb (png, jpg, webp)</p>
                            @error('banner') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="status" class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Initial Status</label>
                            <select name="status" id="status" required class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] lowercase focus:ring-2 focus:ring-[#555555] shadow-inner">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>published</option>
                            </select>
                            @error('status') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-8 border-t border-gray-50 flex justify-end gap-3">
                        <button type="button" onclick="confirmEventAction('event-form', 'create this event')" class="w-full md:w-auto px-10 py-5 bg-[#555555] text-white rounded-3xl font-extrabold lowercase hover:bg-black transition shadow-lg shadow-gray-200">
                            save event.
                        </button>
                    </div>
                </form>
            </div>
        </main>
        
        @push('scripts')
        <script>
            function confirmEventAction(formId, actionText) {
                Swal.fire({
                    title: 'are you sure?',
                    text: `do you want to ${actionText}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#555555',
                    cancelButtonColor: '#F4F4F4',
                    confirmButtonText: 'yes, do it!',
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
    </div>
</x-app-layout>
