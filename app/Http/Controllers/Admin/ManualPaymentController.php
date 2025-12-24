<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManualPaymentSetting;

class ManualPaymentController extends Controller
{
    /**
     * Display a listing of the manual payment settings.
     */
    public function index()
    {
        $settings = ManualPaymentSetting::ordered()->get();
        return view('admin.manual-payments.index', compact('settings'));
    }

    /**
     * Show the form for creating a new manual payment setting.
     */
    public function create()
    {
        return view('admin.manual-payments.create');
    }

    /**
     * Store a newly created manual payment setting in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        ManualPaymentSetting::create([
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'instructions' => $request->instructions,
            'is_active' => $request->has('is_active') ? true : false,
            'display_order' => $request->display_order ?? 0,
        ]);

        return redirect()->route('admin.manual-payments.index')
            ->with('success', 'Manual payment setting created successfully.');
    }

    /**
     * Show the form for editing the specified manual payment setting.
     */
    public function edit($id)
    {
        $setting = ManualPaymentSetting::findOrFail($id);
        return view('admin.manual-payments.edit', compact('setting'));
    }

    /**
     * Update the specified manual payment setting in storage.
     */
    public function update(Request $request, $id)
    {
        $setting = ManualPaymentSetting::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $setting->update([
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'instructions' => $request->instructions,
            'is_active' => $request->has('is_active') ? true : false,
            'display_order' => $request->display_order ?? 0,
        ]);

        return redirect()->route('admin.manual-payments.index')
            ->with('success', 'Manual payment setting updated successfully.');
    }

    /**
     * Remove the specified manual payment setting from storage.
     */
    public function destroy($id)
    {
        $setting = ManualPaymentSetting::findOrFail($id);
        $setting->delete();

        return redirect()->route('admin.manual-payments.index')
            ->with('success', 'Manual payment setting deleted successfully.');
    }
}
