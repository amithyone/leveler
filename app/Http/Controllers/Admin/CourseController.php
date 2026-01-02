<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionPool;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     * Show create form
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store new course
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:courses,code',
            'description' => 'nullable|string',
            'assessment_questions_count' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'training_link' => 'nullable|url|max:500',
            'whatsapp_link' => 'nullable|url|max:500',
            'status' => 'required|in:Active,Inactive',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $data = $request->only([
            'title', 'code', 'description', 'assessment_questions_count', 'passing_score', 'training_link', 'whatsapp_link', 'status'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('courses', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        // Set default assessment_questions_count to 50 if not provided
        if (!isset($data['assessment_questions_count']) || empty($data['assessment_questions_count'])) {
            $data['assessment_questions_count'] = 50;
        }
        
        Course::create($data);

        return redirect()->route('admin.courses.view')
            ->with('success', 'Course created successfully!');
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
            'assessment_questions_count' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'training_link' => 'nullable|url|max:500',
            'whatsapp_link' => 'nullable|url|max:500',
            'status' => 'required|in:Active,Inactive',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $course = Course::findOrFail($id);
        $data = $request->only([
            'title', 'code', 'description', 'assessment_questions_count', 'passing_score', 'training_link', 'whatsapp_link', 'status'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('courses', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $course->update($data);

        return redirect()->route('admin.courses.view')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Delete a course
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        // Check if course has any enrollments or results
        $enrollmentsCount = $course->accessibleTrainees()->count();
        $resultsCount = $course->results()->count();
        $questionsCount = $course->questionPools()->count();
        
        if ($enrollmentsCount > 0 || $resultsCount > 0) {
            return redirect()->route('admin.courses.view')
                ->with('error', 'Cannot delete course. It has ' . 
                    ($enrollmentsCount > 0 ? $enrollmentsCount . ' enrolled trainee(s)' : '') .
                    ($enrollmentsCount > 0 && $resultsCount > 0 ? ' and ' : '') .
                    ($resultsCount > 0 ? $resultsCount . ' result(s)' : '') . 
                    '. Please remove all enrollments and results first.');
        }
        
        // Delete associated question pools (cascade should handle this, but being explicit)
        $course->questionPools()->delete();
        
        // Delete the course
        $course->delete();
        
        return redirect()->route('admin.courses.view')
            ->with('success', 'Course deleted successfully!');
    }
}

