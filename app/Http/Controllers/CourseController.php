<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::orderBy('id', 'DESC')->get();
        return view('admin.courses.index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.courses.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cover' => 'required|image|mimes:png,jpg,svg',
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
        ]);

        // import database dan mempersiapkan u/ melakukan insert data pada db yang terhubung
        // kelebihannya: jika ada kecacatan data, bisa melakukan rollback. artinya data yang masuk tsb harus sempurna.
        DB::beginTransaction();

        try {
            // jika di dalam form memiliki sebuah "file" dengan name: "cover"
            if($request->hasFile('cover')) {
                // melakukan proses peng-copyan data
                $coverPath = $request->file('cover')->store('product_covers', 'public');
                $validated['cover'] = $coverPath;
            }

            // name: Basic Laravel, slug: basic-laravel
            $validated['slug'] = Str::slug($request->name);
            $newCourse = Course::create($validated);
            
            DB::commit();

            return redirect()->route('dashboard.courses.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error! ' . $e->getMessage()],
            ]);

            throw $error;
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
