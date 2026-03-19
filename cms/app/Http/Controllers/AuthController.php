<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Show Registration Form
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Handle Registration
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:employees',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:admin,employee,client', // Add user_type validation
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Auto-approve admin users, others need approval
        $isApproved = $request->user_type == 'admin' ? true : false;

        // Create Employee
        $fullName = $request->full_name;
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
        
        $employee = Employee::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'is_approved' => $isApproved,
        ]);

        // Auto-login for admin, others need to wait for approval
        if ($isApproved) {
            Auth::login($employee);

            return $this->redirectToDashboard($employee->user_type);
        }

        return redirect()->route('login')
            ->with('success', 'Registration successful! '.
                   ($request->user_type == 'admin' ?
                    'Admin account created.' :
                    'Your account is pending admin approval.'));
    }

    // Show Login Form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Login - 2-Step OTP Authentication (Only for Admin)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required|in:admin,employee,client',
        ]);

        $employee = Employee::where('email', $request->email)
            ->where('user_type', $request->user_type)
            ->first();

        if (! $employee) {
            return back()->withErrors([
                'email' => 'No account found with these credentials for the selected user type.',
            ])->withInput($request->only('email', 'user_type', 'remember'));
        }

        if (! $employee->is_approved) {
            return back()->withErrors([
                'email' => 'Your account is pending admin approval. Please wait for approval.',
            ])->withInput($request->only('email', 'user_type', 'remember'));
        }

        if (! Hash::check($request->password, $employee->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email', 'user_type', 'remember'));
        }

        // OTP only for Admin users
        if ($request->user_type === 'admin') {
            // Generate and send OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $employee->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($employee->email)->send(new OtpMail($otp, $employee->full_name));

            // Store user ID and remember preference in session
            session([
                'otp_user_id' => $employee->id,
                'otp_email' => $employee->email,
                'remember_me' => $request->has('remember'),
            ]);

            return redirect()->route('otp.show')->with('success', 'OTP sent to your email.');
        }

        // Direct login for Employee and Client (no OTP)
        Auth::login($employee, $request->has('remember'));
        $request->session()->regenerate();
        $employee->update(['last_login' => now()]);

        return $this->redirectToDashboard($employee->user_type);
    }

    // Show OTP Verification Page
    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please login again.']);
        }
        return view('auth.verify-otp');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please login again.']);
        }

        $employee = Employee::find($userId);
        if (!$employee) {
            return back()->withErrors(['otp' => 'Invalid session.']);
        }

        if ($employee->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        if (now()->greaterThan($employee->otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        // Clear OTP
        $employee->update([
            'otp' => null,
            'otp_expires_at' => null,
            'last_login' => now(),
        ]);

        // Login user
        Auth::login($employee, session('remember_me', false));
        $request->session()->regenerate();

        // Clear OTP session data
        session()->forget(['otp_user_id', 'otp_email', 'remember_me']);

        return $this->redirectToDashboard($employee->user_type);
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please login again.']);
        }

        $employee = Employee::find($userId);
        if (!$employee) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid session.']);
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $employee->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($employee->email)->send(new OtpMail($otp, $employee->full_name));

        return back()->with('success', 'New OTP sent to your email.');
    }

    // Helper function to redirect to dashboard based on user type
    private function redirectToDashboard($userType)
    {
        switch ($userType) {
            case 'admin':
                return redirect()->intended('/admin/dashboard')
                    ->with('success', 'Welcome Admin!');

            case 'employee':
                return redirect()->intended('/employee/dashboard')
                    ->with('success', 'Welcome Employee!');

            case 'client':
                return redirect()->intended('/client/dashboard')
                    ->with('success', 'Welcome Client!');

            default:
                return redirect()->intended('/dashboard');
        }
    }

    // Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    // ADMIN ONLY: Approve pending users
    public function approveUser($id)
    {
        // Check if current user is admin
        if (! Auth::check() || Auth::user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $employee = Employee::findOrFail($id);
        $employee->is_approved = true;
        $employee->save();

        return back()->with('success', 'User approved successfully!');
    }

    // ADMIN ONLY: Get pending approvals
    public function getPendingApprovals()
    {
        if (! Auth::check() || Auth::user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $pendingUsers = Employee::where('is_approved', false)->get();

        return view('admin.pending-approvals', compact('pendingUsers'));
    }
}
