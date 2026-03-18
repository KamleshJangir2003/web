<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'entry_time',
        'exit_time',
        'in_time',
        'out_time',
        'early_checkout_minutes',
        'overtime_hours',
        'late_minutes',
        'status',
        'shift_status',
        'shift',
        'shift_id',
        'reason'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(ShiftType::class, 'shift_id');
    }
}
