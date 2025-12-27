<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionPool;
use App\Models\Result;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return view('admin.courses.index');
    }

    public function view()
    {
        $courses = Course::withCount([
            'questionPools as total_questions',
            'accessibleTrainees as enrolled_trainees',
            'results as total_results'
        ])
        ->orderBy('code')
        ->get()
        ->map(function($course) {
            // Calculate assessment duration from questions (assuming 1.5 mins per question)
            $course->estimated_duration = $course->total_questions > 0 
                ? ceil($course->total_questions * 1.5) 
                : 0;
            return $course;
        });

        return view('admin.courses.view', compact('courses'));
    }

    /**
     * Show course details
     */
    public function show($id)
    {
        $course = Course::withCount([
            'questionPools as total_questions',
            'accessibleTrainees as enrolled_trainees',
            'results as total_results',
            'schedules as total_schedules'
        ])
        ->with(['questionPools' => function($query) {
            $query->limit(5)->latest();
        }])
        ->findOrFail($id);

        // Get recent results
        $recentResults = Result::where('course_id', $course->id)
            ->with('trainee')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // Get enrolled trainees
        $enrolledTrainees = $course->accessibleTrainees()
            ->with('latestPayment')
            ->limit(10)
            ->get();

        return view('admin.courses.show', compact('course', 'recentResults', 'enrolledTrainees'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update course
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:courses,code,' . $id,
            'description' => 'nullable|string',
            'duration_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        $course = Course::findOrFail($id);
        $course->update($request->only([
            'title', 'code', 'description', 'duration_hours', 'status'
        ]));

        return redirect()->route('admin.courses.view')
            ->with('success', 'Course updated successfully!');
    }
}

