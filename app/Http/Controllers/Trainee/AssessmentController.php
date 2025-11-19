<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionPool;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function start($courseId)
    {
        $trainee = Auth::guard('trainee')->user();
        $course = Course::with('questionPools')->findOrFail($courseId);

        // Check if trainee has access to this course
        if (!$trainee->hasAccessToCourse($courseId)) {
            return redirect()->route('trainee.courses.index')
                ->with('error', 'You do not have access to this course. Please make a payment to gain access.');
        }

        // Check if trainee has already passed
        $hasPassed = Result::where('trainee_id', $trainee->id)
            ->where('course_id', $courseId)
            ->where('status', 'passed')
            ->exists();

        if ($hasPassed) {
            return redirect()->route('trainee.courses.show', $courseId)
                ->with('info', 'You have already passed this assessment.');
        }

        $questions = $course->questionPools()->inRandomOrder()->get();

        if ($questions->isEmpty()) {
            return redirect()->route('trainee.courses.show', $courseId)
                ->with('error', 'No questions available for this course.');
        }

        return view('trainee.assessment.start', compact('course', 'questions'));
    }

    public function submit(Request $request, $courseId)
    {
        $trainee = Auth::guard('trainee')->user();
        $course = Course::with('questionPools')->findOrFail($courseId);

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        $questions = $course->questionPools;
        $score = 0;
        $totalQuestions = $questions->count();
        $totalPoints = 0;
        $correctAnswers = [];

        foreach ($questions as $question) {
            $questionPoints = $question->points ?? 1;
            $totalPoints += $questionPoints;
            
            $userAnswer = $request->answers[$question->id] ?? null;
            $correctAnswer = $question->correct_answer;

            if ($userAnswer == $correctAnswer) {
                $score += $questionPoints;
                $correctAnswers[$question->id] = true;
            } else {
                $correctAnswers[$question->id] = false;
            }
        }

        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
        $passingScore = 70; // 70% passing score
        $status = $percentage >= $passingScore ? 'passed' : 'failed';

        // Save result
        $result = Result::create([
            'trainee_id' => $trainee->id,
            'course_id' => $courseId,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => round($percentage, 2),
            'status' => $status,
            'completed_at' => now(),
        ]);

        return redirect()->route('trainee.assessment.result', $result->id)
            ->with('result', $result)
            ->with('correctAnswers', $correctAnswers)
            ->with('questions', $questions);
    }

    public function result($resultId)
    {
        $trainee = Auth::guard('trainee')->user();
        $result = Result::with(['course', 'trainee'])->findOrFail($resultId);

        // Ensure the result belongs to the trainee
        if ($result->trainee_id !== $trainee->id) {
            abort(403);
        }

        return view('trainee.assessment.result', compact('result'));
    }
}
