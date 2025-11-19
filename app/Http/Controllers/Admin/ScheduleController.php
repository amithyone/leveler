<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Course;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with('course');

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
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $schedules = $query->orderBy('start_date', 'desc')->paginate(50);
        $courses = Course::where('status', 'Active')->orderBy('title')->get();

        $stats = [
            'total' => Schedule::count(),
            'scheduled' => Schedule::where('status', 'Scheduled')->count(),
            'ongoing' => Schedule::where('status', 'Ongoing')->count(),
            'completed' => Schedule::where('status', 'Completed')->count(),
        ];

        return view('admin.schedules.index', compact('schedules', 'courses', 'stats'));
    }

    public function create()
    {
        $courses = Course::where('status', 'Active')->orderBy('title')->get();
        return view('admin.schedules.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:Scheduled,Ongoing,Completed,Cancelled',
        ]);

        Schedule::create($request->all());

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created successfully!');
    }

    public function edit($id)
    {
        $schedule = Schedule::with('course')->findOrFail($id);
        $courses = Course::where('status', 'Active')->orderBy('title')->get();
        return view('admin.schedules.edit', compact('schedule', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:Scheduled,Ongoing,Completed,Cancelled',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully!');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully!');
    }
}

