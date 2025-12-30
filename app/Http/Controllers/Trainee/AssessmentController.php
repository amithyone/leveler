<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionPool;
use App\Models\Result;
use App\Helpers\TraineeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function start($courseId)
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee and take assessments.');
        }
        
        $course = Course::with('questionPools')->findOrFail($courseId);

        // Check if trainee has access to this course
        if (!$trainee->hasAccessToCourse($courseId)) {
            return redirect()->route('trainee.courses.index')
                ->with('error', 'You do not have access to this course. Please make a payment to gain access.');
        }

        // Check if trainee has already passed
        $hasPassed = Result::where('trainee_id', $trainee->id)
            ->where('course_id', $courseId)
            ->where('status', 'Pass')
            ->exists();

        if ($hasPassed) {
            return redirect()->route('trainee.courses.show', $courseId)
                ->with('info', 'You have already passed this assessment.');
        }

        // Get all questions from pool
        $allQuestions = $course->questionPools;
        
        if ($allQuestions->isEmpty()) {
            return redirect()->route('trainee.courses.show', $courseId)
                ->with('error', 'No questions available for this course.');
        }

        // Determine how many questions to ask
        $totalQuestionsInPool = $allQuestions->count();
        $questionsToAsk = $course->assessment_questions_count ?? $totalQuestionsInPool;
        
        // Ensure we don't ask more questions than available
        $questionsToAsk = min($questionsToAsk, $totalQuestionsInPool);
        
        // Randomly select questions
        $questions = $allQuestions->shuffle()->take($questionsToAsk);

        return view('trainee.assessment.start', compact('course', 'questions'));
    }

    public function submit(Request $request, $courseId)
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee and take assessments.');
        }
        
        $course = Course::with('questionPools')->findOrFail($courseId);

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        // Get the questions that were actually asked (from the submitted answers)
        $questionIds = array_keys($request->answers);
        $questions = QuestionPool::whereIn('id', $questionIds)
            ->where('course_id', $courseId)
            ->get()
            ->keyBy('id');
        
        $score = 0;
        $totalQuestions = $questions->count();
        $totalPoints = 0;
        $correctAnswers = [];
        $questionsAskedIds = [];

        foreach ($questions as $question) {
            $questionsAskedIds[] = $question->id;
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
        $status = $percentage >= $passingScore ? 'Pass' : 'Fail';

        // Save result
        $result = Result::create([
            'trainee_id' => $trainee->id,
            'course_id' => $courseId,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'questions_asked' => $questionsAskedIds,
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
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee.');
        }
        
        $result = Result::with(['course', 'trainee'])->findOrFail($resultId);

        // Ensure the result belongs to the trainee
        if ($result->trainee_id !== $trainee->id) {
            abort(403);
        }

        return view('trainee.assessment.result', compact('result', 'trainee'));
    }
}
