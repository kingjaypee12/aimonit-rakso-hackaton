<?php

namespace App\Livewire\Student;

use App\Models\GameSession;
use App\Models\Participant;
use Livewire\Component;

class QuestionnairesIntroSheets extends Component
{
    public $code = null;
    public $showStep2 = false;
    public $showNoCodeFound = false;

    public $email = '';
    public $name = '';



    public function mount()
    {

    }

    public function render()
    {
        return view('livewire..student.questionnaires-intro-sheets');
    }

    public function proceedToStep2()
    {
        $this->showStep2 = true;
    }

    public function checkCode()
    {
        $this->showNoCodeFound = false;

        $game_pin = GameSession::select('game_pin')->where('game_pin', trim($this->code))->first()->game_pin ?? null;
        
        if(empty($game_pin)) {
            $this->showNoCodeFound = true;

            return;
        }

        $create = Participant::create([
            'name' => $this->name,
            'email' => $this->email,
            'game_pin' => $game_pin,
        ]);

        if($create) {
            return redirect()->route('student.quiz_sheet', ['game_pin' => $game_pin, 'participant_id' => encrypt($game_pin)]);
        }
    }

    public function backToStep1()
    {
        $this->showStep2 = false;
    }
}
