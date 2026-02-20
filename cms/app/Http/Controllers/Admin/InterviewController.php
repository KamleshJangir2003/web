<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Lead;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    public function index()
    {
        $interviews = Interview::with('lead')
            ->where('result', '!=', 'Selected')
            ->where('status', '!=', 'Rescheduled')
            ->orderBy('interview_date', 'desc')
            ->paginate(10);
        return view('auth.admin.interviews.index', compact('interviews'));
    }

    public function create(Request $request)
    {
        $lead = null;
        $nextRound = null;
        
        if ($request->has('lead_id')) {
            $lead = Lead::findOrFail($request->lead_id);
            
            // If this is for next round, determine the round
            if ($request->has('next_round')) {
                if ($request->next_round == 'Manager') {
                    $nextRound = 'Manager';
                } else {
                    $lastInterview = Interview::where('lead_id', $lead->id)
                        ->where('result', 'Selected')
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
                    if ($lastInterview) {
                        $nextRounds = [
                            'HR' => 'Manager',
                            'Manager' => 'Final'
                        ];
                        $nextRound = $nextRounds[$lastInterview->interview_round] ?? null;
                    }
                }
            }
        }
        
        $leads = Lead::all();
        
        // Get unique interviewers from database + default ones (excluding unwanted names)
        $defaultInterviewers = [
            
            ['name' => 'Harish (Team Leader)', 'email' => 'harish@thekwikster.com', 'phone' => '+91 7300077942'],
            ['name' => 'Vishwash Agarwal', 'email' => 'vishwashagarwal20@gmail.com', 'phone' => '+91 9119156553'],
            ['name' => 'Ajay Singh(Manager)', 'email' => 'manager@thekwikster.com', 'phone' => '+91 8619089315']
        ];
        
        $unwantedNames = ['amit', 'raj', 'neha', 'priya', 'vikash'];
        $dbInterviewers = Interview::distinct()
            ->select('interviewer', 'interviewer_email', 'interviewer_phone')
            ->whereNotNull('interviewer')
            ->get()
            ->filter(function($interview) use ($unwantedNames) {
                foreach ($unwantedNames as $unwanted) {
                    if (stripos($interview->interviewer, $unwanted) !== false) {
                        return false;
                    }
                }
                return true;
            })
            ->map(function($interview) {
                return [
                    'name' => $interview->interviewer,
                    'email' => $interview->interviewer_email,
                    'phone' => $interview->interviewer_phone
                ];
            })
            ->unique('name')
            ->values()
            ->toArray();
        
        $interviewers = array_merge($defaultInterviewers, $dbInterviewers);
        
        return view('auth.admin.interviews.create', compact('lead', 'leads', 'interviewers', 'nextRound'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'candidate_email' => 'required|email',
            'interview_round' => 'required|in:HR,Technical,Manager,Final',
            'interview_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'interviewer' => 'required|string',
            'interviewer_email' => 'required|email',
            'interviewer_phone' => 'required|string',
            'interview_mode' => 'required|in:Online,Offline',
            'meeting_platform' => 'required_if:interview_mode,Online|in:Google Meet,Zoom,Teams',
            'instructions' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($request->lead_id);
        
        $interview = Interview::create([
            'lead_id' => $request->lead_id,
            'candidate_name' => $lead->name,
            'candidate_email' => $request->candidate_email,
            'job_role' => $lead->role,
            'interview_round' => $request->interview_round,
            'interview_date' => $request->interview_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'interviewer' => $request->interviewer,
            'interviewer_email' => $request->interviewer_email,
            'interviewer_phone' => $request->interviewer_phone,
            'interview_mode' => $request->interview_mode,
            'meeting_platform' => $request->meeting_platform,
            'meeting_link' => $request->meeting_link,
            'instructions' => $request->instructions,
            'email_candidate' => $request->has('email_candidate'),
            'email_interviewer' => $request->has('email_interviewer'),
            'whatsapp_notification' => $request->has('whatsapp_notification'),
            'rescheduled_from' => $request->rescheduled_from,
            'reschedule_reason' => $request->reschedule_reason
        ]);

        // If rescheduling, mark old interview as rescheduled
        if ($request->rescheduled_from) {
            Interview::find($request->rescheduled_from)->update(['status' => 'Rescheduled']);
        }

        // Send notifications
        $this->sendNotifications($interview, $lead);
        
        // Log activity
        \App\Models\ActivityLog::log(
            'Scheduled Interview', 
            'Interview Management', 
            "Scheduled {$request->interview_round} interview for {$lead->name}"
        );

        return redirect()->route('admin.interviews.index')->with('success', 'Interview scheduled successfully!');
    }

    public function show(Interview $interview)
    {
        $interview->load('lead');
        return view('auth.admin.interviews.show', compact('interview'));
    }

    public function edit(Interview $interview)
    {
        $leads = Lead::all();
        
        // Get unique interviewers from database + default ones (excluding unwanted names)
        $defaultInterviewers = [
           
            ['name' => 'Harish (Team Leader)', 'email' => 'harish@thekwikster.com', 'phone' => '+91 7300077942'],
            ['name' => 'Vishwash Agarwal', 'email' => 'vishwashagarwal20@gmail.com', 'phone' => '+91 9119156553'],
            ['name' => 'Ajay Singh(Manager)', 'email' => 'manager@thekwikster.com', 'phone' => '+91 8619089315']
        ];
        
        $unwantedNames = ['harish', 'vishwash', 'ajay singh',];
        $dbInterviewers = Interview::distinct()
            ->select('interviewer', 'interviewer_email', 'interviewer_phone')
            ->whereNotNull('interviewer')
            ->get()
            ->filter(function($interview) use ($unwantedNames) {
                foreach ($unwantedNames as $unwanted) {
                    if (stripos($interview->interviewer, $unwanted) !== false) {
                        return false;
                    }
                }
                return true;
            })
            ->map(function($interview) {
                return [
                    'name' => $interview->interviewer,
                    'email' => $interview->interviewer_email,
                    'phone' => $interview->interviewer_phone
                ];
            })
            ->unique('name')
            ->values()
            ->toArray();
        
        $interviewers = array_merge($defaultInterviewers, $dbInterviewers);
        
        return view('auth.admin.interviews.edit', compact('interview', 'leads', 'interviewers'));
    }

    public function update(Request $request, Interview $interview)
    {
        $request->validate([
            'interview_round' => 'required|in:HR,Technical,Manager,Final',
            'interview_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'interviewer' => 'required|string',
            'interview_mode' => 'required|in:Online,Offline',
            'meeting_platform' => 'required_if:interview_mode,Online|in:Google Meet,Zoom,Teams',
            'instructions' => 'nullable|string',
        ]);

        $interview->update([
            'interview_round' => $request->interview_round,
            'interview_date' => $request->interview_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'interviewer' => $request->interviewer,
            'interview_mode' => $request->interview_mode,
            'meeting_platform' => $request->meeting_platform,
            'meeting_link' => $request->meeting_link,
            'instructions' => $request->instructions,
            'email_candidate' => $request->has('email_candidate'),
            'email_interviewer' => $request->has('email_interviewer'),
            'whatsapp_notification' => $request->has('whatsapp_notification'),
        ]);

        return redirect()->route('admin.interviews.index')->with('success', 'Interview updated successfully!');
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return redirect()->route('admin.interviews.index')->with('success', 'Interview deleted successfully!');
    }

    public function generateMeetingLink(Request $request)
    {
        $platform = $request->platform;
        
        if ($platform === 'Google Meet') {
            // Generate real Google Meet link
            $meetingId = $this->generateGoogleMeetLink();
            return response()->json(['link' => $meetingId]);
        }
        
        // For other platforms, generate dummy links
        $meetingId = uniqid();
        $links = [
            'Zoom' => "https://zoom.us/j/{$meetingId}",
            'Teams' => "https://teams.microsoft.com/l/meetup-join/{$meetingId}"
        ];
        
        return response()->json(['link' => $links[$platform] ?? '']);
    }

    private function generateGoogleMeetLink()
    {
        // Generate a random meeting ID similar to Google Meet format
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $meetingId = '';
        for ($i = 0; $i < 10; $i++) {
            $meetingId .= $chars[rand(0, 25)];
        }
        
        // Format: xxx-xxxx-xxx
        $formatted = substr($meetingId, 0, 3) . '-' . substr($meetingId, 3, 4) . '-' . substr($meetingId, 7, 3);
        return "https://meet.google.com/{$formatted}";
    }

    private function sendNotifications($interview, $lead)
    {
        // Send Email to Candidate
        if ($interview->email_candidate && $interview->candidate_email) {
            $this->sendEmailToCandidate($interview, $lead);
        }

        // Send Email to Interviewer
        if ($interview->email_interviewer) {
            $this->sendEmailToInterviewer($interview, $lead);
        }

        // Send WhatsApp Notification
        if ($interview->whatsapp_notification && $lead->number) {
            $this->sendWhatsAppNotification($interview, $lead);
        }
    }

    private function sendEmailToCandidate($interview, $lead)
    {
        try {
            Mail::send('emails.interview-candidate', compact('interview', 'lead'), function ($message) use ($interview) {
                $message->to($interview->candidate_email, $interview->candidate_name)
                        ->subject('Interview Scheduled - ' . $interview->job_role);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send email to candidate: ' . $e->getMessage());
        }
    }

    private function sendEmailToInterviewer($interview, $lead)
    {
        try {
            Mail::send('emails.interview-interviewer', compact('interview', 'lead'), function ($message) use ($interview, $lead) {
                $message->to($interview->interviewer_email)
                        ->subject('Interview Assigned - ' . $interview->candidate_name);
                
                // Attach resume if available - check multiple possible paths
                if ($lead->resume) {
                    $resumePaths = [
                        public_path('uploads/resumes/' . $lead->resume),
                        storage_path('app/public/resumes/' . $lead->resume),
                        storage_path('app/public/uploads/' . $lead->resume),
                        storage_path('app/public/' . $lead->resume),
                        public_path('storage/resumes/' . $lead->resume)
                    ];
                    
                    foreach ($resumePaths as $path) {
                        if (file_exists($path)) {
                            $message->attach($path, [
                                'as' => $interview->candidate_name . '_Resume.pdf',
                                'mime' => 'application/pdf',
                            ]);
                            \Log::info('Resume attached from path: ' . $path);
                            break;
                        }
                    }
                    
                    // Log if resume not found
                    if (!file_exists($resumePaths[0])) {
                        \Log::warning('Resume file not found for lead: ' . $lead->id . ', filename: ' . $lead->resume);
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send email to interviewer: ' . $e->getMessage());
        }
    }

    private function sendWhatsAppNotification($interview, $lead)
    {
        try {
            $message = "🎯 *Interview Scheduled*\n\n";
            $message .= "📅 Date: " . date('d M Y', strtotime($interview->interview_date)) . "\n";
            $message .= "⏰ Time: {$interview->start_time} - {$interview->end_time}\n";
            $message .= "👤 Candidate: {$interview->candidate_name}\n";
            $message .= "💼 Role: {$interview->job_role}\n";
            $message .= "🔄 Round: {$interview->interview_round}\n";
            
            if ($interview->meeting_link) {
                $message .= "🔗 Meeting Link: {$interview->meeting_link}\n";
            }
            
            if ($interview->instructions) {
                $message .= "📝 Instructions: {$interview->instructions}\n";
            }
            
            // Send to candidate
            if ($lead->number) {
                $candidateMessage = $message . "\nGood luck! 🍀";
                $this->sendWhatsAppMessage($lead->number, $candidateMessage);
            }
            
            // Send to interviewer
            if ($interview->interviewer_phone) {
                $interviewerMessage = $message . "\nPlease review the candidate's resume attached in email. 📄";
                $cleanPhone = preg_replace('/[^0-9]/', '', $interview->interviewer_phone);
                $this->sendWhatsAppMessage($cleanPhone, $interviewerMessage);
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp notification: ' . $e->getMessage());
        }
    }

    private function sendWhatsAppMessage($phoneNumber, $message)
    {
        // Example using a WhatsApp API service
        // Replace with your actual WhatsApp API credentials
        try {
            Http::post('https://api.whatsapp.com/send', [
                'phone' => '91' . $phoneNumber,
                'text' => $message,
                'apikey' => env('WHATSAPP_API_KEY', 'your-api-key')
            ]);
        } catch (\Exception $e) {
            \Log::error('WhatsApp API error: ' . $e->getMessage());
        }
    }

    public function updateResult(Request $request, Interview $interview)
    {
        try {
            $request->validate([
                'result' => 'required|in:Selected,Rejected',
                'rejection_reason' => 'required_if:result,Rejected'
            ]);

            $interview->update([
                'result' => $request->result,
                'rejection_reason' => $request->rejection_reason,
                'status' => 'Completed'
            ]);
            
            // Log activity
            \App\Models\ActivityLog::log(
                'Updated Interview Result', 
                'Interview Management', 
                "Marked interview result as {$request->result} for {$interview->candidate_name}"
            );

            $lead = $interview->lead;
            
            if ($request->result == 'Rejected') {
                // Update lead status to rejected
                if ($lead) {
                    $lead->update([
                        'status' => 'Rejected',
                        'rejection_reason' => $request->rejection_reason,
                        'final_result' => 'Rejected'
                    ]);
                }
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Candidate rejected. Process completed.',
                    'final_status' => 'Rejected'
                ]);
            }
            
            // If selected, check round and proceed accordingly
            if ($request->result == 'Selected') {
                if ($interview->interview_round == 'HR') {
                    // HR round passed, schedule manager round
                    if ($lead) {
                        $lead->update(['status' => 'HR Selected']);
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'HR round passed. Ready for Manager round.',
                        'next_action' => 'schedule_manager_round'
                    ]);
                }
                
                if ($interview->interview_round == 'Manager') {
                    // Manager round completed - final decision
                    if ($lead) {
                        $lead->update([
                            'status' => 'Selected',
                            'final_result' => 'Selected'
                        ]);
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Candidate selected! Process completed.',
                        'final_status' => 'Selected'
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Result updated successfully']);
            
        } catch (\Exception $e) {
            \Log::error('Interview result update error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error updating result: ' . $e->getMessage()
            ], 500);
        }
    }

    public function makeOffer(Request $request, Interview $interview)
    {
        $request->validate([
            'current_ctc' => 'nullable|numeric',
            'expected_ctc' => 'nullable|numeric',
            'offered_ctc' => 'required|numeric'
        ]);

        $interview->update([
            'current_ctc' => $request->current_ctc,
            'expected_ctc' => $request->expected_ctc,
            'offered_ctc' => $request->offered_ctc,
            'offer_status' => 'Pending'
        ]);

        return response()->json(['success' => true]);
    }

    public function selectedEmployees()
    {
        // Get selected interviews
        $selectedInterviews = Interview::with('lead')
            ->where('result', 'Selected')
            ->where('welcome_letter_sent', false)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Get directly added employees (not from interviews)
        $directEmployees = Employee::where('user_type', 'employee')
            ->where('is_approved', true)
            ->whereNotIn('email', function($query) {
                $query->select('candidate_email')
                    ->from('interviews')
                    ->where('result', 'Selected');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('auth.admin.interviews.selected', compact('selectedInterviews', 'directEmployees'));
    }

    public function sendWelcomeLetter(Request $request, Interview $interview)
    {
        $request->validate([
            'joining_date' => 'required|date',
            'current_ctc' => 'required|numeric',
            'in_hand_salary' => 'required|numeric'
        ]);

        try {
            // Create employee record
            $employee = Employee::where('email', $interview->candidate_email)->first();
            
            if (!$employee) {
                $nameParts = explode(' ', trim($interview->candidate_name), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                $employee = Employee::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $interview->candidate_email,
                    'phone' => $interview->lead->number ?? null,
                    'department' => $interview->job_role,
                    'platform' => $interview->lead->platform ?? null,
                    'user_type' => 'employee',
                    'is_approved' => true,
                    'action_status' => 'selected',
                    'hired_status' => 'not_hired',
                    'password' => Hash::make('password123'),
                    'joining_date' => $request->joining_date,
                    'current_ctc' => $request->current_ctc,
                    'in_hand_salary' => $request->in_hand_salary,
                ]);
            } else {
                $employee->update([
                    'action_status' => 'selected',
                    'hired_status' => 'not_hired',
                    'joining_date' => $request->joining_date,
                    'current_ctc' => $request->current_ctc,
                    'in_hand_salary' => $request->in_hand_salary,
                ]);
            }

            // Send welcome email
            Mail::send('emails.welcome-letter', compact('employee', 'interview'), function ($message) use ($employee) {
                $message->to($employee->email, $employee->full_name)
                        ->subject('Welcome to The Kwikster - Joining Letter');
            });

            // Mark welcome letter as sent
            $interview->update([
                'welcome_letter_sent' => true,
                'joining_date' => $request->joining_date
            ]);
            
            Log::info('Welcome letter sent to: ' . $employee->email);
            
            return response()->json([
                'success' => true, 
                'message' => 'Welcome letter sent successfully!',
                'redirect' => route('admin.employees.documents.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Welcome letter error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error sending welcome letter: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeInterview(Interview $interview)
    {
        $interview->update(['status' => 'Completed']);
        return response()->json(['success' => true]);
    }

    public function saveReason(Request $request, Interview $interview)
    {
        $interview->update(['reason' => $request->reason]);
        return response()->json(['success' => true]);
    }

    public function reschedule(Interview $interview)
    {
        $lead = $interview->lead;
        $leads = Lead::all();
        $defaultInterviewers = [
            ['name' => 'Harish (Team Leader)', 'email' => 'harish@thekwikster.com', 'phone' => '+91 7300077942'],
            ['name' => 'Vishwash Agarwal', 'email' => 'vishwashagarwal20@gmail.com', 'phone' => '+91 9119156553'],
            ['name' => 'Ajay Singh(Manager)', 'email' => 'manager@thekwikster.com', 'phone' => '+91 8619089315']
        ];
        
        $interviewers = $defaultInterviewers;
        $nextRound = null;
        
        return view('auth.admin.interviews.create', compact('interview', 'lead', 'leads', 'interviewers', 'nextRound'));
    }

    /**
     * Create employee record from interview data after welcome letter is sent
     */
    private function createEmployeeFromInterview(Interview $interview, $joiningDate)
    {
        // Check if employee already exists
        $existingEmployee = Employee::where('email', $interview->candidate_email)->first();
        
        if ($existingEmployee) {
            // Update existing employee with joining date if not set
            if (!$existingEmployee->joining_date) {
                $existingEmployee->update(['joining_date' => $joiningDate]);
            }
            Log::info('Employee already exists: ' . $interview->candidate_email);
            return $existingEmployee;
        }

        // Split candidate name into first and last name
        $nameParts = explode(' ', trim($interview->candidate_name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Create employee record
        $employee = Employee::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $interview->candidate_email,
            'phone' => $interview->lead->number ?? null,
            'department' => $interview->job_role,
            'platform' => $interview->lead->platform ?? null,
            'user_type' => 'employee',
            'is_approved' => true, // Auto-approve since they passed interview
            'password' => Hash::make('password123'), // Default password
            'joining_date' => $joiningDate,
            // CTC and in-hand salary will be set from Selected Employees page
        ]);

        Log::info('Employee created from interview: ' . $employee->id . ' - ' . $employee->email);
        
        return $employee;
    }

    public function saveEmploymentDetails(Request $request, Interview $interview)
    {
        $request->validate([
            'joining_date' => 'required|date',
            'current_ctc' => 'required|numeric|min:0',
            'in_hand_salary' => 'required|numeric|min:0'
        ]);

        try {
            // Save directly to interview table
            $interview->update([
                'joining_date' => $request->joining_date,
                'current_ctc' => $request->current_ctc,
                'in_hand_salary' => $request->in_hand_salary,
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Employment details saved successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Employment details save error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error saving employment details: ' . $e->getMessage()
            ], 500);
        }
    }
}