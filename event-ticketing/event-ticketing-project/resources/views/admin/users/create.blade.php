<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">

        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" class="text-[#777777] hover:text-black transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">add user.</h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">create a new account manually</p>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-6 lg:px-8 mt-12">
            <div class="bg-white rounded-[2rem] shadow-sm p-10">

                <!-- Info Note -->
                <div class="bg-[#F4F4F4] rounded-2xl px-5 py-4 mb-8 flex gap-3 items-start">
                    <svg class="w-5 h-5 text-[#555555] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-[#555555]">A <strong>temporary password</strong> will be auto-generated and emailed to the user. They can reset it via Forgot Password.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                               placeholder="Full name">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                               placeholder="username">
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                               placeholder="email@example.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-xs font-bold text-[#555555] uppercase tracking-widest mb-2">Role</label>
                        <select id="role" name="role" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-[#444444] focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>select a role</option>
                            <option value="admin"     {{ old('role') === 'admin'     ? 'selected' : '' }}>Admin</option>
                            <option value="organizer" {{ old('role') === 'organizer' ? 'selected' : '' }}>Organizer</option>
                            <option value="user"      {{ old('role') === 'user'      ? 'selected' : '' }}>User</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 py-3.5 bg-[#555555] text-white rounded-xl font-bold text-sm lowercase hover:bg-black transition shadow-sm">
                            create user & send email
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
