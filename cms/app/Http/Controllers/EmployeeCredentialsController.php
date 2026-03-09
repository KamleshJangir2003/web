<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EmployeeCredentialsController extends Controller
{
    public function index()
    {
        $employees = Employee::where('user_type', 'employee')
                    ->where('hired_status', 'hired')
                    ->get();
    
        return view('employee-credentials', compact('employees'));
    }

    public function generatePassword($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Generate random password
        $password = Str::random(8);
        
        // Update employee record
        $employee->update([
            'password' => Hash::make($password),
            'temp_password' => $password,
            'password_generated' => true
        ]);

        return redirect()->back()->with('success', 'Password generated for ' . $employee->first_name . ': ' . $password);
    }
}