<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Helpers\TraineeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Note: Install barryvdh/laravel-dompdf for PDF generation
// Run: composer require barryvdh/laravel-dompdf
// use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee and view certificates.');
        }
        
        $certificates = Result::where('trainee_id', $trainee->id)
            ->where('status', 'passed')
            ->with('course')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->filter(function($result) use ($trainee) {
                // Only show certificates if trainee has fully paid
                return $trainee->hasFullyPaid();
            });

        return view('trainee.certificates.index', compact('certificates', 'trainee'));
    }

    public function download($resultId)
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee.');
        }
        $result = Result::with(['course', 'trainee'])->findOrFail($resultId);

        // Ensure the result belongs to the trainee and is passed
        if ($result->trainee_id !== $trainee->id || $result->status !== 'passed') {
            abort(403);
        }

        // Check if trainee has fully paid
        if (!$trainee->hasFullyPaid()) {
            return redirect()->route('trainee.certificates.index')
                ->with('error', 'You must complete your payment to download certificates. Remaining balance: ₦' . number_format($trainee->getRemainingBalance(), 2));
        }

        // Check if PDF package is available
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $data = [
                'trainee' => $result->trainee,
                'course' => $result->course,
                'result' => $result,
                'date' => $result->completed_at->format('F d, Y'),
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('trainee.certificates.pdf', $data);
            
            $filename = 'Certificate_' . $result->course->code . '_' . $trainee->username . '.pdf';
            
            return $pdf->download($filename);
        } else {
            // Fallback: redirect to view page where user can print
            return redirect()->route('trainee.certificates.view', $resultId)
                ->with('info', 'PDF download requires package installation. You can print this page instead.');
        }
    }

    public function view($resultId)
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee.');
        }
        
        $result = Result::with(['course', 'trainee'])->findOrFail($resultId);

        // Ensure the result belongs to the trainee and is passed
        if ($result->trainee_id !== $trainee->id || $result->status !== 'passed') {
            abort(403);
        }

        // Check if trainee has fully paid
        if (!$trainee->hasFullyPaid()) {
            return redirect()->route('trainee.certificates.index')
                ->with('error', 'You must complete your payment to view certificates. Remaining balance: ₦' . number_format($trainee->getRemainingBalance(), 2));
        }

        return view('trainee.certificates.view', compact('result', 'trainee'));
    }
}
