<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trainee;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\Result;
use App\Models\QuestionPool;
use App\Models\AdminUser;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_trainees' => Trainee::count(),
            'active_trainees' => Trainee::where('status', 'Active')->count(),
            'inactive_trainees' => Trainee::where('status', 'Inactive')->count(),
            'total_courses' => Course::count(),
            'total_schedules' => Schedule::count(),
            'upcoming_schedules' => Schedule::where('start_date', '>=', now())->count(),
            'total_results' => Result::count(),
            'total_question_pools' => QuestionPool::count(),
            'total_admin_users' => AdminUser::count(),
            'recent_results' => Result::with(['trainee', 'course'])
                ->orderBy('completed_at', 'desc')
                ->take(5)
                ->get(),
            'recent_trainees' => Trainee::orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

