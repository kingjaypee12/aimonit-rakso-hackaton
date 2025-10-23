<div class="min-h-screen bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 p-3 sm:p-4">
    @if($error_message)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
                <div class="text-6xl mb-4">⚠️</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Oops!</h2>
                <p class="text-gray-600 mb-6">{{ $error_message }}</p>
                <button
                    onclick="window.history.back()"
                    class="bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-full px-8 py-3 font-bold hover:from-purple-700 hover:to-pink-600 transition-all"
                >
                    Go Back
                </button>
            </div>
        </div>
    @elseif(empty($questions) || empty($questions['questions']))
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
                <div class="text-6xl mb-4">📝</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">No Questions Available</h2>
                <p class="text-gray-600">This quiz doesn't have any questions yet.</p>
            </div>
        </div>
    @else
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-center mb-3">
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 text-white text-xs sm:text-sm font-semibold">
                    📌 PIN: {{ $game_pin }} | 🎯 {{ $questions['topic'] ?? 'Quiz' }}
                </div>
            </div>

            <div class="flex justify-between items-center mb-4 text-white">
            <button onclick="quitQuiz()" class="p-2 active:scale-95 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="text-base sm:text-lg font-bold">
                <span id="currentQuestion">1</span> of {{ count($questions['questions']) }}
            </div>
            <div class="w-7"></div>
        </div>

        <div class="w-full bg-white/30 rounded-full h-2 mb-4 overflow-hidden">
            <div id="progressBar" class="bg-white h-full rounded-full transition-all duration-300" style="width: 10%"></div>
        </div>

        <div class="flex justify-center mb-4">
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-lg px-6 py-3 sm:px-8 sm:py-4">
                    <div class="flex items-center gap-3">
                        <svg id="timerIcon" class="w-6 h-6 sm:w-7 sm:h-7 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-center">
                            <div id="timerDisplay" class="text-3xl sm:text-4xl font-black text-purple-600 tabular-nums">30</div>
                            <div class="text-xs text-gray-500 font-semibold">seconds</div>
                        </div>
                    </div>
                </div>
                <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none" viewBox="0 0 100 100">
                    <circle id="timerRing" cx="50" cy="50" r="48" fill="none" stroke="#9333ea" stroke-width="2"
                            stroke-dasharray="301.59" stroke-dashoffset="0" class="transition-all duration-1000 ease-linear opacity-20"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl p-5 sm:p-8 mb-5 min-h-[160px] sm:min-h-[200px] flex items-center justify-center">
            <h2 id="questionText" class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 text-center leading-relaxed px-2">
                {{ $questions['questions'][0]['question'] }}
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:gap-4 mb-6">
            @php
                $colors = [
                    'A' => ['bg' => 'bg-red-500', 'hover' => 'hover:bg-red-600', 'active' => 'active:bg-red-700', 'symbol' => '▲'],
                    'B' => ['bg' => 'bg-blue-500', 'hover' => 'hover:bg-blue-600', 'active' => 'active:bg-blue-700', 'symbol' => '◆'],
                    'C' => ['bg' => 'bg-yellow-500', 'hover' => 'hover:bg-yellow-600', 'active' => 'active:bg-yellow-700', 'symbol' => '●'],
                    'D' => ['bg' => 'bg-green-500', 'hover' => 'hover:bg-green-600', 'active' => 'active:bg-green-700', 'symbol' => '■']
                ];
            @endphp

            @foreach(['A', 'B', 'C', 'D'] as $option)
                <button
                    onclick="selectAnswer('{{ $option }}')"
                    id="option{{ $option }}"
                    class="answer-btn {{ $colors[$option]['bg'] }} {{ $colors[$option]['hover'] }} {{ $colors[$option]['active'] }} text-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 font-bold text-lg sm:text-xl transition-all duration-200 transform active:scale-95 shadow-xl relative overflow-hidden group disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none min-h-[70px] sm:min-h-[80px]"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl sm:text-3xl">{{ $colors[$option]['symbol'] }}</span>
                        </div>
                        <span id="optionText{{ $option }}" class="flex-1 text-left leading-snug">
                            {{ $questions['questions'][0]['options'][$option] }}
                        </span>
                    </div>
                    <div class="absolute inset-0 bg-white/0 active:bg-white/20 transition-all duration-100"></div>
                </button>
            @endforeach
        </div>

        <div class="flex justify-center">
            <div class="bg-white/20 backdrop-blur-sm rounded-full px-6 py-3 text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="font-bold text-base sm:text-lg">Score: <span id="score">0</span> pts</span>
                </div>
            </div>
        </div>

        <div id="explanationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm items-center justify-center p-4 z-50" style="display: none;">
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8 transform transition-all max-h-[90vh] overflow-y-auto">
                <div id="resultIcon" class="text-5xl sm:text-6xl text-center mb-4">✓</div>
                <h3 id="resultTitle" class="text-xl sm:text-2xl font-bold text-center mb-4">Correct!</h3>
                <p id="explanationText" class="text-gray-700 text-center mb-6 leading-relaxed text-base"></p>
                <button
                    onclick="nextQuestion()"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-full py-4 sm:py-5 font-bold text-lg sm:text-xl active:scale-95 transition-all shadow-lg min-h-[56px]"
                >
                    Next Question
                </button>
            </div>
        </div>

        <div id="resultsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm items-center justify-center p-4 z-50" style="display: none;">
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8 transform transition-all max-h-[90vh] overflow-y-auto">
                <div class="text-center">
                    <div class="text-5xl sm:text-6xl mb-4">🎉</div>
                    <h3 class="text-2xl sm:text-3xl font-bold mb-2">Quiz Complete!</h3>
                    <p class="text-gray-600 mb-6 text-sm sm:text-base">{{ $questions['topic'] }}</p>

                    <div class="bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl p-6 mb-6">
                        <div class="text-4xl sm:text-5xl font-bold text-purple-600 mb-2">
                            <span id="finalScore">0</span> <span id="totalQuestions">points</span>
                        </div>
                        <p class="text-gray-700 font-semibold text-sm sm:text-base">Your Total Score</p>
                        <div class="mt-4 text-sm text-gray-600">
                            Out of {{ count($questions['questions']) }} questions answered
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button
                            onclick="restartQuiz()"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-full py-4 sm:py-5 font-bold text-lg sm:text-xl active:scale-95 transition-all shadow-lg min-h-14"
                        >
                            Try Again
                        </button>
                        <button
                            onclick="quitQuiz()"
                            class="w-full bg-gray-200 text-gray-700 rounded-full py-4 sm:py-5 font-bold text-lg sm:text-xl active:scale-95 transition-all min-h-14"
                        >
                            Exit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const quizData = @json($questions['questions'] ?? []);
        const gameSessionId = {{ $game_session->id ?? 'null' }};
        const participantId = {{ $participant->id ?? 'null' }};
        const gameTopic = "{{ $questions['topic'] ?? 'Quiz' }}";
        const csrfToken = "{{ csrf_token() }}";
        let currentQuestionIndex = 0;
        let score = 0;
        let answered = false;
        let timeLeft = 30;
        let timerInterval = null;
        let questionStartTime = null;
        const TIMER_DURATION = 30;

        if (!quizData || quizData.length === 0) {
            console.error('No quiz data available');
        }

        function initQuiz() {
            displayQuestion();
            updateProgress();
            startTimer();
        }

        function startTimer() {
            clearInterval(timerInterval);
            timeLeft = TIMER_DURATION;
            questionStartTime = Date.now();
            updateTimerDisplay();

            timerInterval = setInterval(() => {
                timeLeft--;
                updateTimerDisplay();
                const timerDisplay = document.getElementById('timerDisplay');
                const timerIcon = document.getElementById('timerIcon');

                if (timeLeft <= 5) {
                    timerDisplay.className = 'text-3xl sm:text-4xl font-black text-red-600 tabular-nums animate-pulse';
                    timerIcon.className = 'w-6 h-6 sm:w-7 sm:h-7 text-red-600';
                } else if (timeLeft <= 10) {
                    timerDisplay.className = 'text-3xl sm:text-4xl font-black text-orange-600 tabular-nums';
                    timerIcon.className = 'w-6 h-6 sm:w-7 sm:h-7 text-orange-600';
                }

                if (timeLeft <= 0) {
                    stopTimer();
                    handleTimeOut();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            document.getElementById('timerDisplay').textContent = timeLeft;
        }

        function stopTimer() {
            clearInterval(timerInterval);
        }

        async function submitAnswerToAPI(questionId, answerGiven, answerTimeSeconds) {
            try {
                const response = await fetch('/api/game/answer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        game_session_id: gameSessionId,
                        participant_id: participantId,
                        question_id: questionId,
                        answer_given: answerGiven,
                        answer_time_seconds: answerTimeSeconds,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    console.error('API Error:', data);
                    return null;
                }

                console.log('Answer saved successfully:', data);
                return data;

            } catch (error) {
                console.error('Failed to submit answer:', error);
                return null;
            }
        }

        function handleTimeOut() {
            answered = true;
            const question = quizData[currentQuestionIndex];
            const answerTimeSeconds = (Date.now() - questionStartTime) / 1000;

            submitAnswerToAPI(question.id, 'TIMEOUT', answerTimeSeconds);

            ['A', 'B', 'C', 'D'].forEach(option => {
                document.getElementById('option' + option).disabled = true;
            });

            const correctBtn = document.getElementById('option' + question.correct);
            correctBtn.classList.remove('bg-red-500', 'bg-blue-500', 'bg-yellow-500', 'bg-green-500');
            correctBtn.classList.add('bg-green-600', 'ring-4', 'ring-green-300', 'ring-offset-4');

            showExplanation(false, 'Time\'s up! ' + question.explanation);
        }

        function displayQuestion() {
            if (currentQuestionIndex >= quizData.length) {
                showResults();
                return;
            }

            answered = false;
            const question = quizData[currentQuestionIndex];

            document.getElementById('questionText').textContent = question.question;
            document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;

            const timerDisplay = document.getElementById('timerDisplay');
            const timerIcon = document.getElementById('timerIcon');
            const timerRing = document.getElementById('timerRing');
            timerDisplay.className = 'text-3xl sm:text-4xl font-black text-purple-600 tabular-nums';
            timerIcon.className = 'w-6 h-6 sm:w-7 sm:h-7 text-purple-600';
            timerRing.style.strokeDashoffset = '0';

            ['A', 'B', 'C', 'D'].forEach(option => {
                const optionBtn = document.getElementById('option' + option);
                const optionText = document.getElementById('optionText' + option);
                optionText.textContent = question.options[option];

                optionBtn.disabled = false;
                optionBtn.classList.remove('ring-4', 'ring-white', 'ring-offset-4', 'opacity-50', 'ring-green-300');

                optionBtn.className = optionBtn.className.replace(/bg-\w+-\d+/g, '');
                if (option === 'A') optionBtn.classList.add('bg-red-500', 'hover:bg-red-600', 'active:bg-red-700');
                if (option === 'B') optionBtn.classList.add('bg-blue-500', 'hover:bg-blue-600', 'active:bg-blue-700');
                if (option === 'C') optionBtn.classList.add('bg-yellow-500', 'hover:bg-yellow-600', 'active:bg-yellow-700');
                if (option === 'D') optionBtn.classList.add('bg-green-500', 'hover:bg-green-600', 'active:bg-green-700');
            });

            updateProgress();
            startTimer();
        }

        async function selectAnswer(selectedOption) {
            if (answered) return;

            answered = true;
            stopTimer();

            const question = quizData[currentQuestionIndex];
            const isCorrect = selectedOption === question.correct;

            const answerTimeSeconds = (Date.now() - questionStartTime) / 1000;

            const apiResponse = await submitAnswerToAPI(question.id, selectedOption, answerTimeSeconds);

            if (apiResponse && apiResponse.success) {
                score = apiResponse.data.total_score;

                document.getElementById('score').textContent = score;

                if (apiResponse.data.has_streak_bonus) {
                    console.log('🔥 Streak Bonus! +100 points');
                }
            }

            ['A', 'B', 'C', 'D'].forEach(option => {
                document.getElementById('option' + option).disabled = true;
            });

            const selectedBtn = document.getElementById('option' + selectedOption);

            if (isCorrect) {
                selectedBtn.classList.remove('bg-red-500', 'bg-blue-500', 'bg-yellow-500', 'bg-green-500');
                selectedBtn.classList.add('bg-green-600', 'ring-4', 'ring-green-300', 'ring-offset-4');

                if (navigator.vibrate) {
                    navigator.vibrate([50, 50, 50]);
                }

                showExplanation(true, question.explanation);
            } else {
                selectedBtn.classList.remove('bg-red-500', 'bg-blue-500', 'bg-yellow-500', 'bg-green-500');
                selectedBtn.classList.add('bg-red-600', 'ring-4', 'ring-red-300', 'ring-offset-4');

                const correctBtn = document.getElementById('option' + question.correct);
                correctBtn.classList.remove('bg-red-500', 'bg-blue-500', 'bg-yellow-500', 'bg-green-500');
                correctBtn.classList.add('bg-green-600', 'ring-4', 'ring-green-300', 'ring-offset-4');

                if (navigator.vibrate) {
                    navigator.vibrate(200);
                }

                showExplanation(false, question.explanation);
            }
        }

        function showExplanation(isCorrect, explanation) {
            const modal = document.getElementById('explanationModal');
            const icon = document.getElementById('resultIcon');
            const title = document.getElementById('resultTitle');
            const text = document.getElementById('explanationText');

            if (isCorrect) {
                icon.textContent = '✓';
                icon.className = 'text-5xl sm:text-6xl text-center mb-4 text-green-500';
                title.textContent = 'Correct!';
                title.className = 'text-xl sm:text-2xl font-bold text-center mb-4 text-green-600';
            } else {
                icon.textContent = '✗';
                icon.className = 'text-5xl sm:text-6xl text-center mb-4 text-red-500';
                title.textContent = 'Incorrect';
                title.className = 'text-xl sm:text-2xl font-bold text-center mb-4 text-red-600';
            }

            text.textContent = explanation;
            modal.style.display = 'flex';

            setTimeout(() => {
                modal.querySelector('.bg-white').classList.add('scale-100');
            }, 10);
        }

        function nextQuestion() {
            document.getElementById('explanationModal').style.display = 'none';
            currentQuestionIndex++;
            displayQuestion();
        }

        function updateProgress() {
            const progress = ((currentQuestionIndex + 1) / quizData.length) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        function showResults() {
            stopTimer();
            const modal = document.getElementById('resultsModal');

            document.getElementById('finalScore').textContent = score;
            document.getElementById('totalQuestions').textContent = 'points';

            modal.style.display = 'flex';

            if (navigator.vibrate && score >= 500) {
                navigator.vibrate([100, 50, 100, 50, 100]);
            }
        }

        function restartQuiz() {
            currentQuestionIndex = 0;
            score = 0;
            answered = false;
            document.getElementById('score').textContent = '0';
            document.getElementById('resultsModal').style.display = 'none';
            displayQuestion();
        }

        function quitQuiz() {
            stopTimer();
            if (confirm('Are you sure you want to quit the quiz?')) {
                window.location.href = '/dashboard';
            } else {
                if (!answered && currentQuestionIndex < quizData.length) {
                    startTimer();
                }
            }
        }

        window.addEventListener('beforeunload', function (e) {
            if (currentQuestionIndex < quizData.length && currentQuestionIndex > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        document.addEventListener('DOMContentLoaded', initQuiz);
    </script>
        </div>
    @endif
</div>
