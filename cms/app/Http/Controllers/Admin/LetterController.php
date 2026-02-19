<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;

class LetterController extends Controller
{
    public function index()
    {
        // Group letters by employee email
        $letters = EmailLog::orderBy('sent_at', 'desc')->get();
        $groupedLetters = $letters->groupBy('to_email');
        
        return view('auth.admin.letters.index', compact('groupedLetters'));
    }
}
