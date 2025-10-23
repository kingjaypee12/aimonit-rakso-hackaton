<div class="h-screen flex justify-center items-center bg-gradient-to-br from-purple-500 via-pink-500 to-red-500 p-0">
    <div class="bg-white w-full h-full flex flex-col overflow-hidden sm:rounded-2xl sm:shadow-lg sm:max-w-2xl sm:h-auto sm:m-4">
        <!-- Header -->
        <div id="question_card"
             class="text-center text-3xl sm:text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-8 sm:py-10 transition-all duration-300 flex-shrink-0">
            <div class="flex items-center justify-center gap-3">
                <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                <span>Join Quiz</span>
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
             class="flex flex-col flex-1 items-center justify-center gap-6 p-6 sm:p-8 bg-gradient-to-b from-gray-50 to-white transition-all duration-300 overflow-y-auto">

            <!-- Step 1: Name & Email -->
            @if(!$showStep2)
                <div id="step1" class="w-full max-w-md space-y-5">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Welcome!</h2>
                        <p class="text-gray-600 text-base sm:text-lg">Enter your details to get started</p>
                    </div>

                    <!-- Name Input -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-base sm:text-lg">Your Name</label>
                        <input type="text"
                            wire:model.defer="name"
                            class="w-full px-5 sm:px-6 py-5 sm:py-6 border-2 @error('name') border-red-500 @else border-gray-300 @enderror rounded-xl sm:rounded-2xl text-lg sm:text-xl focus:ring-4 focus:ring-indigo-300 focus:border-indigo-500 focus:outline-none transition-all placeholder-gray-400"
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
                        <label class="block text-gray-700 font-semibold mb-2 text-base sm:text-lg">Your Email</label>
                        <input type="email"
                            wire:model.defer="email"
                            class="w-full px-5 sm:px-6 py-5 sm:py-6 border-2 @error('email') border-red-500 @else border-gray-300 @enderror rounded-xl sm:rounded-2xl text-lg sm:text-xl focus:ring-4 focus:ring-indigo-300 focus:border-indigo-500 focus:outline-none transition-all placeholder-gray-400"
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
                            class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-bold text-xl sm:text-2xl py-6 sm:py-7 rounded-xl sm:rounded-2xl transition-all duration-200 transform active:scale-95 shadow-lg min-h-[70px] sm:min-h-[80px] flex items-center justify-center gap-3">
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
                <div class="w-full max-w-md space-y-5">
                    <!-- Info Banner -->
                    <div class="flex items-start gap-3 p-5 bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm animate-fadeIn">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-base sm:text-lg text-green-700 leading-relaxed">
                            The quiz will automatically start once your access code has been verified.
                        </p>
                    </div>

                    <!-- Code Input -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-base sm:text-lg text-center">
                            Quiz Access Code
                        </label>
                        <input type="text"
                            wire:model.defer="code"
                            class="w-full px-5 sm:px-6 py-5 sm:py-6 border-2 @error('code') border-red-500 @else border-gray-300 @enderror rounded-xl sm:rounded-2xl text-xl sm:text-2xl text-center font-mono uppercase tracking-wider focus:ring-4 focus:ring-green-300 focus:border-green-500 focus:outline-none transition-all placeholder-gray-400"
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
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold text-xl sm:text-2xl py-6 sm:py-7 rounded-xl sm:rounded-2xl transition-all duration-200 transform active:scale-95 shadow-lg min-h-[70px] sm:min-h-[80px] flex items-center justify-center gap-3">
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
                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-lg sm:text-xl py-5 sm:py-6 rounded-xl sm:rounded-2xl transition-all duration-200 transform active:scale-95 min-h-[60px] sm:min-h-[70px] flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        <span>Back</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center p-4 sm:p-6 bg-gray-100 border-t border-gray-200 text-gray-600 text-xs sm:text-sm flex-shrink-0">
            <p>Powered by AiMonit Quiz Platform</p>
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
