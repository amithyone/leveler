<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TraineeRegisterController extends Controller
{
    /**
     * Show the registration category selection page.
     */
    public function showCategorySelection()
    {
        return view('trainee.auth.category');
    }

    /**
     * Handle category selection and redirect to appropriate registration form.
     */
    public function selectCategory(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:nysc,working_class',
            'package_type' => 'required_if:user_type,nysc|in:A,B,C,D',
            'max_courses' => 'required_if:user_type,nysc|integer|min:1|max:9',
            'total_amount' => 'required_if:user_type,nysc|numeric|min:0',
        ]);

        // Store user type in session
        $request->session()->put('registration_user_type', $request->user_type);

        // Store package info for NYSC
        if ($request->user_type === 'nysc') {
            $request->session()->put('registration_package', [
                'type' => $request->package_type,
                'max_courses' => $request->max_courses,
                'total_amount' => $request->total_amount,
            ]);
        }

        return redirect()->route('trainee.register.form');
    }

    /**
     * Show the registration form based on selected category.
     */
    public function showRegistrationForm(Request $request)
    {
        $userType = $request->session()->get('registration_user_type');
        
        if (!$userType) {
            return redirect()->route('trainee.register');
        }

        // Get courses for selection (limit to 9 for NYSC packages)
        $courses = \App\Models\Course::where('status', 'Active')->limit(9)->get();
        
        if ($userType === 'nysc') {
            $package = $request->session()->get('registration_package');
            if (!$package) {
                return redirect()->route('trainee.register')
                    ->with('error', 'Please select a package first');
            }
            return view('trainee.auth.register-nysc', compact('courses', 'package'));
        } else {
            return view('trainee.auth.register-working-class', compact('courses'));
        }
    }

    /**
     * Handle NYSC registration request.
     */
    public function registerNysc(Request $request)
    {
        $package = $request->session()->get('registration_package');
        if (!$package) {
            return redirect()->route('trainee.register')
                ->with('error', 'Please select a package first');
        }

        $minCourses = $package['type'] === 'A' ? 1 : ($package['type'] === 'B' ? 2 : ($package['type'] === 'C' ? 4 : 7));
        $maxCourses = $package['max_courses'];

        $request->validate([
            'full_name' => 'required|string|max:255',
            'state_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'courses' => "required|array|min:{$minCourses}|max:{$maxCourses}",
            'courses.*' => 'required|exists:courses,id',
        ]);

        // Generate email from name (for NYSC, email is optional)
        $email = strtolower(str_replace(' ', '', $request->full_name)) . '@nysc.leveler.com';
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = strtolower(str_replace(' ', '', $request->full_name)) . $counter . '@nysc.leveler.com';
            $counter++;
        }

        // Generate password
        $password = 'Leveler' . rand(1000, 9999);

        // Create user account
        $user = User::create([
            'name' => strtoupper($request->full_name),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'user',
            'user_type' => 'nysc',
        ]);

        // Create trainee record immediately with course selections
        $trainee = Trainee::create([
            'user_id' => $user->id,
            'surname' => strtoupper($request->full_name), // For NYSC, use full name as surname
            'first_name' => '',
            'middle_name' => null,
            'gender' => 'M', // Default
            'phone_number' => $request->whatsapp_number,
            'whatsapp_number' => $request->whatsapp_number,
            'state_code' => strtoupper($request->state_code),
            'username' => self::generateUsername(),
            'password' => $password, // Store plain text for backward compatibility
            'status' => 'Inactive', // Will be activated when payment is made
            'user_type' => 'nysc',
            'nysc_start_date' => now(), // Start countdown timer
            'package_type' => $package['type'], // Store package type
            'total_required' => $package['total_amount'], // Store total package amount
        ]);

        // Store selected courses in session for payment page
        $request->session()->put('selected_courses', $request->courses);
        $request->session()->put('user_type', 'nysc');
        $request->session()->put('registration_data', [
            'full_name' => $request->full_name,
            'state_code' => $request->state_code,
            'whatsapp_number' => $request->whatsapp_number,
        ]);
        $request->session()->put('package_info', $package); // Keep package info for payment

        // Log in the user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('trainee.payments.create')
            ->with('success', 'Registration successful! Please proceed to make payment for your selected courses.')
            ->with('password_info', 'Your password is: ' . $password . ' (Please save this)');
    }

    /**
     * Handle Working-Class registration request.
     */
    public function registerWorkingClass(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'whatsapp_number' => 'required|string|max:20',
            'courses' => 'required|array|min:1|max:9',
            'courses.*' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create user account
        $user = User::create([
            'name' => strtoupper($request->full_name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'user_type' => 'working_class',
        ]);

        // Create trainee record immediately with course selections
        $trainee = Trainee::create([
            'user_id' => $user->id,
            'surname' => strtoupper($request->full_name), // Use full name as surname
            'first_name' => '',
            'middle_name' => null,
            'gender' => 'M', // Default
            'phone_number' => $request->whatsapp_number,
            'whatsapp_number' => $request->whatsapp_number,
            'username' => self::generateUsername(),
            'password' => $request->password, // Store plain text for backward compatibility
            'status' => 'Inactive', // Will be activated when payment is made
            'user_type' => 'working_class',
        ]);

        // Store selected courses in session for payment page
        $request->session()->put('selected_courses', $request->courses);
        $request->session()->put('user_type', 'working_class');
        $request->session()->put('registration_data', [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        // Log in the user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('trainee.payments.create')
            ->with('success', 'Registration successful! Please proceed to make payment for your selected courses.');
    }

    /**
     * Generate a unique username (format: BCD/XXXXXX)
     * Used when creating trainee record from user
     */
    public static function generateUsername()
    {
        $lastTrainee = Trainee::orderBy('id', 'desc')->first();
        $nextNumber = $lastTrainee ? (int) substr($lastTrainee->username, 4) + 1 : 1;
        $username = 'BCD/' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (Trainee::where('username', $username)->exists()) {
            $nextNumber++;
            $username = 'BCD/' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        }

        return $username;
    }
}

