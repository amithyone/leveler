<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Trainee;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('trainee');

        // Search by trainee name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('trainee', function($q) use ($search) {
                $q->where('surname', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $trainees = Trainee::orderBy('surname')->get();
        return view('admin.payments.create', compact('trainees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Manual Payment,Other',
            'payment_date' => 'required|date',
            'status' => 'required|in:Pending,Completed,Failed,Refunded',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'manual_payment_details' => 'nullable|array',
            'manual_payment_details.bank_name' => 'nullable|string|max:255',
            'manual_payment_details.account_name' => 'nullable|string|max:255',
            'manual_payment_details.account_number' => 'nullable|string|max:255',
            'manual_payment_details.instructions' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Handle manual payment details
        if ($request->payment_method === 'Manual Payment' && $request->has('manual_payment_details')) {
            $data['manual_payment_details'] = $request->manual_payment_details;
        } else {
            $data['manual_payment_details'] = null;
        }
        
        $payment = Payment::create($data);

        // If payment is completed, activate the trainee
        if ($payment->status === 'Completed') {
            $payment->trainee->update(['status' => 'Active']);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment recorded successfully');
    }

    public function edit($id)
    {
        $payment = Payment::with('trainee')->findOrFail($id);
        $trainees = Trainee::orderBy('surname')->get();
        return view('admin.payments.edit', compact('payment', 'trainees'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $oldStatus = $payment->status;

        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Manual Payment,Other',
            'payment_date' => 'required|date',
            'status' => 'required|in:Pending,Completed,Failed,Refunded',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'manual_payment_details' => 'nullable|array',
            'manual_payment_details.bank_name' => 'nullable|string|max:255',
            'manual_payment_details.account_name' => 'nullable|string|max:255',
            'manual_payment_details.account_number' => 'nullable|string|max:255',
            'manual_payment_details.instructions' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Handle manual payment details
        if ($request->payment_method === 'Manual Payment' && $request->has('manual_payment_details')) {
            $data['manual_payment_details'] = $request->manual_payment_details;
        } else {
            $data['manual_payment_details'] = null;
        }
        
        $payment->update($data);

        // If payment status changed to Completed, activate the trainee
        if ($payment->status === 'Completed' && $oldStatus !== 'Completed') {
            $payment->trainee->update(['status' => 'Active']);
        }

        // If payment status changed from Completed to something else, check if trainee has other completed payments
        if ($oldStatus === 'Completed' && $payment->status !== 'Completed') {
            if (!$payment->trainee->hasCompletedPayment()) {
                $payment->trainee->update(['status' => 'Inactive']);
            }
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment updated successfully');
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $trainee = $payment->trainee;
        
        $payment->delete();

        // If deleted payment was completed, check if trainee has other completed payments
        if ($payment->status === 'Completed') {
            if (!$trainee->hasCompletedPayment()) {
                $trainee->update(['status' => 'Inactive']);
            }
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment deleted successfully');
    }
}
