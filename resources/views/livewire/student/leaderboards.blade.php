<div class="min-h-screen h-full bg-gradient-to-br from-[#034e96] via-[#0560a8] to-[#f7620c] flex flex-col items-center justify-center py-8 sm:py-12 px-4" wire:poll.3s>


    <div class="text-center mb-8 sm:mb-10">
        <div class="flex items-center justify-center gap-4 mb-5">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-white flex items-center gap-4 drop-shadow-2xl">
                <span class="text-5xl sm:text-6xl md:text-7xl">🏆</span>
                <span>Leaderboard</span>
                <span class="relative flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#f7620c] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-[#f7620c]"></span>
                </span>
            </h1>
        </div>
        <p class="text-white text-base sm:text-lg mb-4 font-semibold drop-shadow-lg">
            See how you rank against others!
            <span class="inline-flex items-center gap-2 text-sm ml-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-bold">Live</span>
            </span>
        </p>
    </div>

    @php
        $podium = collect($leaderboards)->take(3);
        $others = collect($leaderboards)->skip(3);
    @endphp


    <div class="flex justify-center items-end gap-5 sm:gap-8 mb-10 sm:mb-12" wire:loading.class="opacity-75" wire:target="$refresh">

        @if(isset($podium[1]))
        <div class="flex flex-col items-center transform hover:scale-110 transition-all duration-300">
            <div class="text-3xl sm:text-4xl mb-3">🥈</div>
            <div class="bg-gray-300 text-gray-800 font-black text-xl sm:text-2xl w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center rounded-full shadow-2xl border-4 border-white {{ $podium[1]['is_current_user'] ? 'ring-6 ring-[#f7620c] ring-offset-4 ring-offset-[#034e96]' : '' }}">
                {{ strtoupper(substr($podium[1]['name'], 0, 1)) }}
            </div>
            <p class="mt-3 font-bold text-white text-base sm:text-lg drop-shadow-lg {{ $podium[1]['is_current_user'] ? 'text-[#f7620c]' : '' }}">
                {{ $podium[1]['name'] }}
                @if($podium[1]['is_current_user'])
                    <span class="text-sm">(You)</span>
                @endif
            </p>
            <p class="text-sm sm:text-base text-white font-bold drop-shadow-md">{{ $podium[1]['points'] }} pts</p>
            <div class="bg-gray-300 w-20 sm:w-24 h-16 sm:h-20 mt-3 rounded-t-2xl shadow-2xl border-t-4 border-white"></div>
        </div>
        @endif


        @if(isset($podium[0]))
        <div class="flex flex-col items-center transform hover:scale-110 transition-all duration-300">
            <div class="text-4xl sm:text-5xl mb-3 animate-bounce">🥇</div>
            <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 text-white font-black text-2xl sm:text-3xl w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center rounded-full shadow-2xl border-4 border-yellow-200 {{ $podium[0]['is_current_user'] ? 'ring-6 ring-[#f7620c] ring-offset-4 ring-offset-[#034e96]' : '' }}">
                {{ strtoupper(substr($podium[0]['name'], 0, 1)) }}
            </div>
            <p class="mt-3 font-black text-white text-lg sm:text-xl drop-shadow-lg {{ $podium[0]['is_current_user'] ? 'text-[#f7620c]' : '' }}">
                {{ $podium[0]['name'] }}
                @if($podium[0]['is_current_user'])
                    <span class="text-base">(You)</span>
                @endif
            </p>
            <p class="text-base sm:text-lg text-white font-black drop-shadow-md">{{ $podium[0]['points'] }} pts</p>
            <div class="bg-gradient-to-t from-yellow-400 to-yellow-500 w-24 sm:w-28 h-24 sm:h-28 mt-3 rounded-t-2xl shadow-2xl border-t-4 border-yellow-200"></div>
        </div>
        @endif


        @if(isset($podium[2]))
        <div class="flex flex-col items-center transform hover:scale-110 transition-all duration-300">
            <div class="text-3xl sm:text-4xl mb-3">🥉</div>
            <div class="bg-gradient-to-br from-[#f7620c] to-[#d65409] text-white font-black text-xl sm:text-2xl w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center rounded-full shadow-2xl border-4 border-white {{ $podium[2]['is_current_user'] ? 'ring-6 ring-[#f7620c] ring-offset-4 ring-offset-[#034e96]' : '' }}">
                {{ strtoupper(substr($podium[2]['name'], 0, 1)) }}
            </div>
            <p class="mt-3 font-bold text-white text-base sm:text-lg drop-shadow-lg {{ $podium[2]['is_current_user'] ? 'text-[#f7620c]' : '' }}">
                {{ $podium[2]['name'] }}
                @if($podium[2]['is_current_user'])
                    <span class="text-sm">(You)</span>
                @endif
            </p>
            <p class="text-sm sm:text-base text-white font-bold drop-shadow-md">{{ $podium[2]['points'] }} pts</p>
            <div class="bg-gradient-to-t from-[#f7620c] to-[#ff8534] w-20 sm:w-24 h-12 sm:h-16 mt-3 rounded-t-2xl shadow-2xl border-t-4 border-white"></div>
        </div>
        @endif
    </div>


    @if($others->isNotEmpty())
        <div class="w-full max-w-xl">
            <h3 class="text-white font-black text-xl sm:text-2xl mb-4 text-center drop-shadow-lg">Other Participants</h3>
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl divide-y-2 divide-gray-100 overflow-hidden border-4 border-white/30" wire:loading.class="opacity-50 scale-95" wire:target="$refresh">
                @foreach($others as $index => $player)
                    <div class="flex justify-between items-center py-4 sm:py-5 px-5 sm:px-6 hover:bg-gray-50 transition-all duration-200 {{ $player['is_current_user'] ? 'bg-[#f7620c]/10 border-l-4 border-[#f7620c]' : '' }}">
                        <div class="flex items-center gap-4 sm:gap-5">
                            <span class="font-black text-gray-600 text-lg sm:text-xl w-8 sm:w-10 {{ $player['is_current_user'] ? 'text-[#034e96]' : '' }}">{{ $index + 1 }}</span>
                            <div>
                                <span class="font-bold text-gray-900 text-base sm:text-lg {{ $player['is_current_user'] ? 'text-[#034e96]' : '' }}">
                                    {{ $player['name'] }}
                                    @if($player['is_current_user'])
                                        <span class="text-sm text-[#f7620c]">(You)</span>
                                    @endif
                                </span>
                                <p class="text-xs sm:text-sm text-gray-500 font-medium">{{ number_format($player['time'], 2) }}s avg time</p>
                            </div>
                        </div>
                        <span class="text-[#034e96] font-black text-lg sm:text-xl {{ $player['is_current_user'] ? 'text-[#f7620c]' : '' }}">{{ $player['points'] }} pts</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    <div class="mt-8 sm:mt-10 bg-white/20 backdrop-blur-md rounded-2xl px-8 py-4 text-white text-center shadow-xl border-2 border-white/30">
        <div class="flex items-center justify-center gap-5 text-sm sm:text-base">
            <span class="flex items-center gap-2 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <strong class="text-lg">{{ count($leaderboards) }}</strong> participant{{ count($leaderboards) !== 1 ? 's' : '' }}
            </span>
            {{-- <span class="text-white/60">•</span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Updated at <strong>{{ $lastUpdated }}</strong>
            </span> --}}
        </div>
    </div>


    <div class="mt-8 sm:mt-10">
        <a href="/" class="bg-white text-[#034e96] px-8 sm:px-10 py-4 sm:py-5 rounded-full font-black text-lg sm:text-xl hover:bg-gray-100 transition-all shadow-2xl active:scale-95 inline-flex items-center gap-3 border-4 border-white/50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Join Another Quiz</span>
        </a>
        <a href="{{ route('result', ['game_pin' => $game_pin, 'participant_id' => encrypt($participant_id)]) }}" wire:click="generateResult" class="bg-green-400 hover:cursor px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold text-base sm:text-lg hover:bg-gray-100 transition-all shadow-lg active:scale-95 inline-flex items-center gap-2">
            <span>View Result</span>
        </a>
    </div>
    <style>
        [wire\:loading\.class] {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                opacity: 1;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }
    </style>
</div>
