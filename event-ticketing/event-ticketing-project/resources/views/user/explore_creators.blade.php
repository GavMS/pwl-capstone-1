<x-app-layout>
    <div class="min-h-screen bg-[#F9F9F9] font-sans antialiased pb-24">
        <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            
            <!-- Search Bar -->
            <form action="{{ route('user.explore.creators') }}" method="GET" class="mb-8" id="search-form">
                <div class="relative border-b border-gray-300 pb-2 flex gap-4 items-center">
                    <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="search-input" type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search creators..."
                        class="w-full bg-transparent border-none focus:ring-0 text-xl md:text-2xl font-bold placeholder-gray-400 p-0 outline-none"
                        autocomplete="off">
                </div>
            </form>

            <!-- Page Tabs Switching (Event <-> Creator) -->
            <div class="flex justify-center mb-10">
                <div class="inline-flex bg-white rounded-full p-1 border border-gray-200 shadow-sm">
                    <a href="{{ route('user.explore') }}" class="px-8 py-2.5 rounded-full text-sm font-bold text-gray-500 hover:text-gray-900 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Event
                    </a>
                    <a href="{{ route('user.explore.creators') }}" class="px-8 py-2.5 rounded-full text-sm font-bold bg-[#f4fbfc] text-[#38b2ac] transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Creators
                    </a>
                </div>
            </div>

            <!-- Total Results -->
            <p class="text-gray-600 font-medium mb-6 text-center sm:text-left">{{ $organizers->total() }} {{ $organizers->total() === 1 ? 'creator' : 'creators' }} found</p>

            <!-- Creators List (2 columns like GOERS) -->
            <!-- Creators List  -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-x-12 gap-y-8">
                @forelse($organizers as $creator)
                    <div class="flex flex-col sm:flex-row items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition gap-4">
                        
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <!-- Avatar -->
                            <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 bg-gray-100 border border-gray-200">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($creator->name) }}&color=555555&background=E5E5E3" alt="{{ $creator->name }}" class="w-full h-full object-cover">
                            </div>
                            
                            <!-- Info -->
                            <div class="flex flex-col">
                                <h3 class="font-bold text-gray-900 text-base uppercase tracking-wide flex items-center gap-1.5">
                                    {{ $creator->name }}
                                </h3>
                                <p class="text-sm font-medium text-gray-500 mt-0.5">{{ $creator->events_count }} experience / event</p>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="w-full sm:w-auto mt-2 sm:mt-0 flex justify-end">
                            <a href="{{ route('user.organizer.profile', $creator->id) }}" class="px-6 py-2 bg-[#14b8a6] hover:bg-teal-600 text-white font-bold rounded-full text-sm transition shadow-sm w-full sm:w-auto text-center">
                                View Profile
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No creators found.</h3>
                        <p class="text-gray-500 font-medium">Try using different search keywords.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $organizers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

<script>
var _st;
document.getElementById('search-input').addEventListener('input', function(){
    clearTimeout(_st);
    _st = setTimeout(function(){ document.getElementById('search-form').submit(); }, 400);
});
</script>
