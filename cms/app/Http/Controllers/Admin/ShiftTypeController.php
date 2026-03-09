<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftType;
use Illuminate\Http\Request;

class ShiftTypeController extends Controller
{
    public function index()
    {
        $shifts = ShiftType::all();
        return view('admin.shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_after' => 'required|integer|min:0'
        ]);

        ShiftType::create($request->all());

        return redirect()->back()->with('success', 'Shift created successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_after' => 'required|integer|min:0'
        ]);

        $shift = ShiftType::findOrFail($id);
        $shift->update($request->all());

        return redirect()->back()->with('success', 'Shift updated successfully');
    }

    public function destroy($id)
    {
        ShiftType::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Shift deleted successfully');
    }
}
