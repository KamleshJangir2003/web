<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Auth;

class LetterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all letters sent to this employee
        $letters = EmailLog::where('to_email', $user->email)
            ->orderBy('sent_at', 'desc')
            ->get();
        
        return view('employee.letters.index', compact('letters', 'user'));
    }
}
