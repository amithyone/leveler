<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $trainee = Auth::guard('trainee')->user();
        
        $stats = [
            'total_courses' => Course::where('status', 'Active')->count(),
            'enrolled_courses' => Course::where('status', 'Active')->count(), // All active courses are available
            'completed_courses' => Result::where('trainee_id', $trainee->id)
                ->where('status', 'passed')
                ->distinct('course_id')
                ->count(),
            'total_assessments' => Result::where('trainee_id', $trainee->id)->count(),
            'certificates' => Result::where('trainee_id', $trainee->id)
                ->where('status', 'passed')
                ->with('course')
                ->get(),
        ];

        $recentResults = Result::where('trainee_id', $trainee->id)
            ->with('course')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        $availableCourses = Course::where('status', 'Active')
            ->with(['questionPools', 'results' => function($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id);
            }])
            ->get()
            ->map(function($course) use ($trainee) {
                $course->has_taken = $course->results->isNotEmpty();
                $course->has_passed = $course->results->where('status', 'passed')->isNotEmpty();
                return $course;
            });

        return view('trainee.dashboard', compact('stats', 'recentResults', 'availableCourses'));
    }
}
