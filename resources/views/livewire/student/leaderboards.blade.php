<div class="min-h-screen bg-gray-100 flex flex-col items-center py-10 px-4">

    <!-- 🏆 Title -->
    <h1 class="text-3xl font-bold text-gray-800 mb-8">🏆 Game Leaderboard</h1>

    @php
        $podium = collect($leaderboards)->take(3);
        $others = collect($leaderboards)->skip(3);
    @endphp

    <!-- 🥇 Podium Section -->
    <div class="flex justify-center items-end gap-6 mb-10">
        <!-- 2nd Place -->
        @if(isset($podium[1]))
        <div class="flex flex-col items-center">
            <div class="bg-gray-300 text-gray-800 font-bold text-lg w-20 h-20 flex items-center justify-center rounded-full shadow">
                {{ strtoupper(substr($podium[1]['name'], 0, 1)) }}
            </div>
            <p class="mt-2 font-semibold text-gray-700">{{ $podium[1]['name'] }}</p>
            <p class="text-sm text-gray-500">{{ $podium[1]['points'] }} pts</p>
            <div class="bg-gray-300 w-20 h-16 mt-2 rounded-t-lg"></div>
        </div>
        @endif

        <!-- 1st Place -->
        @if(isset($podium[0]))
        <div class="flex flex-col items-center">
            <div class="bg-yellow-400 text-white font-bold text-2xl w-24 h-24 flex items-center justify-center rounded-full shadow-lg border-4 border-yellow-300">
                {{ strtoupper(substr($podium[0]['name'], 0, 1)) }}
            </div>
            <p class="mt-2 font-semibold text-gray-700">{{ $podium[0]['name'] }}</p>
            <p class="text-sm text-gray-500">{{ $podium[0]['points'] }} pts</p>
            <div class="bg-yellow-400 w-24 h-24 mt-2 rounded-t-lg"></div>
        </div>
        @endif

        <!-- 3rd Place -->
        @if(isset($podium[2]))
        <div class="flex flex-col items-center">
            <div class="bg-orange-400 text-white font-bold text-lg w-20 h-20 flex items-center justify-center rounded-full shadow">
                {{ strtoupper(substr($podium[2]['name'], 0, 1)) }}
            </div>
            <p class="mt-2 font-semibold text-gray-700">{{ $podium[2]['name'] }}</p>
            <p class="text-sm text-gray-500">{{ $podium[2]['points'] }} pts</p>
            <div class="bg-orange-400 w-20 h-12 mt-2 rounded-t-lg"></div>
        </div>
        @endif
    </div>

    <!-- 🧾 Other Participants -->
    <div class="w-full max-w-md bg-white rounded-lg shadow-md divide-y divide-gray-100">
        @foreach($others as $index => $player)
            <div class="flex justify-between items-center py-3 px-4 hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-gray-500">{{ $index + 1 }}</span>
                    <span class="font-semibold text-gray-700">{{ $player['name'] }}</span>
                </div>
                <span class="text-indigo-600 font-semibold">{{ $player['points'] }} pts</span>
            </div>
        @endforeach
    </div>

</div>
