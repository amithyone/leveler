<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $trainee = Auth::guard('trainee')->user();
        
        // Get courses trainee has access to
        $accessibleCourseIds = $trainee->accessibleCourses()->pluck('courses.id')->toArray();
        
        $courses = Course::where('status', 'Active')
            ->with(['questionPools', 'results' => function($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id);
            }])
            ->get()
            ->map(function($course) use ($trainee, $accessibleCourseIds) {
                $course->has_access = in_array($course->id, $accessibleCourseIds);
                $course->has_taken = $course->results->isNotEmpty();
                $course->has_passed = $course->results->where('status', 'passed')->isNotEmpty();
                $course->latest_result = $course->results->sortByDesc('completed_at')->first();
                return $course;
            });

        return view('trainee.courses.index', compact('courses', 'trainee'));
    }

    public function show($id)
    {
        $trainee = Auth::guard('trainee')->user();
        $course = Course::with('questionPools')->findOrFail($id);
        
        // Check if trainee has access to this course
        $hasAccess = $trainee->hasAccessToCourse($course->id);
        
        if (!$hasAccess) {
            return redirect()->route('trainee.courses.index')
                ->with('error', 'You do not have access to this course. Please make a payment to gain access.');
        }
        
        $hasTaken = Result::where('trainee_id', $trainee->id)
            ->where('course_id', $course->id)
            ->exists();
        
        $hasPassed = Result::where('trainee_id', $trainee->id)
            ->where('course_id', $course->id)
            ->where('status', 'passed')
            ->exists();

        $latestResult = Result::where('trainee_id', $trainee->id)
            ->where('course_id', $course->id)
            ->orderBy('completed_at', 'desc')
            ->first();

        return view('trainee.courses.show', compact('course', 'hasTaken', 'hasPassed', 'latestResult', 'hasAccess'));
    }
}
