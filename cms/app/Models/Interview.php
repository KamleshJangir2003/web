<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'candidate_name',
        'candidate_email',
        'job_role',
        'interview_round',
        'interview_date',
        'start_time',
        'end_time',
        'interviewer',
        'interviewer_email',
        'interviewer_phone',
        'interview_mode',
        'meeting_platform',
        'meeting_link',
        'instructions',
        'interviewer_notes',
        'email_candidate',
        'email_interviewer',
        'whatsapp_notification',
        'status',
        'result',
        'rejection_reason',
        'current_ctc',
        'expected_ctc',
        'offered_ctc',
        'offer_status',
        'welcome_letter_sent',
        'joining_date',
        'in_hand_salary',
        'rescheduled_from',
        'reschedule_reason',
        'reason'
    ];

    protected $casts = [
        'interview_date' => 'date',
        'email_candidate' => 'boolean',
        'email_interviewer' => 'boolean',
        'whatsapp_notification' => 'boolean',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}