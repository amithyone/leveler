<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManualPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = ManualPaymentSetting::orderBy('order')->orderBy('name')->get();
        return view('admin.manual-payments.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.manual-payments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'payment_instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        ManualPaymentSetting::create([
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'payment_instructions' => $request->payment_instructions,
            'is_active' => $request->has('is_active') ? true : false,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.manual-payments.index')
            ->with('success', 'Manual payment setting created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $setting = ManualPaymentSetting::findOrFail($id);
        return view('admin.manual-payments.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $setting = ManualPaymentSetting::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'payment_instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $setting->update([
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'payment_instructions' => $request->payment_instructions,
            'is_active' => $request->has('is_active') ? true : false,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.manual-payments.index')
            ->with('success', 'Manual payment setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $setting = ManualPaymentSetting::findOrFail($id);
        $setting->delete();

        return redirect()->route('admin.manual-payments.index')
            ->with('success', 'Manual payment setting deleted successfully.');
    }
}
