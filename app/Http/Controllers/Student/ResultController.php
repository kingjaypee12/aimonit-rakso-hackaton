<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GameAnswer;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResultController extends Controller
{
public function index($gamePin, $participant_id)
    {
        $answersJson = $this->getGameAnswers($gamePin)->where('participant_id', decrypt($participant_id))->sortByDesc('id')->unique('question_id')->toArray();
        $master_question = GameSession::where('game_pin', $gamePin)->first();

        $questionsJson = $master_question->quiz_data;
        $additionalContext = "The title of the quiz: {{ $master_question->title }}, the topic:{{ $master_question->topic }}, the description: {{ $master_question->description }}. This flashcards will be use as reviewers of the students";

        $systemPrompt = $this->buildSystemPromptStudent($questionsJson, $answersJson);
        $userPrompt = $this->buildUserPrompt($additionalContext);
        $response = null;

        for($i = 0; $i < 5; $i++) {
            try {
                $response = Http::timeout(60) // Add timeout for LLM calls
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'api-key' => config('services.ai.key'), // Use config instead of hardcoded
                    ])
                    ->post(config('services.ai.url'), [
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => 0.7, // Add temperature for consistency
                        'max_tokens' => 500, // Limit response length
                    ]);
    
                if ($response->failed()) {
                    Log::error('LLM API call failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    throw new \Exception('Failed to get LLM response');
                }
    
                $response = $response->json();
    
            } catch (\Exception $e) {
                Log::error('LLM API error', ['error' => $e->getMessage()]);
                throw $e;
            }
    
            $decoded = json_decode($response['choices'][0]['message']['content']);

            if($decoded) {
                $response = $decoded;
                break;
            }
        }

        return view('student.result-record', ['flashcards' => $response->flashcards]);
    }

    public function getGameAnswers($gamePin)
    {
        return GameAnswer::with('participant')
            ->whereHas('gameSession', function($q) use($gamePin) {
                $q->where('game_pin', $gamePin);
            })
            ->get();
    }

    private function buildSystemPromptTeacher($questions, array $answers): string
    {
        $questionsJson = $questions;
        $answersJson = json_encode($answers, JSON_PRETTY_PRINT);
        
        return "PROMPT
        You are an educational assessment analyst. Analyze the student's performance based on their answers.

        Questions:
        {$questionsJson}

        Student's Answers:
        {$answersJson}

        Provide a comprehensive analysis including:
        1. Overall performance assessment
        2. Key strengths demonstrated
        3. Areas needing improvement
        4. Specific mistakes and their corrections
        5. A concise 5-sentence summary of the learner's quality

        Format your response as JSON with the following structure:
        {
            'overall_score': 'percentage or grade',
            'strengths': ['strength1', 'strength2'],
            'weaknesses': ['weakness1', 'weakness2'],
            'corrections': [{'question': '...', 'mistake': '...', 'correction': '...'}],
            'summary': '5-sentence summary here'
        }
        PROMPT";
    }

    private function buildSystemPromptStudent($questions, array $answers): string
    {
        $questionsJson = $questions;
        $answersJson = json_encode($answers, JSON_PRETTY_PRINT);
        
        return "PROMPT
            You are a fun and friendly learning coach who creates review flashcards for children.  
            Use the student's answers to create easy, visual, and encouraging flashcards that help them review what they missed.  
            Use emojis to make learning exciting and easy to remember!

            Student Answer:
            {$answersJson}

            Quizlet Question:
            {$questions}

            Instructions:
            1. Look at which questions the student got **wrong**.
            2. For each wrong answer, create a **child-friendly flashcard** that teaches the correct idea in a simple way.
            3. Use **emojis** that match the topic (for example: 🌱 for plants, ☀️ for the sun, 🐶 for animals, 📏 for tools, 💧 for water, 🍎 for food, etc.).
            4. Keep explanations short (1–2 sentences) — like a teacher showing a fun card to a child.
            5. Make sure every flashcard feels positive and encouraging (“Let’s learn this together!” instead of “You got this wrong.”).
            6. The student is a child — use simple words and friendly tone.
            7. Do **not** include negative or test-like language.
            8. Minimum of 5 flash cards.

            Output the result as a **JSON** object with this exact structure:
            {
                'flashcards': [
                    {
                        'emoji': '🌱',
                        'title': 'Living and Non-Living Things',
                        'tip': 'Living things like trees grow, eat, and need sunlight to live.'
                    },
                    {
                        'emoji': '🌡️',
                        'title': 'Measuring Temperature',
                        'tip': 'We use a thermometer to check how hot or cold something is!'
                    }
                ]
            }

            If there are no wrong answers, return a single flashcard saying:
            {
                'flashcards': [
                    {
                        'emoji': '🎉',
                        'title': 'Awesome Work!',
                        'tip': 'You got everything right! Keep shining bright!'
                    }
                ]
            }
            PROMPT";
    }

    private function buildUserPrompt(?string $context = null): string
    {
        return $context ?? 'Please analyze the student performance based on the provided data.';
    }

}
