<?php

namespace App\Livewire\Teacher\Quizzes;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Quiz $quiz;

    #[Rule('required|string|max:255')]
    public string $title = '';

    public string $description = '';

    #[Rule('required|integer|min:0|max:100')]
    public int $pass_threshold = 70;

    #[Rule('nullable|integer|min:1')]
    public ?int $time_limit = null;

    public array $questions = [];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load('questions.answers');
        $this->title = $quiz->title;
        $this->description = $quiz->description ?? '';
        $this->pass_threshold = $quiz->pass_threshold;
        $this->time_limit = $quiz->time_limit;

        foreach ($quiz->questions as $question) {
            $answers = [];
            foreach ($question->answers as $answer) {
                $answers[] = [
                    'id' => Str::uuid()->toString(),
                    'existing_id' => $answer->id,
                    'answer' => $answer->answer,
                    'is_correct' => (bool) $answer->is_correct,
                ];
            }

            $this->questions[] = [
                'id' => Str::uuid()->toString(),
                'existing_id' => $question->id,
                'question' => $question->question,
                'order' => $question->order,
                'answers' => $answers,
            ];
        }
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'id' => Str::uuid()->toString(),
            'existing_id' => null,
            'question' => '',
            'order' => count($this->questions),
            'answers' => [
                [
                    'id' => Str::uuid()->toString(),
                    'existing_id' => null,
                    'answer' => '',
                    'is_correct' => true,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'existing_id' => null,
                    'answer' => '',
                    'is_correct' => false,
                ]
            ],
        ];
    }

    public function removeQuestion($index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function addAnswer($questionIndex)
    {
        $this->questions[$questionIndex]['answers'][] = [
            'id' => Str::uuid()->toString(),
            'existing_id' => null,
            'answer' => '',
            'is_correct' => false,
        ];
    }

    public function removeAnswer($questionIndex, $answerIndex)
    {
        unset($this->questions[$questionIndex]['answers'][$answerIndex]);
        $this->questions[$questionIndex]['answers'] = array_values($this->questions[$questionIndex]['answers']);
    }

    public function setCorrectAnswer($questionIndex, $answerIndex)
    {
        foreach ($this->questions[$questionIndex]['answers'] as $idx => $answer) {
            $this->questions[$questionIndex]['answers'][$idx]['is_correct'] = ($idx === $answerIndex);
        }
    }

    public function save()
    {
        $this->validate();

        $this->quiz->update([
            'title' => $this->title,
            'description' => $this->description,
            'pass_threshold' => $this->pass_threshold,
            'time_limit' => $this->time_limit,
        ]);

        $existingQuestionIds = [];

        foreach ($this->questions as $qIndex => $qData) {
            if (empty($qData['question'])) continue;

            $question = QuizQuestion::updateOrCreate(
                ['id' => $qData['existing_id'] ?? null],
                [
                    'quiz_id' => $this->quiz->id,
                    'question' => $qData['question'],
                    'order' => $qIndex,
                ]
            );

            $existingQuestionIds[] = $question->id;
            $existingAnswerIds = [];

            foreach ($qData['answers'] as $aData) {
                if (empty($aData['answer'])) continue;

                $answer = QuizAnswer::updateOrCreate(
                    ['id' => $aData['existing_id'] ?? null],
                    [
                        'quiz_question_id' => $question->id,
                        'answer' => $aData['answer'],
                        'is_correct' => $aData['is_correct'],
                    ]
                );
                
                $existingAnswerIds[] = $answer->id;
            }

            // Remove deleted answers
            QuizAnswer::where('quiz_question_id', $question->id)
                ->whereNotIn('id', $existingAnswerIds)
                ->delete();
        }

        // Remove deleted questions
        QuizQuestion::where('quiz_id', $this->quiz->id)
            ->whereNotIn('id', $existingQuestionIds)
            ->delete();

        session()->flash('success', 'Тест успешно обновлен.');
        return redirect()->route('teacher.quizzes.index');
    }

    public function render()
    {
        return view('livewire.teacher.quizzes.edit');
    }
}
