<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Result;
use App\Helpers\TraineeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        // If user doesn't have a trainee record yet, show enrollment message
        if (!$trainee) {
            return view('trainee.dashboard', [
                'trainee' => null,
                'user' => $user,
                'stats' => [
                    'enrolled_courses' => 0,
                    'completed_courses' => 0,
                    'total_assessments' => 0,
                    'certificates' => collect([]),
                ],
                'recentResults' => collect([]),
                'availableCourses' => collect([]),
                'showEnrollment' => true
            ]);
        }
        
        $stats = [
            'total_courses' => Course::where('status', 'Active')->count(),
            'enrolled_courses' => $trainee->accessibleCourses()->count(),
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
                $course->has_access = $trainee->hasAccessToCourse($course->id);
                return $course;
            });

        return view('trainee.dashboard', compact('stats', 'recentResults', 'availableCourses', 'trainee', 'user'));
    }
}
