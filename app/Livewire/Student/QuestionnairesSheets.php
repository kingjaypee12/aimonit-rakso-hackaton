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

    public function mount($game_pin, $participant_id)
    {
        if (!$game_pin) {
            $this->error_message = 'No game PIN provided.';
            return;
        }

        if (!$participant_id) {
            $this->error_message = 'No participant provided.';
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

        try {
            $decryptedParticipantId = decrypt($participant_id);
            $this->participant = GameParticipant::find($decryptedParticipantId);

            if (!$this->participant) {
                $this->error_message = 'Participant not found. Please rejoin the quiz.';
                return;
            }

            if ($this->participant->game_session_id !== $gameSession->id) {
                $this->error_message = 'Invalid participant for this quiz.';
                return;
            }

            if (!$this->participant->is_active) {
                $this->participant->update([
                    'is_active' => true,
                    'left_at' => null,
                ]);
            }

        } catch (\Exception $e) {
            $this->error_message = 'Invalid participant session. Please rejoin the quiz.';
            logger()->error('Participant decryption error: ' . $e->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.student.questionnaires-sheets');
    }
}
