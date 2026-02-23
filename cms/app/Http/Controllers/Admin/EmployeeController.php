<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\EmailLog;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::where('user_type', '!=', 'admin')
        ->where('hired_status', 'hired')
        ->where('employee_status', 'active')
        ->whereNotNull('joining_date')
        ->whereRaw('DATE_ADD(joining_date, INTERVAL certification_period DAY) <= CURDATE()');
        // Combined role and platform filtering
        if ($request->has('role') && $request->role) {
            $query->where('user_type', $request->role);
        }
        
        if ($request->has('platform') && $request->platform) {
            $query->where('platform', $request->platform);
        }
        
        // If both role and platform are provided, filter by both
        if ($request->has('role') && $request->has('platform') && $request->role && $request->platform) {
            $query->where('user_type', $request->role)
                  ->where('platform', $request->platform);
        }
        
        $employees = $query->orderBy('first_name')->get();
        
        return view('auth.admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('auth.admin.employees.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'contact_number' => 'required|string|max:20',
            'guardian_number' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'shift' => 'required|string',
            'current_address' => 'required|string',
            'current_city' => 'required|string|max:255',
            'current_state' => 'required|string|max:255',
            'current_pincode' => 'required|string|max:10',
            'permanent_address' => 'required|string',
            'permanent_city' => 'required|string|max:255',
            'permanent_state' => 'required|string|max:255',
            'permanent_pincode' => 'required|string|max:10',
            'uan_number' => 'nullable|string|max:50',
            'esic_number' => 'nullable|string|max:50',
            'Account_Holder_Name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'bank_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $nameParts = explode(' ', $request->full_name, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            Employee::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->contact_number,
                'password' => Hash::make('password123'),
                'user_type' => 'employee',
                'department' => $request->designation,
                'is_approved' => true,
                'action_status' => null,
                'hired_status' => 'not_hired',
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'dob' => $request->dob,
                'contact_number' => $request->contact_number,
                'guardian_number' => $request->guardian_number,
                'gender' => $request->gender,
                'shift' => $request->shift,
                'address' => $request->current_address,
                'city' => $request->current_city,
                'state' => $request->current_state,
                'pincode' => $request->current_pincode,
                'permanent_address' => $request->permanent_address,
                'permanent_city' => $request->permanent_city,
                'permanent_state' => $request->permanent_state,
                'permanent_pincode' => $request->permanent_pincode,
                'uan_number' => $request->uan_number,
                'esic_number' => $request->esic_number,
                'account_holder_name' => $request->Account_Holder_Name,
                'bank_account_number' => $request->bank_account_number,
                'ifsc_code' => $request->ifsc_code,
                'bank_name' => $request->bank_name,
                'joining_date' => now(),
            ]);

            \App\Models\ActivityLog::log(
                'Created Employee', 
                'Employee Management', 
                'Created new employee: ' . $request->full_name
            );

            \App\Helpers\NotificationHelper::employeeAdded($request->full_name);

            return redirect()->route('admin.interviews.selected')->with('success', 'Employee created successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving employment details: ' . $e->getMessage())->withInput();
        }
    }

    public function allEmployees()
    {
        $employees = Employee::where('user_type', '!=', 'admin')
                        ->where('is_approved', 1)
                        ->where('action_status', 'selected')
                        ->where('hired_status', 'hired')
                        ->orderBy('first_name')
                        ->get();
        
        return view('auth.admin.employees.all-employees', compact('employees'));
    }

    public function sendBulkMail(Request $request)
    {
        $request->validate([
            'emails' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $emails = explode(',', $request->emails);
        $subject = $request->subject;
        $message = $request->message;

        try {
            // For testing - just return success without actually sending
            // Remove this block when mail is properly configured
            return redirect()->back()->with('success', 'Mail would be sent to ' . count($emails) . ' employees! (Mail sending disabled for testing)');
            
            // Uncomment below when mail is configured
            /*
            foreach ($emails as $email) {
                Mail::to(trim($email))->send(new BulkMail($subject, $message));
            }
            return redirect()->back()->with('success', 'Mail sent successfully to ' . count($emails) . ' employees!');
            */
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send mail: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('auth.admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $data = $request->except(['_token', '_method', 'selfie']);
        
        // Handle empty values
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                unset($data[$key]);
            }
        }
        
        $employee->update($data);
        
        if ($request->hasFile('selfie')) {
            $file = $request->file('selfie');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/selfies'), $filename);
            $employee->selfie = $filename;
            $employee->save();
        }

        \App\Models\ActivityLog::log(
            'Updated Employee', 
            'Employee Management', 
            'Updated employee: ' . $employee->first_name . ' ' . $employee->last_name
        );

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Log activity before deletion
        \App\Models\ActivityLog::log(
            'Deleted Employee', 
            'Employee Management', 
            'Deleted employee: ' . $employee->first_name . ' ' . $employee->last_name
        );
        
        $employee->delete();
        
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully!');
    }

    public function getEmployeesData()
    {
        $employees = Employee::where('user_type', '!=', 'admin')
                        ->where('action_status', 'selected')
                        ->where('hired_status', 'hired')
                        ->orderBy('first_name')
                        ->get();
        
        return response()->json(['employees' => $employees]);
    }

    public function showDetails($id)
    {
        $employee = Employee::findOrFail($id);
        return view('auth.admin.employees.employee-details', compact('employee'));
    }

    public function profiles(Request $request)
    {
       $query = Employee::where('user_type', '!=', 'admin')
        ->where('hired_status', 'hired')
        ->where('employee_status', 'active')
        ->whereNotNull('joining_date')
        ->whereRaw('DATE_ADD(joining_date, INTERVAL certification_period DAY) <= CURDATE()');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('department', 'LIKE', "%{$search}%")
                  ->orWhere('contact_number', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        $employees = $query->orderBy('first_name')->paginate(10);
        
        return view('auth.admin.employees.profiles', compact('employees'));
    }

    public function employeeList()
    {
        $employees = Employee::where('user_type', '!=', 'admin')
                        ->where('action_status', 'selected')
                        ->where('hired_status', 'hired')
                        ->orderBy('first_name')
                        ->get();
        
        return view('auth.admin.employee-list', compact('employees'));
    }

    public function employeeShifts()
    {
        $employees = Employee::where('user_type', '!=', 'admin')
                        ->where('action_status', 'selected')
                        ->where('hired_status', 'hired')
                        ->orderBy('first_name')
                        ->get();
        
        return view('auth.admin.employees.employee_shift', compact('employees'));
    }

    public function showProfile($id)
    {
        $employee = Employee::findOrFail($id);
        return view('auth.admin.employees.profile-show', compact('employee'));
    }

    public function updateProfile(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'contact_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'guardian_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50',
            'in_hand_salary' => 'nullable|numeric|min:0',
            'current_ctc' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee->update($request->only([
            'first_name', 'last_name', 'email', 'contact_number', 'phone', 'department',
            'father_name', 'mother_name', 'dob', 'gender', 'guardian_number',
            'address', 'city', 'state', 'pincode', 'bank_name', 'ifsc_code', 'bank_account_number',
            'in_hand_salary', 'current_ctc', 'joining_date'
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function quickAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'department' => 'nullable|string|max:255',
            'user_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            // Split full name into first and last name
            $nameParts = explode(' ', $request->full_name, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            $employee = Employee::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department ?? 'Training',
                'user_type' => $request->user_type,
                'password' => Hash::make('password123'),
                'is_approved' => true,
                'action_status' => null,
                'hired_status' => 'not_hired',
                'joining_date' => now(),
                'current_ctc' => 0,
                'in_hand_salary' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully!',
                'employee' => $employee
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating employee: ' . $e->getMessage()
            ]);
        }
    }

    public function updateDetails(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'joining_date' => 'required|date',
            'current_ctc' => 'required|numeric|min:0',
            'in_hand_salary' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $employee = Employee::findOrFail($id);
            $employee->update([
                'joining_date' => $request->joining_date,
                'current_ctc' => $request->current_ctc,
                'in_hand_salary' => $request->in_hand_salary,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee details updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating employee: ' . $e->getMessage()
            ]);
        }
    }

    public function checkDetails($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $detailsSaved = $employee->joining_date && $employee->current_ctc && $employee->in_hand_salary;
            
            return response()->json([
                'details_saved' => $detailsSaved
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'details_saved' => false
            ]);
        }
    }

    public function sendWelcome(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'joining_date' => 'required|date',
            'current_ctc' => 'required|numeric|min:0',
            'in_hand_salary' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $employee = Employee::findOrFail($id);
            
            // Update employee details
            $employee->update([
                'action_status' => 'selected',
                'hired_status' => 'not_hired',
                'joining_date' => $request->joining_date,
                'current_ctc' => $request->current_ctc,
                'in_hand_salary' => $request->in_hand_salary,
            ]);

            // Send welcome email
            Mail::send('emails.welcome-letter', compact('employee'), function ($message) use ($employee) {
                $message->to($employee->email, $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name)
                        ->subject('Welcome to The Kwikster - Joining Letter');
            });
            // ✅ Add this
EmailLog::create([
    'to_email' => $employee->email,
    'subject' => 'Welcome to The Kwikster - Joining Letter',
    'content' => view('emails.welcome-letter', compact('employee'))->render(),
    'sent_at' => now(),
    'status' => 'sent'
]);

            \Log::info('Welcome letter sent to: ' . $employee->email);

            return response()->json([
                'success' => true,
                'message' => 'Welcome letter sent successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Welcome letter error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending welcome letter: ' . $e->getMessage()
            ]);
        }
    }
}