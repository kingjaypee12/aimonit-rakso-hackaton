<div class="min-h-screen bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 flex flex-col items-center py-6 sm:py-10 px-4" wire:poll.3s>


    <div class="text-center mb-6 sm:mb-8">
        <div class="flex items-center justify-center gap-3 mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-white flex items-center gap-3">
                <span class="text-4xl sm:text-5xl">🏆</span>
                <span>Leaderboard</span>
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
            </h1>
        </div>
        <p class="text-white/80 text-sm sm:text-base mb-3">
            See how you rank against others!
            <span class="inline-flex items-center gap-1 text-xs">
                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </p>
        <button
            wire:click="$refresh"
            class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition-all active:scale-95 inline-flex items-center gap-2"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
        >
            <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove>Refresh Now</span>
            <span wire:loading>Refreshing...</span>
        </button>
    </div>

    @php
        $podium = collect($leaderboards)->take(3);
        $others = collect($leaderboards)->skip(3);
    @endphp


    <div class="flex justify-center items-end gap-4 sm:gap-6 mb-8 sm:mb-10" wire:loading.class="opacity-75" wire:target="$refresh">

        @if(isset($podium[1]))
        <div class="flex flex-col items-center transform hover:scale-105 transition-all">
            <div class="text-2xl sm:text-3xl mb-2">🥈</div>
            <div class="bg-gray-300 text-gray-800 font-bold text-lg sm:text-xl w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center rounded-full shadow-lg {{ $podium[1]['is_current_user'] ? 'ring-4 ring-white ring-offset-2 ring-offset-purple-600' : '' }}">
                {{ strtoupper(substr($podium[1]['name'], 0, 1)) }}
            </div>
            <p class="mt-2 font-semibold text-white text-sm sm:text-base {{ $podium[1]['is_current_user'] ? 'text-yellow-300' : '' }}">
                {{ $podium[1]['name'] }}
                @if($podium[1]['is_current_user'])
                    <span class="text-xs">(You)</span>
                @endif
            </p>
            <p class="text-xs sm:text-sm text-white/80 font-bold">{{ $podium[1]['points'] }} pts</p>
            <div class="bg-gray-300 w-16 sm:w-20 h-12 sm:h-16 mt-2 rounded-t-lg shadow-lg"></div>
        </div>
        @endif


        @if(isset($podium[0]))
        <div class="flex flex-col items-center transform hover:scale-105 transition-all">
            <div class="text-3xl sm:text-4xl mb-2 animate-bounce">🥇</div>
            <div class="bg-yellow-400 text-white font-bold text-xl sm:text-2xl w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center rounded-full shadow-xl border-4 border-yellow-300 {{ $podium[0]['is_current_user'] ? 'ring-4 ring-white ring-offset-2 ring-offset-purple-600' : '' }}">
                {{ strtoupper(substr($podium[0]['name'], 0, 1)) }}
            </div>
            <p class="mt-2 font-bold text-white text-base sm:text-lg {{ $podium[0]['is_current_user'] ? 'text-yellow-300' : '' }}">
                {{ $podium[0]['name'] }}
                @if($podium[0]['is_current_user'])
                    <span class="text-xs">(You)</span>
                @endif
            </p>
            <p class="text-sm sm:text-base text-white/90 font-bold">{{ $podium[0]['points'] }} pts</p>
            <div class="bg-yellow-400 w-20 sm:w-24 h-20 sm:h-24 mt-2 rounded-t-lg shadow-xl"></div>
        </div>
        @endif


        @if(isset($podium[2]))
        <div class="flex flex-col items-center transform hover:scale-105 transition-all">
            <div class="text-2xl sm:text-3xl mb-2">🥉</div>
            <div class="bg-orange-400 text-white font-bold text-lg sm:text-xl w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center rounded-full shadow-lg {{ $podium[2]['is_current_user'] ? 'ring-4 ring-white ring-offset-2 ring-offset-purple-600' : '' }}">
                {{ strtoupper(substr($podium[2]['name'], 0, 1)) }}
            </div>
            <p class="mt-2 font-semibold text-white text-sm sm:text-base {{ $podium[2]['is_current_user'] ? 'text-yellow-300' : '' }}">
                {{ $podium[2]['name'] }}
                @if($podium[2]['is_current_user'])
                    <span class="text-xs">(You)</span>
                @endif
            </p>
            <p class="text-xs sm:text-sm text-white/80 font-bold">{{ $podium[2]['points'] }} pts</p>
            <div class="bg-orange-400 w-16 sm:w-20 h-8 sm:h-12 mt-2 rounded-t-lg shadow-lg"></div>
        </div>
        @endif
    </div>


    @if($others->isNotEmpty())
        <div class="w-full max-w-md">
            <h3 class="text-white font-bold text-lg sm:text-xl mb-3 text-center">Other Participants</h3>
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl divide-y divide-gray-100 overflow-hidden" wire:loading.class="opacity-50 scale-95" wire:target="$refresh">
                @foreach($others as $index => $player)
                    <div class="flex justify-between items-center py-3 sm:py-4 px-4 sm:px-5 hover:bg-gray-50 transition-all {{ $player['is_current_user'] ? 'bg-purple-50 border-l-4 border-purple-600' : '' }}">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <span class="font-bold text-gray-500 text-base sm:text-lg w-6 sm:w-8">{{ $index + 1 }}</span>
                            <div>
                                <span class="font-semibold text-gray-800 text-sm sm:text-base {{ $player['is_current_user'] ? 'text-purple-700' : '' }}">
                                    {{ $player['name'] }}
                                    @if($player['is_current_user'])
                                        <span class="text-xs text-purple-600">(You)</span>
                                    @endif
                                </span>
                                <p class="text-xs text-gray-500">{{ number_format($player['time'], 2) }}s avg time</p>
                            </div>
                        </div>
                        <span class="text-purple-600 font-bold text-base sm:text-lg">{{ $player['points'] }} pts</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    <div class="mt-6 sm:mt-8 bg-white/10 backdrop-blur-sm rounded-xl px-6 py-3 text-white text-center">
        <div class="flex items-center justify-center gap-4 text-xs sm:text-sm">
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <strong>{{ count($leaderboards) }}</strong> participant{{ count($leaderboards) !== 1 ? 's' : '' }}
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


    <div class="mt-6 sm:mt-8">
        <a href="/" class="bg-white text-purple-600 px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold text-base sm:text-lg hover:bg-gray-100 transition-all shadow-lg active:scale-95 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Join Another Quiz</span>
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
