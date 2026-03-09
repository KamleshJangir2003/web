<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftType extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_name',
        'start_time',
        'end_time',
        'late_after'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'shift_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'shift_id');
    }
}
