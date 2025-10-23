<?php

namespace App\Livewire\Student;

use App\Models\GameAnswer;
use Livewire\Component;

class Leaderboards extends Component
{
    public $game_pin, $participant_id;
    
    public function mount($game_pin, $participant_id)
    {
        $this->game_pin = $game_pin;
        $this->participant_id = $participant_id;
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

                return [
                    'points' => $answers->sum('points_earned'),
                    'time'   => $answers->sum('answer_time_seconds'),
                    'participant_id' => $answers->first()->participant_id ?? '0',
                    'name' => $answers->first()->participant->student->name ?? '0',
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
