<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainee;
use App\Models\Course;
use App\Models\Result;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Overall Statistics
        $stats = [
            'total_trainees' => Trainee::count(),
            'active_trainees' => Trainee::where('status', 'Active')->count(),
            'total_courses' => Course::count(),
            'total_results' => Result::count(),
            'passed_results' => Result::where('status', 'passed')->count(),
            'failed_results' => Result::where('status', 'failed')->count(),
            'total_payments' => Payment::where('status', 'Completed')->count(),
            'total_revenue' => Payment::where('status', 'Completed')->sum('amount'),
        ];

        // Course Performance
        $coursePerformance = Course::withCount(['results as total_results', 'results as passed_results' => function($query) {
            $query->where('status', 'passed');
        }])
        ->withCount('questionPools as total_questions')
        ->get()
        ->map(function($course) {
            $course->pass_rate = $course->total_results > 0 
                ? ($course->passed_results / $course->total_results) * 100 
                : 0;
            $course->average_score = Result::where('course_id', $course->id)->avg('percentage') ?? 0;
            return $course;
        })
        ->sortByDesc('total_results')
        ->take(10);

        // Recent Activity
        $recentResults = Result::with(['trainee', 'course'])
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Payment Statistics
        $paymentStats = [
            'today' => Payment::where('status', 'Completed')
                ->whereDate('payment_date', today())
                ->sum('amount'),
            'this_month' => Payment::where('status', 'Completed')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'this_year' => Payment::where('status', 'Completed')
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
        ];

        // Trainee Status Distribution
        $traineeStatus = [
            'active' => Trainee::where('status', 'Active')->count(),
            'inactive' => Trainee::where('status', 'Inactive')->count(),
            'with_payment' => Trainee::whereHas('payments', function($q) {
                $q->where('status', 'Completed');
            })->count(),
        ];

        return view('admin.reports.index', compact('stats', 'coursePerformance', 'recentResults', 'paymentStats', 'traineeStatus'));
    }
}

