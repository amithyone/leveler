<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionPool;
use App\Models\Result;
use App\Models\Trainee;
use App\Helpers\TraineeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    public function start($courseId)
    {
        $user = Auth::user();
        
        // Admins can access all courses for testing
        $isAdmin = $user && $user->isAdmin();
        
        if ($isAdmin) {
            // Get or create a trainee record for admin (for result tracking)
            $trainee = TraineeHelper::getCurrentTrainee();
            if (!$trainee) {
                // Create a temporary trainee record for admin testing
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
            $trainee = TraineeHelper::getCurrentTrainee();
            
            if (!$trainee) {
                return redirect()->route('trainee.payments.create')
                    ->with('info', 'Please select a course package to become a trainee and take assessments.');
            }

            // Check if trainee has access to this course
            if (!$trainee->hasAccessToCourse($courseId)) {
                return redirect()->route('trainee.courses.index')
                    ->with('error', 'You do not have access to this course. Please make a payment to gain access.');
            }
        }
        
        $course = Course::with('questionPools')->findOrFail($courseId);

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
        
        // If course has no questions, allow file upload only
        if ($allQuestions->isEmpty()) {
            return view('trainee.assessment.file-only', compact('course'));
        }

        // Determine how many questions to ask
        $totalQuestionsInPool = $allQuestions->count();
        // Default to 50 questions if not specified, but don't exceed available questions
        $defaultQuestions = 50;
        $questionsToAsk = $course->assessment_questions_count ?? $defaultQuestions;
        
        // Ensure we don't ask more questions than available
        $questionsToAsk = min($questionsToAsk, $totalQuestionsInPool);
        
        // Randomly select questions - shuffle() ensures different questions each time a trainee takes the assessment
        // Each call to shuffle() creates a new random order, so every assessment attempt gets different questions
        $questions = $allQuestions->shuffle()->take($questionsToAsk);

        return view('trainee.assessment.start', compact('course', 'questions'));
    }

    public function submit(Request $request, $courseId)
    {
        $user = Auth::user();
        
        // Admins can access all courses for testing
        if ($user && $user->isAdmin()) {
            $trainee = TraineeHelper::getCurrentTrainee();
            if (!$trainee) {
                // Create a temporary trainee record for admin testing
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
            $trainee = TraineeHelper::getCurrentTrainee();
            
            if (!$trainee) {
                return redirect()->route('trainee.payments.create')
                    ->with('info', 'Please select a course package to become a trainee and take assessments.');
            }
        }
        
        $course = Course::with('questionPools')->findOrFail($courseId);

        // Check if course has questions
        $allQuestions = $course->questionPools;
        $hasQuestions = $allQuestions->isNotEmpty();
        
        if ($hasQuestions) {
            $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'required',
                'assessment_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png,zip,rar',
                'file_link' => 'nullable|url|max:500',
            ]);
        } else {
            // For courses without questions, file upload or link is required
            $request->validate([
                'assessment_file' => 'required_without:file_link|file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png,zip,rar',
                'file_link' => 'required_without:assessment_file|url|max:500',
            ]);
        }

        // Check if course has questions
        $allQuestions = $course->questionPools;
        $hasQuestions = $allQuestions->isNotEmpty();
        
        $score = 0;
        $totalQuestions = 0;
        $totalPoints = 0;
        $correctAnswers = [];
        $questionsAskedIds = [];
        $percentage = 0;
        $status = 'Pass'; // Default to Pass for file-only submissions

        if ($hasQuestions) {
            // Get the questions that were actually asked (from the submitted answers)
            $questionIds = array_keys($request->answers ?? []);
            $questions = QuestionPool::whereIn('id', $questionIds)
                ->where('course_id', $courseId)
                ->get()
                ->keyBy('id');
            
            $totalQuestions = $questions->count();

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
            $passingScore = $course->passing_score ?? 70; // Use course-specific passing score or default to 70%
            $status = $percentage >= $passingScore ? 'Pass' : 'Fail';
        }

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('assessment_file')) {
            $file = $request->file('assessment_file');
            $fileName = 'assessment_' . $trainee->id . '_' . $courseId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('assessment_files', $fileName, 'public');
        }

        // Get file link if provided
        $fileLink = $request->input('file_link');

        // For file-only submissions (no questions), set percentage to 100% and status to Pass
        if (!$hasQuestions) {
            $percentage = 100;
            $status = 'Pass';
        }

        // Save result
        $result = Result::create([
            'trainee_id' => $trainee->id,
            'course_id' => $courseId,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'questions_asked' => $hasQuestions ? $questionsAskedIds : null,
            'file_path' => $filePath,
            'file_link' => $fileLink,
            'percentage' => round($percentage, 2),
            'status' => $status,
            'completed_at' => now(),
        ]);

        $redirect = redirect()->route('trainee.assessment.result', $result->id)
            ->with('result', $result);
            
        if ($hasQuestions) {
            $redirect->with('correctAnswers', $correctAnswers)
                     ->with('questions', $questions);
        }
        
        return $redirect;
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
