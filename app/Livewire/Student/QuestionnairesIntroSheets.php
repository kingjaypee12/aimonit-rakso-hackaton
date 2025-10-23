<?php

namespace App\Livewire\Student;

use App\Models\GameSession;
use App\Models\GameParticipant;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QuestionnairesIntroSheets extends Component
{
    public $code = null;
    public $showStep2 = false;
    public $showNoCodeFound = false;

    public $email = '';
    public $name = '';

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:255',
        'code' => 'required|min:3|max:10',
    ];

    protected $messages = [
        'name.required' => 'Please enter your name',
        'name.min' => 'Name must be at least 2 characters',
        'email.required' => 'Please enter your email',
        'email.email' => 'Please enter a valid email address',
        'code.required' => 'Please enter the quiz code',
        'code.min' => 'Code must be at least 3 characters',
    ];

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire..student.questionnaires-intro-sheets');
    }

    public function proceedToStep2()
    {
        $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255',
        ]);

        $this->showStep2 = true;
    }

    public function checkCode()
    {
        $this->showNoCodeFound = false;

        $this->validate([
            'code' => 'required|min:3|max:10',
        ]);

        try {
            DB::beginTransaction();
            $gameSession = GameSession::where('game_pin', strtoupper($this->code))
                ->whereIn('status', ['waiting', 'in_progress'])
                ->first();

            if (!$gameSession) {
                $this->showNoCodeFound = true;
                $this->addError('code', 'Invalid quiz code or quiz is not available.');
                DB::rollBack();
                return;
            }

            if ($gameSession->status === 'in_progress' && !$gameSession->allow_late_join) {
                $this->addError('code', 'This quiz has already started and does not allow late joins.');
                DB::rollBack();
                return;
            }

            $user = User::firstOrCreate(
                ['email' => $this->email],
                [
                    'name' => $this->name,
                    'password' => Hash::make(uniqid()),
                ]
            );

            if ($user->name !== $this->name) {
                $user->update(['name' => $this->name]);
            }

            $existingParticipant = GameParticipant::where('game_session_id', $gameSession->id)
                ->where('student_id', $user->id)
                ->first();

            if ($existingParticipant) {
                if (!$existingParticipant->is_active) {
                    $existingParticipant->update([
                        'is_active' => true,
                        'left_at' => null,
                    ]);
                }
                $participant = $existingParticipant;
            } else {

                $participant = GameParticipant::create([
                    'game_session_id' => $gameSession->id,
                    'student_id' => $user->id,
                    'nickname' => $this->name,
                    'joined_at' => now(),
                    'is_active' => true,
                ]);
            }

            DB::commit();

            session([
                'participant_id' => $participant->id,
                'participant_name' => $this->name,
                'participant_email' => $this->email,
            ]);

            return redirect()->route('student.quiz_sheet', [
                'game_pin' => $gameSession->game_pin,
                'participant_id' => encrypt($participant->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('code', 'An error occurred while joining the quiz. Please try again.');
            logger()->error('Quiz join error: ' . $e->getMessage(), [
                'email' => $this->email,
                'name' => $this->name,
                'code' => $this->code,
            ]);
        }
    }

    public function backToStep1()
    {
        $this->showStep2 = false;
        $this->reset('code');
        $this->resetErrorBag('code');
    }
}
