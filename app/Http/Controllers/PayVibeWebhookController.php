<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Payment;
use App\Models\Trainee;
use App\Models\Course;
use App\Mail\PaymentReceiptEmail;

class PayVibeWebhookController extends Controller
{
    /**
     * Handle incoming PayVibe webhooks
     */
    public function handle(Request $request)
    {
        try {
            $webhookData = $request->all();
            Log::info('PayVibeWebhook: Received webhook', $webhookData);

            // Extract webhook data
            $reference = $webhookData['reference'] ?? $webhookData['ref'] ?? null;
            $status = $webhookData['status'] ?? $webhookData['transaction_status'] ?? null;
            $amount = $webhookData['amount'] ?? null;

            if (!$reference) {
                Log::error('PayVibeWebhook: Missing reference', $webhookData);
                return response()->json(['error' => 'Missing reference'], 400);
            }

            // Find payment by reference
            $payment = Payment::where('transaction_reference', $reference)->first();

            if (!$payment) {
                Log::error('PayVibeWebhook: Payment not found', [
                    'reference' => $reference,
                    'payload' => $webhookData
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Check if payment already processed
            if ($payment->status == 'Completed') {
                Log::info('PayVibeWebhook: Payment already processed', [
                    'reference' => $reference,
                    'payment_id' => $payment->id
                ]);
                return response()->json(['message' => 'Payment already processed']);
            }

            // Process successful payment
            if ($status === 'success' || $status === 'completed' || $status === 'successful') {
                $trainee = Trainee::find($payment->trainee_id);
                
                if ($trainee) {
                    // Update payment status
                    $payment->status = 'Completed';
                    $payment->notes = 'PayVibe payment confirmed via webhook. Status: ' . $status;
                    $payment->save();

                    // Activate trainee if not already active
                    if ($trainee->status !== 'Active') {
                        $trainee->update(['status' => 'Active']);
                    }

                    // Update trainee's total paid and total required based on package
                    $trainee->total_paid = $trainee->getTotalPaid();
                    
                    // Set total_required based on the payment's package type
                    if ($payment->total_required) {
                        $trainee->total_required = $payment->total_required;
                    } else {
                        // Fallback: set based on package type
                        if ($payment->package_type === 'package') {
                            $trainee->total_required = 22500;
                        } elseif ($payment->package_type === 'single') {
                            $trainee->total_required = 10000;
                        }
                    }
                    $trainee->save();

                    // Grant course access based on payment package (even for installments)
                    if ($payment->course_access_count > 0) {
                        $this->grantCourseAccess($trainee, $payment);
                    }

                    // Send payment receipt email
                    if ($trainee->user && $trainee->user->email) {
                        try {
                            Mail::to($trainee->user->email)->send(new PaymentReceiptEmail($payment, $trainee));
                        } catch (\Exception $e) {
                            Log::error('PayVibeWebhook: Failed to send payment receipt email: ' . $e->getMessage());
                            // Don't fail webhook if email fails
                        }
                    }

                    Log::info('PayVibeWebhook: Payment processed successfully', [
                        'reference' => $reference,
                        'status' => $status,
                        'amount' => $amount,
                        'trainee_id' => $trainee->id,
                        'course_access_count' => $payment->course_access_count,
                    ]);

                    return response()->json(['message' => 'Payment processed successfully']);
                }
            }

            Log::error('PayVibeWebhook: Trainee not found', [
                'reference' => $reference,
                'trainee_id' => $payment->trainee_id
            ]);

            return response()->json(['error' => 'Trainee not found'], 404);

        } catch (\Exception $e) {
            Log::error('PayVibeWebhook: Processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Grant course access to trainee based on payment
     */
    private function grantCourseAccess(Trainee $trainee, Payment $payment)
    {
        $courseAccessCount = $payment->course_access_count;
        
        // Get courses trainee already has access to
        $existingAccess = $trainee->accessibleCourses()->pluck('courses.id')->toArray();

        // Priority 1: Use selected_courses from trainee registration if available
        $coursesToGrant = [];
        if ($trainee->selected_courses && count($trainee->selected_courses) > 0) {
            // Filter to only include courses that are active and not already granted
            $selectedCourseIds = is_array($trainee->selected_courses) ? $trainee->selected_courses : [];
            $availableSelectedCourses = Course::where('status', 'Active')
                ->whereIn('id', $selectedCourseIds)
                ->pluck('id')
                ->toArray();
            
            // Filter out courses trainee already has access to
            $coursesToGrant = array_values(array_diff($availableSelectedCourses, $existingAccess));
            
            Log::info('PayVibeWebhook: Using selected courses from registration', [
                'trainee_id' => $trainee->id,
                'selected_courses' => $selectedCourseIds,
                'available_selected' => $availableSelectedCourses,
                'courses_to_grant' => $coursesToGrant,
            ]);
        }
        
        // Priority 2: Fallback to first N courses if no selected_courses (backward compatibility)
        if (empty($coursesToGrant)) {
            $availableCourses = Course::where('status', 'Active')
                ->orderBy('id')
                ->get();

            // Filter out courses trainee already has access to
            $coursesToGrant = $availableCourses->whereNotIn('id', $existingAccess)
                ->take($courseAccessCount)
                ->pluck('id')
                ->toArray();
            
            Log::info('PayVibeWebhook: Using fallback course selection', [
                'trainee_id' => $trainee->id,
                'course_access_count' => $courseAccessCount,
                'courses_to_grant' => $coursesToGrant,
            ]);
        }

        if (count($coursesToGrant) > 0) {
            $trainee->grantCourseAccess($coursesToGrant, $payment->id);
            
            Log::info('PayVibeWebhook: Course access granted', [
                'trainee_id' => $trainee->id,
                'payment_id' => $payment->id,
                'courses_granted' => count($coursesToGrant),
                'course_ids' => $coursesToGrant,
            ]);
        } else {
            Log::warning('PayVibeWebhook: No courses available to grant', [
                'trainee_id' => $trainee->id,
                'payment_id' => $payment->id,
                'requested_count' => $courseAccessCount,
                'existing_access_count' => count($existingAccess),
                'has_selected_courses' => !empty($trainee->selected_courses),
            ]);
        }
    }
}
