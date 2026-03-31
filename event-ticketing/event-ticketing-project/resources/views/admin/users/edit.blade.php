<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">

        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" class="text-[#777777] hover:text-black transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">edit user.</h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">update account details for {{ $user->name }}</p>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-6 lg:px-8 mt-12">
            <div class="bg-white rounded-[2rem] shadow-sm p-10">

                <!-- Role display (read-only) -->
                <div class="bg-[#F4F4F4] rounded-2xl px-5 py-4 mb-8 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-[#777777] uppercase tracking-widest">Current Role</p>
                        <p class="text-sm font-bold text-[#444444] mt-1 capitalize">{{ $user->role }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                        {{ $user->role === 'admin' ? 'bg-gray-800 text-white' : ($user->role === 'organizer' ? 'bg-blue-100 text-blue-700' : 'bg-[#E5E5E3] text-[#555555]') }}">
                        {{ $user->role }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                               placeholder="Full name">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                               placeholder="username">
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                               placeholder="email@example.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Status</label>
                        <select id="is_active" name="is_active" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white">
                            <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>🟢 Active</option>
                            <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>🔴 Inactive</option>
                        </select>
                        @error('is_active')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 py-3.5 bg-[#555555] text-white rounded-xl font-bold text-sm lowercase hover:bg-black transition shadow-sm">
                            save changes
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex-1 py-3.5 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold text-sm lowercase text-center hover:bg-[#E5E5E3] transition shadow-sm">
                            cancel
                        </a>
                    </div>
                </form>

            </div>
        </main>
    </div>
</x-app-layout>
