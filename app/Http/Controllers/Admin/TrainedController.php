<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainee;
use App\Models\Result;
use App\Models\Course;
use Illuminate\Http\Request;

class TrainedController extends Controller
{
    public function index(Request $request)
    {
        $query = Trainee::whereHas('myResults', function($q) {
            $q->where('status', 'passed');
        })->with(['myResults.course']);

        // Filter by course
        if ($request->has('course_id') && $request->course_id) {
            $query->whereHas('myResults', function($q) use ($request) {
                $q->where('course_id', $request->course_id)->where('status', 'passed');
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('surname', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $trainees = $query->orderBy('surname')->orderBy('first_name')->paginate(50);

        // Add certificate count for each trainee
        $trainees->getCollection()->transform(function($trainee) {
            $trainee->certificate_count = $trainee->myResults()->where('status', 'passed')->count();
            $trainee->latest_certificate = $trainee->myResults()
                ->where('status', 'passed')
                ->with('course')
                ->orderBy('completed_at', 'desc')
                ->first();
            return $trainee;
        });

        $courses = Course::where('status', 'Active')->orderBy('title')->get();

        $stats = [
            'total_trained' => Trainee::whereHas('myResults', function($q) {
                $q->where('status', 'passed');
            })->count(),
            'total_certificates' => Result::where('status', 'passed')->count(),
            'total_courses_completed' => Result::where('status', 'passed')
                ->distinct('course_id')
                ->count('course_id'),
        ];

        return view('admin.trained.index', compact('trainees', 'courses', 'stats'));
    }
}

