<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">

        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">manage users.</h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">create, edit, and manage account status</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   class="inline-block px-5 py-2.5 bg-[#555555] text-white rounded-xl font-bold lowercase hover:bg-black transition shadow-sm text-sm">
                    + add user
                </a>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 flex flex-col gap-8">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-600 px-5 py-4 rounded-2xl text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search (live) -->
            <form method="GET" action="{{ route('admin.users.index') }}" id="search-form" class="flex gap-3">
                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ $search }}"
                    placeholder="search by name, username, or email..."
                    class="flex-1 px-5 py-3 bg-white rounded-2xl border border-gray-200 text-sm text-[#555555] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 shadow-sm"
                    autocomplete="off"
                />
                @if($search)
                    <a href="{{ route('admin.users.index') }}"
                       class="px-5 py-3 bg-white text-[#555555] rounded-2xl font-bold text-sm hover:bg-[#F4F4F4] transition shadow-sm lowercase border border-gray-200">
                        clear
                    </a>
                @endif
            </form>
            <script>
                (function () {
                    const input = document.getElementById('search-input');
                    const form  = document.getElementById('search-form');
                    let timer;
                    input.addEventListener('input', function () {
                        clearTimeout(timer);
                        timer = setTimeout(function () { form.submit(); }, 300);
                    });
                })();
            </script>

            <!-- Users Table -->
            <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left px-6 py-4 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Name</th>
                            <th class="text-left px-6 py-4 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Username</th>
                            <th class="text-left px-6 py-4 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Email</th>
                            <th class="text-left px-6 py-4 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Role</th>
                            <th class="text-left px-6 py-4 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Status</th>
                            <th class="text-right px-6 py-4 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-[#FAFAFA] transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 min-w-[2.5rem] rounded-xl bg-[#E5E5E3] flex items-center justify-center font-bold text-[#555555] text-xs uppercase shrink-0">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="font-semibold text-[#444444]">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[#555555]">{{ $user->username }}</td>
                                <td class="px-6 py-4 text-[#555555]">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if($user->role === 'admin')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-gray-800 text-white">admin</span>
                                    @elseif($user->role === 'organizer')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-blue-100 text-blue-700">organizer</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-[#F4F4F4] text-[#777777]">user</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-green-100 text-green-700">active</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-red-100 text-red-600">inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end items-center gap-2">
                                        <!-- Edit -->
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="px-3 py-1.5 bg-[#F4F4F4] text-[#555555] rounded-lg text-xs font-bold hover:bg-[#E5E5E3] transition lowercase">
                                            edit
                                        </a>

                                        <!-- Toggle Active (hidden for self) -->
                                        @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold lowercase transition
                                                    {{ $user->is_active
                                                        ? 'bg-red-50 text-red-600 hover:bg-red-100'
                                                        : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                                {{ $user->is_active ? 'deactivate' : 'activate' }}
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <button type="button"
                                            onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}')"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold lowercase transition bg-red-100 text-red-700 hover:bg-red-200">
                                            delete
                                        </button>
                                        @else
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold lowercase text-[#BBBBBB] bg-[#F9F9F9] cursor-not-allowed" title="Cannot deactivate your own account">
                                            you
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-[#777777] text-sm">
                                    no users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-[2rem] shadow-2xl p-8 max-w-md w-full mx-4 animate-fade-in">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#333333]">delete account?</h3>
                    <p class="text-sm text-[#777777] mt-0.5">this action cannot be undone.</p>
                </div>
            </div>

            <p class="text-sm text-[#555555] mb-6 leading-relaxed">
                You are about to permanently delete the account of
                <strong id="modal-user-name" class="text-[#333333]"></strong>.
                This will only succeed if the account has no transactions, e-tickets, or events.
            </p>

            <form id="delete-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold text-sm lowercase hover:bg-red-700 transition">
                        yes, delete permanently
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 py-3 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold text-sm lowercase hover:bg-[#E5E5E3] transition">
                        cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(userId, userName) {
            document.getElementById('modal-user-name').textContent = userName;
            document.getElementById('delete-form').action = '/admin/users/' + userId;
            document.getElementById('delete-modal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }
        // Close on backdrop click
        document.getElementById('delete-modal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
</x-app-layout>
