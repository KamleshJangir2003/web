<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intern extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'email',
        'phone', 
        'company',
        'role',
        'platform',
        'resume',
        'status',
        'condition_status',
        'reason',
        'final_result',
        'rejection_reason',
        'internship_duration',
        'start_date',
        'end_date',
        'stipend',
        'total_paid',
        'pending_amount',
        'mentor_id',
        'course',
        'hr_id',
        'profile_details',
        'notes',
        'comment',
        'documents',
        'completion_date',
        'performance_rating',
        'completion_remarks',
        'certificate_path',
        'cancellation_date',
        'cancellation_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'dob' => 'date',
        'completion_date' => 'date',
        'cancellation_date' => 'date',
        'documents' => 'array'
    ];

    protected $attributes = [
        'status' => 'new',
        'condition_status' => '',
        'role' => 'Intern'
    ];

    public function interviews()
    {
        return $this->hasMany(InternInterview::class);
    }

    public function mentor()
    {
        return $this->belongsTo(Employee::class, 'mentor_id');
    }
    
    public function hr()
    {
        return $this->belongsTo(Employee::class, 'hr_id');
    }
    
    public function payments()
    {
        return $this->hasMany(InternPayment::class);
    }

    public function hasScheduledInterview()
    {
        return $this->interviews()->exists();
    }
}