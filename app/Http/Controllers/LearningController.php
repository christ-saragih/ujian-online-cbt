<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    public function index() {
        // mendapatkan data user
        $user = Auth::user();

        // mendapatkan data kelas yang dimiliki oleh user
        $my_courses = $user->courses()->with('category')->orderBy('id', 'DESC')->get();

        // dd($my_courses);

        return view('student.courses.index', [
            'my_courses' => $my_courses,
        ]);
    }
}
