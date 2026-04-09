<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Options') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-8 text-gray-900 flex flex-col items-center">
                    
                    <!-- Icon -->
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>

                    <h3 class="text-3xl font-extrabold text-gray-800 mb-2">Complete Your Payment</h3>
                    <p class="text-gray-500 mb-8 text-center max-w-md">Please complete your transaction to secure your tickets. Do not refresh this page while processing.</p>
                    
                    <!-- Transaction Details Card -->
                    <div class="bg-gray-50 border border-gray-200 p-6 rounded-xl mb-8 w-full shadow-inner">
                        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                            <p class="text-sm font-medium text-gray-500">Transaction ID</p>
                            <p class="font-bold text-gray-800 bg-gray-200 px-3 py-1 rounded-md text-sm">{{ $transaction->order_id }}</p>
                        </div>
                        
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="font-bold uppercase tracking-wider text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full">{{ $transaction->status }}</p>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <p class="text-sm font-medium text-gray-500">Total Amount</p>
                            <p class="font-extrabold text-3xl text-[#EE3D2F]">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div id="button-container-new" class="flex flex-col space-y-4 w-full sm:w-2/3">
                        <button id="pay-button" class="w-full bg-[#EE3D2F] text-white px-8 py-4 rounded-xl font-bold hover:bg-red-700 hover:shadow-lg transition-all duration-200 text-lg flex justify-center items-center gap-2">
                            <span>Proceed to Payment</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                        
                        <a href="{{ route('user.dashboard') }}" class="w-full text-center px-8 py-3 rounded-xl font-semibold text-gray-600 bg-white border-2 border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200">
                            Return to Dashboard (Pay Later)
                        </a>
                    </div>

                    <!-- Alternate Action Buttons (Shown after popup is closed) -->
                    <div id="button-container-pending" class="hidden flex-col space-y-4 w-full sm:w-2/3">
                        <button id="resume-button" class="w-full bg-green-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-700 hover:shadow-lg transition-all duration-200 text-lg flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Resume Payment</span>
                        </button>

                        <button id="change-button" class="w-full bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-800 hover:shadow-lg transition-all duration-200 text-lg flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Change Payment Method</span>
                        </button>
                        
                        <a href="{{ route('user.dashboard') }}" class="w-full text-center px-8 py-3 rounded-xl font-semibold text-gray-600 bg-white border-2 border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200">
                            Return to Dashboard (Pay Later)
                        </a>
                    </div>
                    
                    @if(app()->environment('local'))
                    <!-- DEV ONLY: Cheat Mock Button -->
                    <div class="mt-8 pt-6 border-t border-gray-200 w-full sm:w-2/3 flex flex-col items-center">
                        <p class="text-xs text-gray-400 mb-2 uppercase tracking-wide font-bold">Developer Tools</p>
                        <button onclick="document.getElementById('mock-form').submit()" class="w-full bg-orange-100 text-orange-700 border border-orange-300 px-4 py-2 rounded-lg font-bold hover:bg-orange-200 transition-all text-sm flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>Mock Payment Success (Cheat)</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="recreate-form" action="{{ route('checkout.recreate', $transaction->order_id) }}" method="POST" class="hidden">
        @csrf
    </form>
    
    <form id="mock-form" action="{{ route('checkout.mock', $transaction->order_id) }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Custom Toast Notification -->
    <div id="custom-toast" class="fixed bottom-5 right-5 transform translate-y-full opacity-0 transition-all duration-500 ease-in-out z-50 flex items-center bg-gray-900 text-white px-6 py-4 rounded-xl shadow-2xl">
        <svg class="w-6 h-6 text-blue-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span id="toast-message" class="font-medium text-sm"></span>
        <button onclick="hideToast()" class="ml-4 text-gray-400 hover:text-white focus:outline-none shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Script Midtrans Snap -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script type="text/javascript">
        const toast = document.getElementById('custom-toast');
        const toastMessage = document.getElementById('toast-message');
        
        const containerNew = document.getElementById('button-container-new');
        const containerPending = document.getElementById('button-container-pending');

        const payBtn = document.getElementById('pay-button');
        const resumeBtn = document.getElementById('resume-button');
        const changeBtn = document.getElementById('change-button');
        const recreateForm = document.getElementById('recreate-form');

        function showToast(message) {
            toastMessage.textContent = message;
            toast.classList.remove('translate-y-full', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            // Auto hide after 5 seconds
            setTimeout(hideToast, 5000);
        }

        function hideToast() {
            toast.classList.add('translate-y-full', 'opacity-0');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }

        function handlePaymentClosure(message) {
            showToast(message);
            // Hide the initial pay button and show the two split buttons
            containerNew.classList.add('hidden');
            containerNew.classList.remove('flex');
            containerPending.classList.remove('hidden');
            containerPending.classList.add('flex');
        }

        function openSnapPopup() {
            snap.pay('{{ $transaction->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('user.my-tickets') }}?success=true";
                },
                onPending: function(result){
                    handlePaymentClosure("Payment pending. Please select an option below.");
                },
                onError: function(result){
                    handlePaymentClosure("Payment error/closed. Please select an option below.");
                },
                onClose: function(){
                    handlePaymentClosure("Payment window closed. Please select an option below.");
                }
            });
        }

        // Event Listeners
        payBtn.onclick = function(e) {
            e.preventDefault();
            openSnapPopup();
        };

        resumeBtn.onclick = function(e) {
            e.preventDefault();
            openSnapPopup();
        };

        changeBtn.onclick = function(e) {
            e.preventDefault();
            changeBtn.innerHTML = '<span class="animate-pulse">Loading...</span>';
            recreateForm.submit();
        };
    </script>
</x-app-layout>
