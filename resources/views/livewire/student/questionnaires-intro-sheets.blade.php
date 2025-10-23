<div class="min-h-screen h-full flex justify-center items-center bg-gradient-to-br from-[#034e96] to-[#f7620c] p-0">
    <div class="bg-white w-full h-full min-h-screen flex flex-col overflow-hidden sm:rounded-3xl sm:shadow-2xl sm:max-w-2xl sm:min-h-0 sm:h-auto sm:m-4">
        <!-- Header -->
        <div id="question_card"
             class="text-center text-3xl sm:text-5xl font-bold bg-gradient-to-r from-[#034e96] to-[#0a6bc2] text-white py-10 sm:py-12 transition-all duration-300 flex-shrink-0 shadow-lg">
            <div class="flex items-center justify-center gap-3">
                <svg class="w-12 h-12 sm:w-14 sm:h-14 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                <span class="drop-shadow-lg">BrightBuds</span>
            </div>
        </div>

        <!-- No Code Found Banner -->
        @if ($showNoCodeFound)
            <div class="flex flex-col items-center justify-center py-6 px-6 bg-red-50 border-l-4 border-red-500 shadow-sm animate-shake">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-600 text-xl sm:text-2xl font-semibold">
                        Quiz code "<span class="font-mono">{{ strtoupper($code) }}</span>" not found!
                    </p>
                </div>
            </div>
        @endif

        <!-- Form Container -->
        <div id="form_container"
             class="flex flex-col flex-1 items-center justify-center gap-6 p-8 sm:p-10 bg-gradient-to-b from-gray-50 to-white transition-all duration-300 overflow-y-auto">

            <!-- Step 1: Name & Email -->
            @if(!$showStep2)
                <div id="step1" class="w-full max-w-md space-y-6">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-[#034e96] to-[#f7620c] bg-clip-text text-transparent mb-3">Welcome!</h2>
                        <p class="text-gray-600 text-lg sm:text-xl">Enter your details to get started</p>
                    </div>

                    <!-- Name Input -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-3 text-lg sm:text-xl">Your Name</label>
                        <input type="text"
                            wire:model.defer="name"
                            class="w-full px-6 sm:px-7 py-6 sm:py-7 border-2 @error('name') border-red-500 @else border-gray-300 @enderror rounded-2xl text-xl sm:text-2xl focus:ring-4 focus:ring-[#034e96]/30 focus:border-[#034e96] focus:outline-none transition-all placeholder-gray-400 shadow-sm"
                            placeholder="John Doe" />
                        @error('name')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-3 text-lg sm:text-xl">Your Email</label>
                        <input type="email"
                            wire:model.defer="email"
                            class="w-full px-6 sm:px-7 py-6 sm:py-7 border-2 @error('email') border-red-500 @else border-gray-300 @enderror rounded-2xl text-xl sm:text-2xl focus:ring-4 focus:ring-[#034e96]/30 focus:border-[#034e96] focus:outline-none transition-all placeholder-gray-400 shadow-sm"
                            placeholder="john@example.com" />
                        @error('email')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Next Button -->
                    <button wire:click="proceedToStep2"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="w-full bg-gradient-to-r from-[#034e96] to-[#0a6bc2] hover:from-[#023a70] hover:to-[#034e96] text-white font-bold text-2xl sm:text-3xl py-7 sm:py-8 rounded-2xl transition-all duration-200 transform active:scale-95 shadow-xl hover:shadow-2xl min-h-[80px] sm:min-h-[90px] flex items-center justify-center gap-3">
                        <svg wire:loading.remove class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <svg wire:loading class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Next</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            @endif

            <!-- Step 2: Quiz Code -->
            @if ($showStep2)
                <div class="w-full max-w-md space-y-6">
                    <!-- Info Banner -->
                    <div class="flex items-start gap-4 p-6 bg-gradient-to-r from-[#f7620c]/10 to-[#f7620c]/5 border-l-4 border-[#f7620c] rounded-xl shadow-md animate-fadeIn">
                        <svg class="w-7 h-7 text-[#f7620c] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-lg sm:text-xl text-gray-800 leading-relaxed font-medium">
                            The quiz will automatically start once your access code has been verified.
                        </p>
                    </div>

                    <!-- Code Input -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-4 text-xl sm:text-2xl text-center">
                            Quiz Access Code
                        </label>
                        <input type="text"
                            wire:model.defer="code"
                            class="w-full px-6 sm:px-7 py-7 sm:py-8 border-3 @error('code') border-red-500 @else border-[#034e96]/30 @enderror rounded-2xl text-2xl sm:text-3xl text-center font-mono uppercase tracking-widest focus:ring-4 focus:ring-[#f7620c]/30 focus:border-[#f7620c] focus:outline-none transition-all placeholder-gray-400 shadow-lg font-bold"
                            placeholder="ABC123"
                            maxlength="10" />
                        @error('code')
                            <p class="text-red-500 text-sm mt-2 text-center flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-gray-500 text-sm sm:text-base mt-2 text-center">
                            Enter the code provided by your teacher
                        </p>
                    </div>

                    <!-- Start Quiz Button -->
                    <button wire:click="checkCode"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="w-full bg-gradient-to-r from-[#f7620c] to-[#ff8534] hover:from-[#d65409] hover:to-[#f7620c] text-white font-bold text-2xl sm:text-3xl py-7 sm:py-8 rounded-2xl transition-all duration-200 transform active:scale-95 shadow-xl hover:shadow-2xl min-h-[80px] sm:min-h-[90px] flex items-center justify-center gap-3">
                        <svg wire:loading.remove class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg wire:loading class="animate-spin w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Start Quiz</span>
                        <span wire:loading>Joining...</span>
                    </button>

                    <!-- Back Button -->
                    <button wire:click="backToStep1"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xl sm:text-2xl py-6 sm:py-7 rounded-2xl transition-all duration-200 transform active:scale-95 min-h-[70px] sm:min-h-[80px] flex items-center justify-center gap-2 border-2 border-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        <span>Back</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center p-5 sm:p-7 bg-gradient-to-r from-[#034e96]/5 to-[#f7620c]/5 border-t-2 border-[#034e96]/20 text-gray-600 text-sm sm:text-base flex-shrink-0">
            <p class="font-semibold">Powered by <span class="text-[#034e96]">AiMonit</span> Quiz Platform</p>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        .animate-shake {
            animation: shake 0.3s ease-in-out;
        }
    </style>
</div>
