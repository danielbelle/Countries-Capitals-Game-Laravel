<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    private $app_data;

    public function __construct()
    {
        // load app_data.php file from app folder
        $this->app_data = include app_path('app_data.php');
    }

    public function showData()
    {
        return response()->json($this->app_data);
    }

    public function startGame(): View
    {
        return view('home');
    }

    public function prepareGame(Request $request)
    {
        // validate request
        $request->validate(
            [
                'total_questions' => 'required|integer|min:3|max:30'
            ],
            [
                'total_questions.required' => 'O número de questões é obrigatório!',
                'total_questions.integer' => 'O número de questões tem que ser um valor inteiro!',
                'total_questions.min' => 'O número de questões tem que ser no mínimo 3!',
                'total_questions.max' => 'O número de questões tem que ser no máximo 30!'

            ]
        );

        // get total questions

        $total_questions = $request->input('total_questions');

        // prepare all the quiz structure
        $quiz = $this->prepareQuiz($total_questions);

        dd($quiz);
    }

    private function prepareQuiz($total_questions): array
    {
        // get all the questions
        $questions = [];
        $total_countries = count($this->app_data);
        $indexes = range(0, $total_countries - 1);
        shuffle($indexes);
        $indexes = array_slice($indexes, 0, $total_questions);

        // create array of questions
        $question_number = 1;
        foreach ($indexes as $index) {
            $question['question_number'] = $question_number++;
            $question['country'] = $this->app_data[$index]['country'];
            $question['correct_answer'] = $this->app_data[$index]['capital'];

            // wrong answers
            $other_capitals = array_column($this->app_data, 'capital');

            // remove the correct answer
            $other_capitals = array_diff($other_capitals, [$question['correct_answer']]);

            // shuffle the wrong answers
            shuffle($other_capitals);
            $question['wrong_answers'] = array_slice($other_capitals, 0, 3);

            // store answer result

            $question['correct'] = null;
            $questions[] = $question;
        }
        return $questions;
    }
}
