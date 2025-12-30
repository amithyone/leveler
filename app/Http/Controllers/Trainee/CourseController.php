<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Result;
use App\Models\Trainee;
use App\Helpers\TraineeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        // Admins can access all courses for testing
        $isAdmin = $user && $user->isAdmin();
        
        if (!$trainee && !$isAdmin) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee and access courses.');
        }
        
        // Get courses trainee has access to (or all courses for admin)
        if ($isAdmin) {
            $accessibleCourseIds = Course::where('status', 'Active')->pluck('id')->toArray();
            // Create trainee record for admin if needed
            if (!$trainee) {
                $trainee = Trainee::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'surname' => 'Admin',
                        'first_name' => $user->name,
                        'gender' => 'M',
                        'username' => 'admin_' . $user->id,
                        'password' => $user->password,
                        'phone_number' => '',
                        'status' => 'Active',
                    ]
                );
            }
        } else {
            $accessibleCourseIds = $trainee->accessibleCourses()->pluck('courses.id')->toArray();
        }
        
        $courses = Course::where('status', 'Active')
            ->with(['questionPools', 'results' => function($query) use ($trainee) {
                if ($trainee) {
                    $query->where('trainee_id', $trainee->id);
                }
            }])
            ->get()
            ->map(function($course) use ($trainee, $accessibleCourseIds, $isAdmin) {
                $course->has_access = $isAdmin || in_array($course->id, $accessibleCourseIds);
                $course->has_taken = $course->results->isNotEmpty();
                $course->has_passed = $course->results->where('status', 'Pass')->isNotEmpty();
                $course->latest_result = $course->results->sortByDesc('completed_at')->first();
                return $course;
            });

        return view('trainee.courses.index', compact('courses', 'trainee'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        // Admins can access all courses for testing
        $isAdmin = $user && $user->isAdmin();
        
        if (!$trainee && !$isAdmin) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee and access courses.');
        }
        
        // Create trainee record for admin if needed
        if ($isAdmin && !$trainee) {
            $trainee = Trainee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'surname' => 'Admin',
                    'first_name' => $user->name,
                    'gender' => 'M',
                    'username' => 'admin_' . $user->id,
                    'password' => $user->password,
                    'phone_number' => '',
                    'status' => 'Active',
                ]
            );
        }
        
        $course = Course::with('questionPools')->findOrFail($id);
        
        // Check if trainee has access to this course (admins always have access)
        $hasAccess = $isAdmin || ($trainee && $trainee->hasAccessToCourse($course->id));
        
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
