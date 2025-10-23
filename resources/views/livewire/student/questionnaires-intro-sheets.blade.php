<div class="h-screen flex justify-center items-center bg-gray-100 p-4">
    <div class="bg-white rounded-2xl shadow-lg w-screen overflow-hidden">
        <!-- Header -->
        <div id="question_card"
             class="text-center text-5xl font-semibold bg-indigo-500 text-white py-10 transition-all duration-300">
            👋 Hello! Enter Required Field
        </div>

        
        <!-- No Code Found Banner -->
        @if ($showNoCodeFound)
            <div class="flex flex-col items-center justify-center py-10 px-6 bg-red-100 border border-red-300 shadow-sm text-center text-4xl">
                <p class="text-red-600 mt-1">
                    No <b>{{ $code }}</b> Code Found!
                </p>
            </div>
        @endif
    

        <!-- Form Container -->
        <div id="form_container"
             class="flex flex-col items-center gap-6 p-6 bg-gray-50 transition-all duration-300">
             
            <!-- Step 1 -->
            @if(!$showStep2)
                <div id="step1" class="w-full flex flex-col gap-4 text-5xl">
                    <input type="text"
                        wire:model="name"
                        class="field w-full px-4 py-10 border border-gray-300 rounded-lg text-4xl focus:ring-2 focus:ring-indigo-400 focus:outline-none text-center"
                        placeholder="Your name here..." />
                    <input type="text"
                        id="email"
                        wire:model="email"
                        class="field w-full px-4 py-10 border border-gray-300 rounded-lg text-4xl focus:ring-2 focus:ring-indigo-400 focus:outline-none text-center"
                        placeholder="Your email here..." />
                    <button id="nextBtn"
                            wire:click="proceedToStep2"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-10 px-6 rounded-lg w-full transition transform hover:scale-105">
                        Next
                    </button>
                </div>
            @endif

            <!-- Step 2 -->
            @if ($showStep2)
                <div class="flex flex-col items-center justify-center py-10 px-6 bg-green-100 border border-green-300 rounded-md shadow-sm text-center animate-fadeIn">
                    <p class="text-4xl text-green-600">
                        Please note: The quiz will automatically start once your access code has been verified.
                    </p>
                </div>
                <div id="step2" class="w-full flex flex-col gap-4">
                    <input type="text"
                        wire:model.defer="code"
                        class="field w-full px-4 py-10 border border-gray-300 rounded-lg text-4xl focus:ring-2 focus:ring-indigo-400 focus:outline-none text-center"
                        placeholder="Enter your quiz code..." />
                    <button wire:click="checkCode"
                            class="bg-green-500 hover:bg-green-600 text-white font-semibold py-10 px-6 rounded-lg w-full transition transform hover:scale-105 text-4xl">
                        Enter Code
                    </button>
                    <button 
                        wire:click="backToStep1"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-10 px-6 rounded-lg w-full transition transform hover:scale-105 text-4xl">
                        Back
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
