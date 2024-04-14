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

    public function index()
    {
        $courses = Course::orderBy('id', 'DESC')->get();
        return view('admin.courses.index', [
            'courses' => $courses,
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.courses.create', [
            'categories' => $categories,
        ]);
    }

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

    public function show(Course $course)
    {
        //
    }

    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.edit', [
            'course' => $course,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'cover' => 'sometimes|image|mimes:png,jpg,svg',
            'name' => 'required|string|max:255',
            'category_id' => 'sometimes|integer',
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
            
            $course->update($validated);
            
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
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return redirect()->route('dashboard.courses.index');
        }
        catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error! ' . $e->getMessage()],
            ]);

            throw $error;
        }
    }
}
