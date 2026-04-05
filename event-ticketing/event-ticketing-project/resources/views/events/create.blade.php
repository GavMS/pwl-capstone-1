<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        <!-- Header -->
        <header
            class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10 text-[#555555]">
            <div class="max-w-4xl mx-auto py-6 px-6 lg:px-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight lowercase">
                        create event.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        fill in details to publish a new experience.
                    </p>
                </div>
                <a href="{{ route($prefix . '.events.index') }}" onclick="return handleCancelNav(event, this)"
                    class="inline-flex items-center gap-2 text-sm font-bold text-[#777777] hover:text-black transition transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    back to list
                </a>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-6 lg:px-8 mt-12">
            <div class="bg-white rounded-[2.5rem] shadow-sm p-10 border border-gray-100">
                <form id="event-form" action="{{ route($prefix . '.events.store') }}" method="POST"
                    enctype="multipart/form-data" class="space-y-10">
                    @csrf

                    <!-- General Error Alert -->
                    @if($errors->has('error'))
                        <p class="text-[10px] text-red-500 font-bold mb-6 ml-1 lowercase">⚠️ {{ $errors->first('error') }}
                        </p>
                    @endif

                    <!-- Title & Description -->
                    <div class="space-y-6">
                        <div class="flex flex-col gap-2">
                            <label for="title"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Event
                                Title</label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner"
                                placeholder="name of your event...">
                            @error('title') <p class="text-[10px] text-red-500 font-bold ml-1 lowercase">⚠️
                            {{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2" x-data="{ desc: '{{ old('description') }}', max: 5000 }">
                            <label for="description"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Description</label>
                            <textarea name="description" id="description" rows="5" required x-model="desc"
                                x-on:input="desc = $event.target.value"
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner"
                                placeholder="tell them what to expect..."></textarea>
                            <div class="flex justify-between items-center ml-1">
                                <div>
                                    @error('description') <p class="text-[10px] text-red-500 font-bold lowercase">⚠️
                                    {{ $message }}</p> @enderror
                                </div>
                                <span class="text-[9px] font-bold text-red-500 lowercase opacity-70"
                                    x-text="desc.length + ' / ' + max + ' characters'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-50 pt-10">
                        <div class="flex flex-col gap-2">
                            <label for="date"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Date &
                                Time</label>
                            <input type="datetime-local" name="date" id="date" required value="{{ old('date') }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner">
                            @error('date') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="location"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Venue /
                                Location</label>
                            <input type="text" name="location" id="location" required value="{{ old('location') }}"
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner"
                                placeholder="venue name or address...">
                            @error('location') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="city"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">City</label>
                            <select name="city" id="city" required
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner">
                                <option value="">select city...</option>
                                @foreach(['Jakarta', 'Bandung', 'Bali', 'Surabaya', 'Yogyakarta', 'Tangerang', 'Bekasi', 'Semarang', 'Medan', 'Solo'] as $c)
                                    <option value="{{ $c }}" {{ old('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('city') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="format"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Format</label>
                            <select name="format" id="format" required
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-inner">
                                <option value="onsite" {{ old('format', 'onsite') == 'onsite' ? 'selected' : '' }}>Event
                                    Onsite</option>
                                <option value="online" {{ old('format') == 'online' ? 'selected' : '' }}>Event Online
                                </option>
                            </select>
                            @error('format') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category & Organizer -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-50 pt-10">
                        <div class="flex flex-col gap-2">
                            <label for="category_id"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Event
                                Category</label>
                            <select name="category_id" id="category_id"
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] lowercase focus:ring-2 focus:ring-[#555555] shadow-inner">
                                <option value="">no category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id_category }}" {{ old('category_id') == $category->id_category ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(auth()->user()->role === 'admin')
                            <div class="flex flex-col gap-2">
                                <label for="organizer_id"
                                    class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Assign
                                    Organizer</label>
                                <select name="organizer_id" id="organizer_id"
                                    class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] lowercase focus:ring-2 focus:ring-[#555555] shadow-inner">
                                    <option value="">platform / no organizer</option>
                                    @foreach($organizers as $organizer)
                                        <option value="{{ $organizer->id }}" {{ old('organizer_id') == $organizer->id ? 'selected' : '' }}>
                                            {{ $organizer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organizer_id') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="flex flex-col gap-2 opacity-50">
                                <label
                                    class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Organizer</label>
                                <div class="w-full px-6 py-4 bg-gray-100 rounded-2xl font-bold text-[#777777] lowercase">
                                    {{ auth()->user()->name }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Banner & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-50 pt-10">
                        <div class="flex flex-col gap-2">
                            <label for="banner"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Banner
                                Image</label>

                            <!-- Live preview (hidden until a file is picked) -->
                            <div id="banner-preview-wrap"
                                class="hidden w-full h-32 rounded-2xl overflow-hidden mb-3 border border-gray-200 relative">
                                <img id="banner-preview" src="" class="w-full h-full object-cover">
                                <button type="button" onclick="clearBannerPreview()" title="remove selected image"
                                    class="absolute top-2 right-2 w-7 h-7 flex items-center justify-center bg-black/60 text-white rounded-full hover:bg-black transition text-xs font-bold">
                                    ✕
                                </button>
                            </div>

                            <div class="relative group">
                                <input type="file" name="banner" id="banner" accept="image/*"
                                    class="w-full text-xs font-bold text-[#777777] file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:uppercase file:bg-[#555555] file:text-white hover:file:bg-black transition">
                            </div>
                            <p class="text-[9px] text-[#999999] font-bold mt-1 uppercase tracking-wider ml-1">max size:
                                2mb (png, jpg, webp)</p>
                            @error('banner') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="status"
                                class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest ml-1">Initial
                                Status</label>
                            <select name="status" id="status" required
                                class="w-full px-6 py-4 bg-[#F4F4F4] border-none rounded-2xl font-bold text-[#444444] lowercase focus:ring-2 focus:ring-[#555555] shadow-inner">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>published
                                </option>
                            </select>
                            @error('status') <p class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Ticket Configuration -->
                    <div class="border-t border-gray-50 pt-10" x-data="{ 
                        tickets: @js(old('tickets', [['ticket_type_id' => '', 'price' => '', 'stock' => '']])),
                        ticketTypes: @js($ticketTypes->map(fn($t) => ['id_ticket_type' => $t->id_ticket_type, 'name' => $t->name])->values()),
                        addTicket() {
                            this.tickets.push({ ticket_type_id: '', price: '', stock: '' });
                        },
                        removeTicket(index) {
                            if (this.tickets.length > 1) this.tickets.splice(index, 1);
                        },
                        availableFor(index) {
                            const usedIds = this.tickets
                                .filter((_, i) => i !== index)
                                .map(t => String(t.ticket_type_id))
                                .filter(id => id !== '');
                            return this.ticketTypes.filter(t => !usedIds.includes(String(t.id_ticket_type)));
                        }
                    }">
                        <div class="flex items-center justify-between mb-6 ml-1">
                            <div>
                                <h4 class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest">ticket
                                    configurations</h4>
                                <p class="text-[10px] text-[#999999] lowercase mt-1 font-medium">define what kinds of
                                    tickets you are selling</p>
                            </div>
                            <button type="button" @click="addTicket()"
                                x-bind:disabled="tickets.length >= ticketTypes.length"
                                x-bind:class="tickets.length >= ticketTypes.length ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#555555] hover:text-white'"
                                class="px-4 py-2 bg-[#F4F4F4] text-[#555555] rounded-xl text-[10px] font-extrabold uppercase tracking-widest transition shadow-sm">
                                + add ticket type
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(ticket, index) in tickets" :key="index">
                                <div
                                    class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-[#F9F9F8] p-6 rounded-[2rem] border border-gray-100 relative group/ticket">
                                    <div class="md:col-span-5 flex flex-col gap-2">
                                        <label
                                            class="text-[9px] font-bold text-[#999999] uppercase tracking-widest ml-1">ticket
                                            type</label>
                                        <select :name="'tickets['+index+'][ticket_type_id]'"
                                            x-model="ticket.ticket_type_id"
                                            class="ticket-type-select w-full px-5 py-3.5 bg-white border-none rounded-xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-sm text-sm">
                                            <option value="">select type...</option>
                                            <template x-for="type in availableFor(index)" :key="type.id_ticket_type">
                                                <option :value="type.id_ticket_type"
                                                    :selected="String(ticket.ticket_type_id) === String(type.id_ticket_type)"
                                                    x-text="type.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="md:col-span-3 flex flex-col gap-2">
                                        <label
                                            class="text-[9px] font-bold text-[#999999] uppercase tracking-widest ml-1">price
                                            (IDR)</label>
                                        <input type="number" :name="'tickets['+index+'][price]'" x-model="ticket.price"
                                            min="0" placeholder="e.g. 150000"
                                            class="w-full px-5 py-3.5 bg-white border-none rounded-xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-sm text-sm">
                                    </div>
                                    <div class="md:col-span-3 flex flex-col gap-2">
                                        <label
                                            class="text-[9px] font-bold text-[#999999] uppercase tracking-widest ml-1">stock
                                            (slots)</label>
                                        <input type="number" :name="'tickets['+index+'][stock]'" x-model="ticket.stock"
                                            min="1" placeholder="e.g. 100"
                                            class="w-full px-5 py-3.5 bg-white border-none rounded-xl font-bold text-[#444444] focus:ring-2 focus:ring-[#555555] shadow-sm text-sm">
                                    </div>
                                    <div class="md:col-span-1 flex items-end justify-center pb-1">
                                        <button type="button" @click="removeTicket(index)" x-show="tickets.length > 1"
                                            class="w-10 h-10 flex items-center justify-center bg-white text-red-300 rounded-xl hover:bg-red-50 hover:text-red-500 transition shadow-sm border border-gray-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Ticket type warning -->
                        <p id="ticket-type-warning"
                            class="hidden text-[10px] text-red-500 font-bold mt-4 ml-1 lowercase">⚠️ please select a
                            ticket type for every row.</p>
                        @error('tickets') <p class="text-[10px] text-red-500 font-bold mt-4 ml-1 lowercase">⚠️
                        {{ $message }}</p> @enderror
                    </div>

                    <!-- Submit -->
                    <div class="pt-8 border-t border-gray-50 flex justify-end gap-3">
                        <a href="{{ route($prefix . '.events.index') }}"
                            class="w-full md:w-auto px-10 py-5 bg-[#F4F4F4] text-[#777777] rounded-3xl font-extrabold lowercase hover:bg-gray-200 transition shadow-sm text-center">
                            cancel.
                        </a>
                        <button type="button" onclick="confirmEventAction('event-form', 'create this event')"
                            class="w-full md:w-auto px-10 py-5 bg-[#555555] text-white rounded-3xl font-extrabold lowercase hover:bg-black transition shadow-lg shadow-gray-200">
                            save event.
                        </button>
                    </div>
                </form>
            </div>
        </main>

        @push('scripts')
            <script>
                // Banner live preview
                document.getElementById('banner').addEventListener('change', function () {
                    const file = this.files[0];
                    if (!file) {
                        document.getElementById('banner-preview-wrap').classList.add('hidden');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('banner-preview').src = e.target.result;
                        document.getElementById('banner-preview-wrap').classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                });

                function clearBannerPreview() {
                    const input = document.getElementById('banner');
                    input.value = '';
                    document.getElementById('banner-preview').src = '';
                    document.getElementById('banner-preview-wrap').classList.add('hidden');
                }

                function handleCancelNav(e, el) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'leave without saving?',
                        text: 'your changes will not be saved.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#555555',
                        cancelButtonColor: '#F4F4F4',
                        confirmButtonText: 'yes, leave',
                        cancelButtonText: 'stay',
                        customClass: {
                            popup: 'rounded-[2rem]',
                            confirmButton: 'rounded-xl font-bold px-6 py-3',
                            cancelButton: 'rounded-xl font-bold px-6 py-3 text-[#777777]'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = el.href;
                        }
                    });
                }

                function confirmEventAction(formId, actionText) {
                    // Validate: every ticket row must have a type selected
                    const selects = document.querySelectorAll('.ticket-type-select');
                    const warning = document.getElementById('ticket-type-warning');
                    const allFilled = Array.from(selects).every(s => s.value !== '');

                    if (!allFilled) {
                        warning.classList.remove('hidden');
                        warning.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                    warning.classList.add('hidden');

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