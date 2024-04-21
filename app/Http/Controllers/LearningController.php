<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuestion;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    public function index() {
        // mendapatkan data user
        $user = Auth::user();

        // mendapatkan data kelas yang dimiliki oleh user
        $my_courses = $user->courses()->with('category')->orderBy('id', 'DESC')->get();
        
        // melakukan foreach ulang agar mendapatkan data yang terbaru
        foreach($my_courses as $course) {
            // mendapatkah total pertanyaan dari suatu course
            $totalQuestionsCount = $course->questions()->count();

            // mendapatkan pertanyaan yang sudah dijawab student dalam suatu course
            $answeredQuestionsCount = StudentAnswer::where('user_id', $user->id)
            ->whereHas('question', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->distinct()->count('course_question_id');

            // misalnya baru jawab 5 dari 10 pertanyaan
            if($answeredQuestionsCount < $totalQuestionsCount) {
                // mencari pertanyaan mana yang belum dijawab
                $firstUnansweredQuestion = CourseQuestion::where('course_id', $course->id)
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('course_question_id')->from('student_answers')
                    ->where('user_id', $user->id);
                })->orderBy('id', 'asc')->first();

                // misal 10 pertanyaan, yang dijawab 2, berarti mulai dari 3,
                // kalo udah dijawab semua sampe 10, berarti null (artinya semua udah terjawab)
                $course->nextQuestionId = $firstUnansweredQuestion ? $firstUnansweredQuestion->id : null;
            }
            else {
                $course->nextQuestionId = null;
            }

        }

        return view('student.courses.index', [
            'my_courses' => $my_courses,
        ]);
    }

    public function learning(Course $course, $question) {
        // ambil data student yang sedang login
        $user = Auth::user();

        // pastikan student memiliki hak akses ke kelas yang dituju
        $isEnrolled = $user->courses()->where('course_id', $course->id)->exists();

        // kalo user belum enroll
        if(!$isEnrolled) {
            abort(404);
        }

        $currentQuestion = CourseQuestion::where('course_id', $course->id)->where('id', $question)->firstOrFail();

        return view('student.courses.learning', [
            'course' => $course,
            'question' => $currentQuestion,
        ]);
    }
}
