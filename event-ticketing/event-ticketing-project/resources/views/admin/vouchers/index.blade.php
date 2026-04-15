<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans pb-24">
        
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="px-10 py-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-4xl font-extrabold text-[#555555] tracking-tight lowercase">
                        vouchers.
                    </h2>
                    <p class="text-[#777777] font-medium text-base lowercase mt-1">
                        manage discount codes for users
                    </p>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('admin.vouchers.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        new voucher
                    </a>
                </div>
            </div>
        </header>

        <main class="w-full px-10 mt-12">
            @if(session('success'))
                <div class="mb-8 p-4 bg-green-50 text-green-800 border border-green-200 rounded-2xl font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2.5rem] shadow-sm overflow-hidden border border-gray-50">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-[#555555]">
                        <thead class="bg-[#F4F4F4]/50 text-[#999999] uppercase text-[10px] font-extrabold tracking-widest">
                            <tr>
                                <th class="px-8 py-5">Code</th>
                                <th class="px-8 py-5">Discount</th>
                                <th class="px-8 py-5">Scope</th>
                                <th class="px-8 py-5">Uses</th>
                                <th class="px-8 py-5">Valid Until</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr class="border-b border-gray-50 hover:bg-[#F4F4F4]/30 transition group">
                                    <td class="px-8 py-6">
                                        <div class="font-extrabold text-[#444444] text-base">{{ $voucher->code }}</div>
                                        @if($voucher->description)
                                            <div class="text-xs text-[#999999] mt-1">{{ Str::limit($voucher->description, 30) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-1.5 items-start">
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-blue-50 text-blue-700 font-bold text-xs">
                                                {{ $voucher->discount_percent }}% OFF
                                            </span>
                                            @if($voucher->max_discount)
                                                <span class="text-[10px] font-extrabold text-[#777777] uppercase tracking-widest">up to Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</span>
                                            @endif
                                            @if($voucher->min_purchase > 0)
                                                <span class="text-[10px] font-medium text-[#999999]">Min. Spend Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @if($voucher->event_id)
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-extrabold uppercase tracking-widest rounded-md">Event</span>
                                                <span class="font-medium text-[#777777] text-xs truncate max-w-[150px]">{{ $voucher->event->title ?? 'Unknown Event' }}</span>
                                            </div>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold uppercase tracking-widest rounded-md">Global</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-[#444444]">{{ $voucher->used_count }}</span>
                                            <span class="text-[#999999] text-xs">/ {{ $voucher->max_uses ?? '∞' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-xs font-medium text-[#777777]">
                                        @if($voucher->valid_until)
                                            {{ $voucher->valid_until->format('d M Y, H:i') }}
                                        @else
                                            <span class="italic text-[#999999]">No End Date</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <form action="{{ route('admin.vouchers.toggle', $voucher->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 rounded-xl font-bold text-xs transition {{ $voucher->is_active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                                                {{ $voucher->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="w-8 h-8 rounded-lg bg-[#F4F4F4] text-[#777777] hover:bg-[#555555] hover:text-white flex items-center justify-center transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this voucher?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-16 text-center">
                                        <div class="w-16 h-16 bg-[#F4F4F4] rounded-2xl mx-auto flex items-center justify-center text-[#999999] mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                        </div>
                                        <h4 class="text-xl font-bold text-[#555555] lowercase tracking-tight">no vouchers found</h4>
                                        <p class="text-[#999999] text-sm mt-1">create your first discount code to boost sales.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($vouchers->hasPages())
                    <div class="px-8 py-5 border-t border-gray-50 bg-[#F4F4F4]/20">
                        {{ $vouchers->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</x-app-layout>
