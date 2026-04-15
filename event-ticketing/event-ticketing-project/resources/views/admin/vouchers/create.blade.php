<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans pb-24">
        
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="px-10 py-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-4xl font-extrabold text-[#555555] tracking-tight lowercase">
                        create voucher.
                    </h2>
                    <p class="text-[#777777] font-medium text-base lowercase mt-1">
                        issue a new discount code
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.vouchers.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#F4F4F4] text-[#555555] rounded-2xl font-bold lowercase hover:bg-[#EBEBEB] transition shadow-sm">
                        cancel
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-10 mt-12">
            @if($errors->any())
                <div class="mb-8 p-6 bg-red-50 text-red-800 border border-red-200 rounded-[2rem] font-medium">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.vouchers.store') }}" method="POST" class="bg-white rounded-[3rem] shadow-sm p-10 border border-gray-50 flex flex-col gap-8">
                @csrf

                <!-- Basic Info -->
                <div>
                    <h3 class="text-xl font-bold text-[#444444] lowercase mb-6">basic details.</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Voucher Code *</label>
                            <input type="text" name="code" value="{{ old('code') }}" required maxlength="50" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition uppercase" placeholder="e.g. SUMMER24">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Discount Percent *</label>
                            <div class="relative">
                                <input type="number" name="discount_percent" value="{{ old('discount_percent') }}" required min="1" max="100" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl pl-5 pr-12 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition" placeholder="10">
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 font-bold text-[#999999]">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 mt-6">
                        <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Description</label>
                        <textarea name="description" rows="2" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-medium focus:border-[#555555] focus:ring-0 transition resize-none" placeholder="Brief description of the voucher promotion">{{ old('description') }}</textarea>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Scope -->
                <div x-data="{ scope: '{{ old('scope', 'global') }}' }">
                    <h3 class="text-xl font-bold text-[#444444] lowercase mb-6">applicability.</h3>
                    
                    <div class="flex flex-col gap-4">
                        <label class="flex items-center gap-3 p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition" :class="{'bg-[#F4F4F4] border-[#d4d4d4]': scope === 'global'}">
                            <input type="radio" name="scope" value="global" x-model="scope" class="w-5 h-5 text-[#555555] focus:ring-[#555555]">
                            <div>
                                <div class="font-bold text-[#444444]">Global Voucher</div>
                                <div class="text-xs text-[#777777]">Can be applied to any event in the platform</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition" :class="{'bg-[#F4F4F4] border-[#d4d4d4]': scope === 'event'}">
                            <input type="radio" name="scope" value="event" x-model="scope" class="w-5 h-5 text-[#555555] focus:ring-[#555555]">
                            <div>
                                <div class="font-bold text-[#444444]">Event Specific</div>
                                <div class="text-xs text-[#777777]">Only works for a selected event</div>
                            </div>
                        </label>
                    </div>

                    <div x-show="scope === 'event'" class="mt-4 flex flex-col gap-2" x-transition>
                        <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Select Event *</label>
                        <select name="event_id" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition" :required="scope === 'event'">
                            <option value="">-- Choose Event --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id_event }}" {{ old('event_id') == $event->id_event ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Limits & Validity -->
                <div>
                    <h3 class="text-xl font-bold text-[#444444] lowercase mb-6">limits & timeframe.</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Max Uses</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition" placeholder="Leave blank for infinite">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Valid From</label>
                            <input type="datetime-local" name="valid_from" value="{{ old('valid_from') }}" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Valid Until</label>
                            <input type="datetime-local" name="valid_until" value="{{ old('valid_until') }}" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="flex flex-col gap-2">
                            <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Minimum Purchase (Rp)</label>
                            <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" min="0" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition" placeholder="0">
                            <div class="text-[10px] font-bold text-[#999999] mt-1">Minimum cart total required (0 refers to no minimum).</div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-bold text-[#777777] uppercase tracking-widest text-[10px]">Max Discount Cap (Rp)</label>
                            <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0" class="w-full bg-[#F4F4F4] border-transparent rounded-2xl px-5 py-4 text-[#555555] font-bold focus:border-[#555555] focus:ring-0 transition" placeholder="Blank for no cap">
                            <div class="text-[10px] font-bold text-[#999999] mt-1">Maximum nominal amount the discount can slash.</div>
                        </div>
                    </div>
                </div>
                
                <hr class="border-gray-100">

                <!-- Status -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-6 h-6 text-[#555555] bg-[#F4F4F4] border-transparent rounded-lg focus:ring-[#555555]">
                    <label for="is_active" class="font-bold text-[#444444] cursor-pointer">Activate immediately upon creation</label>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <button type="submit" class="w-full py-5 bg-[#555555] text-white rounded-2xl font-bold lowercase text-xl hover:bg-black transition shadow-sm">
                        save voucher code
                    </button>
                </div>

            </form>
        </main>
    </div>
</x-app-layout>
