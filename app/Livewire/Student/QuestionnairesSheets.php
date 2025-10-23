<?php

namespace App\Livewire\Student;

use App\Models\GameSession;
use App\Models\GameParticipant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QuestionnairesSheets extends Component
{
    public $game_pin;
    public $game_session;
    public $participant;
    public $questions = [];
    public $error_message = '';

    public function mount($game_pin = null)
    {
        if (!$game_pin) {
            $this->error_message = 'No game PIN provided.';
            return;
        }

        $this->game_pin = $game_pin;
        $gameSession = GameSession::where('game_pin', $game_pin)->first();

        if (!$gameSession) {
            $this->error_message = 'Invalid game PIN. Please check and try again.';
            return;
        }

        if ($gameSession->status === 'cancelled') {
            $this->error_message = 'This game has been cancelled.';
            return;
        }

        if ($gameSession->status === 'completed') {
            $this->error_message = 'This game has already ended.';
            return;
        }

        $this->game_session = $gameSession;

        $quizData = json_decode($gameSession->quiz_data, true);

        if (!$quizData || !isset($quizData['questions'])) {
            $this->error_message = 'Quiz data is not available.';
            return;
        }

        $this->questions = $quizData;
        $studentId = Auth::id() ?? 1;

        $this->participant = GameParticipant::firstOrCreate(
            [
                'game_session_id' => $gameSession->id,
                'student_id' => $studentId,
            ],
            [
                'nickname' => Auth::user()->name ?? 'Guest Student',
                'total_score' => 0,
                'correct_answers' => 0,
                'incorrect_answers' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
                'rank' => null,
                'average_answer_time' => 0,
                'is_active' => true,
                'joined_at' => now(),
            ]
        );
    }

    public function render()
    {
        return view('livewire.student.questionnaires-sheets');
    }
}
