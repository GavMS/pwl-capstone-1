<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10 transition-all">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        ticket types.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        manage global ticket classifications
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.ticket-types.create') }}" class="inline-flex items-center px-6 py-3 bg-[#555555] text-white rounded-2xl font-bold text-sm lowercase hover:bg-black transition-all shadow-sm group">
                        <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        add ticket type
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12">
            @if(session('success'))
                <div class="mb-8 p-4 bg-green-100 border border-green-200 text-green-700 rounded-2xl font-bold text-sm lowercase animate-fade-in-down">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-8 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl font-bold text-sm lowercase animate-fade-in-down">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-[2.5rem] shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="px-8 py-6 text-[10px] font-bold text-[#999999] uppercase tracking-widest">Type Name</th>
                                <th class="px-8 py-6 text-[10px] font-bold text-[#999999] uppercase tracking-widest">Default Capacity</th>
                                <th class="px-8 py-6 text-[10px] font-bold text-[#999999] uppercase tracking-widest text-right">actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($ticketTypes as $type)
                                <tr class="group hover:bg-[#F9F9F8] transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-[#444444] text-lg tracking-tight">{{ $type->name }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-[#F4F4F4] text-[#777777] rounded-lg text-xs font-bold lowercase">
                                            {{ $type->capacity ?? 'Unlimited' }} attendees
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-3 transition-opacity">
                                            <a href="{{ route('admin.ticket-types.edit', $type->id_ticket_type) }}" class="w-10 h-10 bg-[#F4F4F4] text-[#555555] rounded-xl flex items-center justify-center hover:bg-[#555555] hover:text-white transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('admin.ticket-types.destroy', $type->id_ticket_type) }}" method="POST" id="delete-form-{{ $type->id_ticket_type }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('{{ $type->id_ticket_type }}')" class="w-10 h-10 bg-red-50 text-red-400 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-16 text-center text-[#999999] lowercase italic font-medium">
                                        no ticket types found. create one to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($ticketTypes->hasPages())
                    <div class="px-8 py-6 border-t border-gray-50 bg-[#F9F9F8]">
                        {{ $ticketTypes->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'are you sure?',
                text: "this will remove the ticket type mapping.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#F4F4F4',
                confirmButtonText: 'yes, delete it!',
                cancelButtonText: 'cancel',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl font-bold px-6 py-3 text-white',
                    cancelButton: 'rounded-xl font-bold px-6 py-3 text-[#777777]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
