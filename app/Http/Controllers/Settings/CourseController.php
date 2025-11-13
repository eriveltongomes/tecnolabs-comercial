<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('settings.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('settings.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'invested_value' => 'required|numeric|min:0',
            'lifespan_hours' => 'required|integer|min:1',
        ]);

        Course::create($request->all());

        return redirect()->route('settings.courses.index')->with('success', 'Curso cadastrado com sucesso.');
    }

    public function edit(Course $course)
    {
        return view('settings.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'invested_value' => 'required|numeric|min:0',
            'lifespan_hours' => 'required|integer|min:1',
        ]);

        $course->update($request->all());

        return redirect()->route('settings.courses.index')->with('success', 'Curso atualizado com sucesso.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('settings.courses.index')->with('success', 'Curso excluído com sucesso.');
    }
}