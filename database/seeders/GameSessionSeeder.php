<?php

namespace Database\Seeders;

use App\Models\GameSession;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@demo.com')->first();

        if (!$teacher) {
            $teacher = User::create([
                'name' => 'Teacher Demo',
                'email' => 'teacher@demo.com',
                'password' => bcrypt('password'),
            ]);
        }

        $lesson1 = Lesson::firstOrCreate(
            ['title' => 'Living and Non-Living Things'],
            [
                'teacher_id' => $teacher->id,
                'description' => 'Learn about the characteristics of living and non-living things',
                'subject' => 'Science',
                'grade_level' => '1',
                'status' => 'completed',
            ]
        );

        $lesson2 = Lesson::firstOrCreate(
            ['title' => 'Basic Mathematics'],
            [
                'teacher_id' => $teacher->id,
                'description' => 'Introduction to numbers and basic operations',
                'subject' => 'Mathematics',
                'grade_level' => '1',
                'status' => 'completed',
            ]
        );

        $lesson3 = Lesson::firstOrCreate(
            ['title' => 'Solar System'],
            [
                'teacher_id' => $teacher->id,
                'description' => 'Exploring planets and space',
                'subject' => 'Science',
                'grade_level' => '2',
                'status' => 'completed',
            ]
        );

        $gameSession1 = GameSession::create([
            'lesson_id' => $lesson1->id,
            'teacher_id' => $teacher->id,
            'title' => 'Living Things Quiz',
            'description' => 'Test your knowledge about living and non-living things',
            'topic' => 'Living and Non-Living Things',
            'game_pin' => 'ABC123',
            'quiz_data' => json_encode(['topic' => 'Living and Non-Living Things', 'questions' => []]),
            'game_settings' => json_encode([
                'time_per_question' => 30,
                'points_per_question' => 100,
                'show_answers' => true,
                'randomize_questions' => false,
                'randomize_options' => false,
            ]),
            'status' => 'waiting',
            'total_questions' => 10,
            'allow_late_join' => false,
        ]);

        $questionsData1 = [
            [
                'difficulty' => 'easy',
                'concept' => 'Identifying living things',
                'question' => 'Which of the following is a living thing?',
                'options' => ['A' => 'Rock', 'B' => 'Tree', 'C' => 'Car', 'D' => 'Book'],
                'correct' => 'B',
                'explanation' => 'A tree is a living thing because it grows, needs water and sunlight, and can reproduce. Rocks, cars, and books are non-living things.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 1,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Characteristics of living things',
                'question' => 'What do all living things need to survive?',
                'options' => ['A' => 'Toys', 'B' => 'Food and water', 'C' => 'Television', 'D' => 'Money'],
                'correct' => 'B',
                'explanation' => 'All living things need food and water to survive. These provide energy and nutrients necessary for life.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 2,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Animal classification',
                'question' => 'Which animal is a mammal?',
                'options' => ['A' => 'Snake', 'B' => 'Fish', 'C' => 'Dog', 'D' => 'Bird'],
                'correct' => 'C',
                'explanation' => 'A dog is a mammal. Mammals have fur or hair, give birth to live babies, and feed their young with milk.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 3,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Plant parts',
                'question' => 'Which part of the plant takes in water from the soil?',
                'options' => ['A' => 'Leaves', 'B' => 'Flowers', 'C' => 'Roots', 'D' => 'Stem'],
                'correct' => 'C',
                'explanation' => 'Roots grow underground and take in water and nutrients from the soil to help the plant grow.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 4,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'States of matter',
                'question' => 'What state of matter is water in when it is ice?',
                'options' => ['A' => 'Liquid', 'B' => 'Gas', 'C' => 'Solid', 'D' => 'Plasma'],
                'correct' => 'C',
                'explanation' => 'Ice is water in its solid state. When water freezes, it becomes solid ice.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 5,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Weather',
                'question' => 'What do we use to measure temperature?',
                'options' => ['A' => 'Ruler', 'B' => 'Thermometer', 'C' => 'Scale', 'D' => 'Clock'],
                'correct' => 'B',
                'explanation' => 'A thermometer is used to measure how hot or cold something is (temperature).',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 6,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Life cycles',
                'question' => 'What does a caterpillar turn into?',
                'options' => ['A' => 'Frog', 'B' => 'Butterfly', 'C' => 'Bird', 'D' => 'Bee'],
                'correct' => 'B',
                'explanation' => 'A caterpillar goes through metamorphosis and turns into a butterfly. It first becomes a chrysalis before emerging as a butterfly.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 7,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Habitats',
                'question' => 'Where do fish live?',
                'options' => ['A' => 'In trees', 'B' => 'In water', 'C' => 'Underground', 'D' => 'In the sky'],
                'correct' => 'B',
                'explanation' => 'Fish live in water habitats such as oceans, rivers, lakes, and ponds. They have gills to breathe underwater.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 8,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'The Sun',
                'question' => 'What does the Sun give us?',
                'options' => ['A' => 'Light and heat', 'B' => 'Rain', 'C' => 'Snow', 'D' => 'Wind'],
                'correct' => 'A',
                'explanation' => 'The Sun provides light and heat to Earth. It is our main source of energy and helps plants grow.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 9,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Senses',
                'question' => 'Which body part do we use to smell?',
                'options' => ['A' => 'Eyes', 'B' => 'Ears', 'C' => 'Nose', 'D' => 'Tongue'],
                'correct' => 'C',
                'explanation' => 'We use our nose to smell. The nose detects different scents and odors in the air.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 10,
            ],
        ];

        $quizDataQuestions1 = [];
        foreach ($questionsData1 as $questionData) {
            $question = \App\Models\Question::create([
                'game_session_id' => $gameSession1->id,
                'difficulty' => $questionData['difficulty'],
                'concept' => $questionData['concept'],
                'question' => $questionData['question'],
                'options' => $questionData['options'],
                'correct' => $questionData['correct'],
                'explanation' => $questionData['explanation'],
                'points' => $questionData['points'],
                'time_limit_seconds' => $questionData['time_limit_seconds'],
                'order' => $questionData['order'],
            ]);

            $quizDataQuestions1[] = [
                'id' => $question->id,
                'difficulty' => $question->difficulty,
                'concept' => $question->concept,
                'question' => $question->question,
                'options' => $question->options,
                'correct' => $question->correct,
                'explanation' => $question->explanation,
            ];
        }

        $gameSession1->update([
            'quiz_data' => json_encode([
                'topic' => 'Living and Non-Living Things',
                'questions' => $quizDataQuestions1
            ])
        ]);

        $gameSession2 = GameSession::create([
            'lesson_id' => $lesson2->id,
            'teacher_id' => $teacher->id,
            'title' => 'Math Basics Quiz',
            'description' => 'Test your basic math skills',
            'topic' => 'Basic Mathematics',
            'game_pin' => 'MATH01',
            'quiz_data' => json_encode(['topic' => 'Basic Mathematics', 'questions' => []]),
            'game_settings' => json_encode([
                'time_per_question' => 30,
                'points_per_question' => 100,
                'show_answers' => true,
                'randomize_questions' => false,
                'randomize_options' => false,
            ]),
            'status' => 'waiting',
            'total_questions' => 5,
            'allow_late_join' => false,
        ]);

        $questionsData2 = [
            [
                'difficulty' => 'easy',
                'concept' => 'Addition',
                'question' => 'What is 2 + 3?',
                'options' => ['A' => '4', 'B' => '5', 'C' => '6', 'D' => '7'],
                'correct' => 'B',
                'explanation' => '2 + 3 equals 5. When you add 2 and 3 together, you get 5.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 1,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Subtraction',
                'question' => 'What is 10 - 4?',
                'options' => ['A' => '5', 'B' => '6', 'C' => '7', 'D' => '8'],
                'correct' => 'B',
                'explanation' => '10 - 4 equals 6. When you take away 4 from 10, you have 6 left.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 2,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Counting',
                'question' => 'How many fingers do you have on both hands?',
                'options' => ['A' => '8', 'B' => '10', 'C' => '12', 'D' => '5'],
                'correct' => 'B',
                'explanation' => 'You have 10 fingers in total - 5 on each hand.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 3,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Shapes',
                'question' => 'How many sides does a triangle have?',
                'options' => ['A' => '2', 'B' => '3', 'C' => '4', 'D' => '5'],
                'correct' => 'B',
                'explanation' => 'A triangle has 3 sides. That\'s why it\'s called a tri-angle!',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 4,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Greater than',
                'question' => 'Which number is bigger: 7 or 3?',
                'options' => ['A' => '3', 'B' => '7', 'C' => 'They are equal', 'D' => 'Neither'],
                'correct' => 'B',
                'explanation' => '7 is bigger than 3. It comes after 3 when we count.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 5,
            ],
        ];

        $quizDataQuestions2 = [];
        foreach ($questionsData2 as $questionData) {
            $question = \App\Models\Question::create([
                'game_session_id' => $gameSession2->id,
                'difficulty' => $questionData['difficulty'],
                'concept' => $questionData['concept'],
                'question' => $questionData['question'],
                'options' => $questionData['options'],
                'correct' => $questionData['correct'],
                'explanation' => $questionData['explanation'],
                'points' => $questionData['points'],
                'time_limit_seconds' => $questionData['time_limit_seconds'],
                'order' => $questionData['order'],
            ]);

            $quizDataQuestions2[] = [
                'id' => $question->id,
                'difficulty' => $question->difficulty,
                'concept' => $question->concept,
                'question' => $question->question,
                'options' => $question->options,
                'correct' => $question->correct,
                'explanation' => $question->explanation,
            ];
        }

        $gameSession2->update([
            'quiz_data' => json_encode([
                'topic' => 'Basic Mathematics',
                'questions' => $quizDataQuestions2
            ])
        ]);


        $gameSession3 = GameSession::create([
            'lesson_id' => $lesson3->id,
            'teacher_id' => $teacher->id,
            'title' => 'Solar System Adventure',
            'description' => 'Learn about planets and space',
            'topic' => 'Solar System',
            'game_pin' => 'SPACE1',
            'quiz_data' => json_encode(['topic' => 'Solar System', 'questions' => []]),
            'game_settings' => json_encode([
                'time_per_question' => 30,
                'points_per_question' => 100,
                'show_answers' => true,
                'randomize_questions' => false,
                'randomize_options' => false,
            ]),
            'status' => 'waiting',
            'total_questions' => 6,
            'allow_late_join' => false,
        ]);


        $questionsData3 = [
            [
                'difficulty' => 'easy',
                'concept' => 'The Sun',
                'question' => 'What is at the center of our solar system?',
                'options' => ['A' => 'Earth', 'B' => 'Moon', 'C' => 'Sun', 'D' => 'Mars'],
                'correct' => 'C',
                'explanation' => 'The Sun is at the center of our solar system. All planets orbit around it.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 1,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Earth',
                'question' => 'What planet do we live on?',
                'options' => ['A' => 'Mars', 'B' => 'Venus', 'C' => 'Earth', 'D' => 'Jupiter'],
                'correct' => 'C',
                'explanation' => 'We live on planet Earth. It\'s the third planet from the Sun.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 2,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Moon',
                'question' => 'What orbits around the Earth?',
                'options' => ['A' => 'Sun', 'B' => 'Moon', 'C' => 'Mars', 'D' => 'Stars'],
                'correct' => 'B',
                'explanation' => 'The Moon orbits around Earth. We can see it in the night sky.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 3,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Day and Night',
                'question' => 'What causes day and night?',
                'options' => ['A' => 'The Moon moving', 'B' => 'Clouds covering the Sun', 'C' => 'Earth spinning', 'D' => 'The Sun turning off'],
                'correct' => 'C',
                'explanation' => 'Day and night happen because Earth spins on its axis. When your side faces the Sun, it\'s day!',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 4,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Stars',
                'question' => 'When can we usually see stars?',
                'options' => ['A' => 'Morning', 'B' => 'Afternoon', 'C' => 'Night', 'D' => 'Never'],
                'correct' => 'C',
                'explanation' => 'We can see stars at night when the sky is dark. They are actually always there, but the Sun is too bright during the day!',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 5,
            ],
            [
                'difficulty' => 'easy',
                'concept' => 'Planets',
                'question' => 'Which planet is known as the Red Planet?',
                'options' => ['A' => 'Earth', 'B' => 'Mars', 'C' => 'Venus', 'D' => 'Jupiter'],
                'correct' => 'B',
                'explanation' => 'Mars is called the Red Planet because of its reddish color from iron oxide (rust) on its surface.',
                'points' => 100,
                'time_limit_seconds' => 30,
                'order' => 6,
            ],
        ];

        $quizDataQuestions3 = [];
        foreach ($questionsData3 as $questionData) {
            $question = \App\Models\Question::create([
                'game_session_id' => $gameSession3->id,
                'difficulty' => $questionData['difficulty'],
                'concept' => $questionData['concept'],
                'question' => $questionData['question'],
                'options' => $questionData['options'],
                'correct' => $questionData['correct'],
                'explanation' => $questionData['explanation'],
                'points' => $questionData['points'],
                'time_limit_seconds' => $questionData['time_limit_seconds'],
                'order' => $questionData['order'],
            ]);

            $quizDataQuestions3[] = [
                'id' => $question->id,
                'difficulty' => $question->difficulty,
                'concept' => $question->concept,
                'question' => $question->question,
                'options' => $question->options,
                'correct' => $question->correct,
                'explanation' => $question->explanation,
            ];
        }

        $gameSession3->update([
            'quiz_data' => json_encode([
                'topic' => 'Solar System',
                'questions' => $quizDataQuestions3
            ])
        ]);
    }
}
