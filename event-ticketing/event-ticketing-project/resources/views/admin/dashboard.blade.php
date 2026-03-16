<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Selamat datang Administrator, {{ $user->name }}!</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Anda login dengan role: <span class="font-semibold px-2 py-1 bg-red-100 text-red-800 rounded-md dark:bg-red-900 dark:text-red-300">{{ ucfirst($user->role) }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
