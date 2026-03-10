<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('employee')->orderBy('created_at', 'desc')->get();
        return view('admin.leaves.index', compact('leaves'));
    }

    public function approve($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update(['status' => 'approved']);
        return back()->with('success', 'Leave approved successfully!');
    }

    public function reject(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update(['status' => 'rejected']);
        return back()->with('success', 'Leave rejected successfully!');
    }
}
