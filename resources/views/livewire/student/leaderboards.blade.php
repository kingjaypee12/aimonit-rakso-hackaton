<div class="min-h-screen bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 flex flex-col items-center py-6 sm:py-10 px-4">

    <!-- 🏆 Title -->
    <div class="text-center mb-6 sm:mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2 flex items-center justify-center gap-3">
            <span class="text-4xl sm:text-5xl">🏆</span>
            <span>Leaderboard</span>
        </h1>
        <p class="text-white/80 text-sm sm:text-base">See how you rank against others!</p>
    </div>

    @php
        $podium = collect($leaderboards)->take(3);
        $others = collect($leaderboards)->skip(3);
    @endphp

    <!-- 🥇 Podium Section -->
    <div class="flex justify-center items-end gap-4 sm:gap-6 mb-8 sm:mb-10">
        <!-- 2nd Place -->
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

        <!-- 1st Place -->
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

        <!-- 3rd Place -->
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

    <!-- 🧾 Other Participants -->
    @if($others->isNotEmpty())
        <div class="w-full max-w-md">
            <h3 class="text-white font-bold text-lg sm:text-xl mb-3 text-center">Other Participants</h3>
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl divide-y divide-gray-100 overflow-hidden">
                @foreach($others as $index => $player)
                    <div class="flex justify-between items-center py-3 sm:py-4 px-4 sm:px-5 hover:bg-gray-50 transition {{ $player['is_current_user'] ? 'bg-purple-50 border-l-4 border-purple-600' : '' }}">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <span class="font-bold text-gray-500 text-base sm:text-lg w-6 sm:w-8">{{ $index + 4 }}</span>
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

    <!-- Back Button -->
    <div class="mt-8 sm:mt-10">
        <a href="/" class="bg-white text-purple-600 px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold text-base sm:text-lg hover:bg-gray-100 transition-all shadow-lg active:scale-95 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Join Another Quiz</span>
        </a>
    </div>

</div>
