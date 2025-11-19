<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Course;
use App\Models\Trainee;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['trainee', 'course']);

        // Filter by course
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        // Search by trainee name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('trainee', function($q) use ($search) {
                $q->where('surname', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $results = $query->orderBy('completed_at', 'desc')->paginate(50);

        $courses = Course::where('status', 'Active')->orderBy('title')->get();
        
        $stats = [
            'total' => Result::count(),
            'passed' => Result::where('status', 'passed')->count(),
            'failed' => Result::where('status', 'failed')->count(),
            'average_score' => Result::avg('percentage'),
        ];

        return view('admin.results.index', compact('results', 'courses', 'stats'));
    }

    public function show($id)
    {
        $result = Result::with(['trainee', 'course'])->findOrFail($id);
        return view('admin.results.show', compact('result'));
    }
}

