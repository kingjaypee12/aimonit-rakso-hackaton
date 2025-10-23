<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function quizSheets($uid)
    {
        return view('student.quiz-sheet', ['uid' => $uid]);
    }
}
