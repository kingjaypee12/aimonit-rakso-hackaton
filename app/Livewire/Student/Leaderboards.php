<?php

namespace App\Livewire\Student;

use App\Models\GameAnswer;
use App\Models\GameParticipant;
use Livewire\Component;

class Leaderboards extends Component
{
    public $game_pin;
    public $participant_id = null;
    public $current_participant = null;

    public function mount($game_pin, $participant_id = null)
    {
        $this->game_pin = $game_pin;
        $this->participant_id = $participant_id;

        // If participant_id is provided and encrypted, decrypt it
        if ($participant_id) {
            try {
                $decryptedId = decrypt($participant_id);
                $this->current_participant = GameParticipant::find($decryptedId);
            } catch (\Exception $e) {
                // If decryption fails, participant_id might already be numeric
                $this->current_participant = GameParticipant::find($participant_id);
            }
        }
    }

    public function render()
    {
        $leaderboards = $this->getLeaderBoards();

        return view('livewire..student.leaderboards', compact('leaderboards'));
    }

    private function getLeaderBoards()
    {
        $participant_scores = GameAnswer::with('participant')
            ->whereHas('gameSession', function($q) {
                $q->where('game_pin', $this->game_pin);
            })
            ->get()
            ->groupBy('participant_id')
            ->map(function($answers) {
                $answers = $answers->sortByDesc('id')->unique('question_id');

                $participantId = $answers->first()->participant_id ?? '0';
                $isCurrentUser = $this->current_participant && $this->current_participant->id == $participantId;

                return [
                    'points' => $answers->sum('points_earned'),
                    'time'   => $answers->sum('answer_time_seconds'),
                    'participant_id' => $participantId,
                    'name' => $answers->first()->participant->student->name ?? '0',
                    'is_current_user' => $isCurrentUser,
                ];
            })
            ->sort(function ($a, $b) {
                if ($a['points'] === $b['points']) {
                    return $a['time'] <=> $b['time'];
                }
                return $b['points'] <=> $a['points'];
            })
            ->values()
            ->toArray();

        return $participant_scores;
    }
}
