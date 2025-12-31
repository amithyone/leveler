<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionPool;
use Illuminate\Http\Request;

class QuestionPoolController extends Controller
{
    public function index(Request $request)
    {
        $courseId = $request->get('course');
        
        if ($courseId) {
            $course = Course::findOrFail($courseId);
            $questions = QuestionPool::where('course_id', $courseId)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return view('admin.question-pool.course-questions', compact('course', 'questions'));
        }

        // Show all courses if no course selected
        $courses = Course::withCount('questionPools as question_pools_count')
            ->orderBy('code')
            ->get();
        
        return view('admin.question-pool.index', compact('courses'));
    }

    /**
     * Show questions for a specific course
     */
    public function showCourseQuestions($courseId)
    {
        $course = Course::findOrFail($courseId);
        $questions = QuestionPool::where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.question-pool.course-questions', compact('course', 'questions'));
    }

    /**
     * Show create form for a question
     */
    public function create(Request $request)
    {
        $courseId = $request->get('course');
        $courses = Course::orderBy('code')->get();
        
        if ($courseId) {
            $course = Course::findOrFail($courseId);
            return view('admin.question-pool.create', compact('courses', 'course'));
        }
        
        return view('admin.question-pool.create', compact('courses'));
    }

    /**
     * Store a new question
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,essay',
            'option_a' => 'required_if:type,multiple_choice|string|nullable',
            'option_b' => 'required_if:type,multiple_choice|string|nullable',
            'option_c' => 'required_if:type,multiple_choice|string|nullable',
            'option_d' => 'required_if:type,multiple_choice|string|nullable',
            'correct_answer' => 'required|string|min:1',
            'points' => 'required|integer|min:1',
        ], [
            'correct_answer.required' => 'Please provide a correct answer.',
            'correct_answer.min' => 'The correct answer cannot be empty.',
        ]);

        // Build options array based on question type
        $options = null;
        if ($request->type === 'multiple_choice') {
            $options = [];
            if ($request->option_a) $options['A'] = $request->option_a;
            if ($request->option_b) $options['B'] = $request->option_b;
            if ($request->option_c) $options['C'] = $request->option_c;
            if ($request->option_d) $options['D'] = $request->option_d;
        } elseif ($request->type === 'true_false') {
            $options = [
                'A' => 'True',
                'B' => 'False'
            ];
        }

        QuestionPool::create([
            'course_id' => $request->course_id,
            'question' => $request->question,
            'type' => $request->type,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'points' => $request->points,
        ]);

        return redirect()->route('admin.question-pool.course', $request->course_id)
            ->with('success', 'Question added successfully!');
    }

    /**
     * Show edit form for a question
     */
    public function edit($id)
    {
        $question = QuestionPool::with('course')->findOrFail($id);
        return view('admin.question-pool.edit', compact('question'));
    }

    /**
     * Update a question
     */
    public function update(Request $request, $id)
    {
        $question = QuestionPool::findOrFail($id);

        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,essay',
            'option_a' => 'required_if:type,multiple_choice|string|nullable',
            'option_b' => 'required_if:type,multiple_choice|string|nullable',
            'option_c' => 'required_if:type,multiple_choice|string|nullable',
            'option_d' => 'required_if:type,multiple_choice|string|nullable',
            'correct_answer' => 'required|string|min:1',
            'points' => 'required|integer|min:1',
        ], [
            'correct_answer.required' => 'Please provide a correct answer.',
            'correct_answer.min' => 'The correct answer cannot be empty.',
        ]);

        // Build options array based on question type
        $options = null;
        if ($request->type === 'multiple_choice') {
            $options = [];
            if ($request->option_a) $options['A'] = $request->option_a;
            if ($request->option_b) $options['B'] = $request->option_b;
            if ($request->option_c) $options['C'] = $request->option_c;
            if ($request->option_d) $options['D'] = $request->option_d;
        } elseif ($request->type === 'true_false') {
            $options = [
                'A' => 'True',
                'B' => 'False'
            ];
        }

        $question->update([
            'question' => $request->question,
            'type' => $request->type,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'points' => $request->points,
        ]);

        return redirect()->route('admin.question-pool.course', $question->course_id)
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Delete a question
     */
    public function destroy($id)
    {
        $question = QuestionPool::findOrFail($id);
        $courseId = $question->course_id;
        $question->delete();

        return redirect()->route('admin.question-pool.course', $courseId)
            ->with('success', 'Question deleted successfully!');
    }
}

