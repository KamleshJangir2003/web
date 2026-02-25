<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterestedCandidate;
use App\Models\Lead;
use App\Models\Interview;
use App\Models\Employee;
use Illuminate\Http\Request;

class CandidateJourneyController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('search');
        
        // Get all candidates with their journey
        $journeys = collect();
        $processedEmails = [];
        $processedPhones = [];
        
        // Start from InterestedCandidates
        $interestedCandidates = InterestedCandidate::when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('number', 'like', "%{$query}%");
        })->latest()->get();
        
        foreach ($interestedCandidates as $candidate) {
            $processedEmails[] = $candidate->email;
            $processedPhones[] = $candidate->number;
            $journey = [
                'id' => 'IC-' . $candidate->id,
                'name' => $candidate->name,
                'email' => $candidate->email,
                'phone' => $candidate->number,
                'role' => $candidate->role,
                'platform' => $candidate->platform,
                'stages' => [],
                'current_stage' => 'interested',
                'final_status' => $candidate->status,
                'rejection_reason' => null,
                'created_at' => $candidate->created_at
            ];
            
            // Stage 1: Interested
            $journey['stages'][] = [
                'name' => 'Interested',
                'status' => 'completed',
                'date' => $candidate->interested_at ?? $candidate->created_at,
                'notes' => $candidate->notes
            ];
            
            // Check if converted to Lead
            $lead = Lead::where(function($q) use ($candidate) {
                if (!empty($candidate->email)) {
                    $q->where('email', $candidate->email);
                }
                if (!empty($candidate->number)) {
                    $q->orWhere('phone', $candidate->number)
                      ->orWhere('number', $candidate->number);
                }
            })->first();
            
            if ($lead) {
                $journey['current_stage'] = 'lead';
                
                // Check if lead is rejected
                $isLeadRejected = ($lead->final_result === 'rejected' || $lead->status === 'rejected' || $lead->condition_status === 'Rejected');
                
                $journey['stages'][] = [
                    'name' => 'Lead Created',
                    'status' => $isLeadRejected ? 'rejected' : 'completed',
                    'date' => $lead->created_at,
                    'notes' => $isLeadRejected ? ($lead->rejection_reason ?? $lead->reason ?? 'Rejected at lead stage') : "Status: {$lead->status}"
                ];
                
                // If lead rejected, set final status and skip further stages
                if ($isLeadRejected) {
                    $journey['rejection_reason'] = $lead->rejection_reason ?? $lead->reason;
                    $journey['final_status'] = 'rejected';
                }
                
                // Check interviews
                $interviews = $lead->interviews;
                if ($interviews->count() > 0) {
                    $journey['current_stage'] = 'interview';
                    
                    foreach ($interviews as $interview) {
                        $interviewStatus = $interview->result === 'Reject' ? 'reject' : ($interview->result === 'Selected' ? 'completed' : $interview->status);
                        $rejectionReason = $interview->rejection_reason ?? $interview->reason ?? null;
                        
                        $journey['stages'][] = [
                            'name' => "Interview - Round {$interview->interview_round}",
                            'status' => $interviewStatus,
                            'date' => $interview->interview_date,
                            'result' => $interview->result,
                            'rejection_reason' => $rejectionReason,
                            'notes' => "Mode: {$interview->interview_mode}, Interviewer: {$interview->interviewer}"
                        ];
                        
                        // Check for rejection - case insensitive and multiple variations
                        $resultLower = strtolower(trim($interview->result ?? ''));
                        if (in_array($resultLower, ['reject', 'rejected', 'not selected'])) {
                            $journey['rejection_reason'] = $rejectionReason;
                            $journey['final_status'] = 'interview_reject';
                        }
                    }
                }
                
                // Check if hired (only if not rejected)
                if (!in_array($journey['final_status'], ['rejected', 'interview_reject'])) {
                    $employee = Employee::where(function($q) use ($candidate) {
                        $q->where('email', $candidate->email)
                          ->orWhere('phone', $candidate->number);
                    })->first();
                    
                    if ($employee) {
                        // Check if employee was not selected after hiring
                        if ($employee->action_status === 'not_selected') {
                            $journey['current_stage'] = 'not_selected';
                            $journey['final_status'] = 'rejected';
                            $journey['rejection_reason'] = $employee->action_reason ?? 'Not selected after certification period';
                            $journey['stages'][] = [
                                'name' => 'Not Selected',
                                'status' => 'rejected',
                                'date' => $employee->updated_at,
                                'notes' => $employee->action_reason ?? 'Not selected after certification period'
                            ];
                        } else {
                            $journey['current_stage'] = 'hired';
                            $journey['final_status'] = 'hired';
                            $journey['stages'][] = [
                                'name' => 'Hired',
                                'status' => 'completed',
                                'date' => $employee->created_at,
                                'notes' => "Employee ID: {$employee->employee_id}, Status: {$employee->employee_status}"
                            ];
                        }
                    }
                }
            }
            
            $journeys->push($journey);
        }
        
        // Get ALL Leads (including those with status changes)
        $allLeads = Lead::when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%")
              ->orWhere('number', 'like', "%{$query}%");
        })->where(function($q) use ($processedEmails, $processedPhones) {
            $q->whereNotIn('email', array_filter($processedEmails))
              ->whereNotIn('number', array_filter($processedPhones));
        })->latest()->get();
        
        foreach ($allLeads as $lead) {
            $journey = [
                'id' => 'L-' . $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone ?? $lead->number,
                'role' => $lead->role ?? 'Unknown',
                'platform' => $lead->platform ?? 'Direct',
                'stages' => [],
                'current_stage' => 'lead',
                'final_status' => 'active',
                'rejection_reason' => null,
                'created_at' => $lead->created_at
            ];
            
            // Determine lead status based on condition_status
            $conditionStatus = $lead->condition_status;
            $isLeadRejected = ($lead->final_result === 'rejected' || $lead->status === 'rejected' || $conditionStatus === 'Rejected');
            $isNotInterested = ($conditionStatus === 'Not Interested');
            $isWrongNumber = ($conditionStatus === 'Wrong Number');
            
            // Add Lead stage with appropriate status
            $stageStatus = 'completed';
            $stageNotes = "Status: {$conditionStatus}";
            
            if ($isLeadRejected) {
                $stageStatus = 'rejected';
                $stageNotes = $lead->rejection_reason ?? $lead->reason ?? 'Rejected';
                $journey['final_status'] = 'rejected';
                $journey['rejection_reason'] = $lead->rejection_reason ?? $lead->reason;
            } elseif ($isNotInterested) {
                $stageStatus = 'rejected';
                $stageNotes = $lead->reason ?? 'Not Interested';
                $journey['final_status'] = 'rejected';
                $journey['rejection_reason'] = $lead->reason;
            } elseif ($isWrongNumber) {
                $stageStatus = 'rejected';
                $stageNotes = $lead->reason ?? 'Wrong Number';
                $journey['final_status'] = 'rejected';
                $journey['rejection_reason'] = $lead->reason;
            }
            
            $journey['stages'][] = [
                'name' => 'Lead',
                'status' => $stageStatus,
                'date' => $lead->created_at,
                'notes' => $stageNotes
            ];
            
            // Check interviews
            $interviews = $lead->interviews;
            if ($interviews->count() > 0 && !$isLeadRejected) {
                $journey['current_stage'] = 'interview';
                
                foreach ($interviews as $interview) {
                    $interviewStatus = $interview->result === 'Reject' ? 'reject' : ($interview->result === 'Selected' ? 'completed' : $interview->status);
                    $rejectionReason = $interview->rejection_reason ?? $interview->reason ?? null;
                    
                    $journey['stages'][] = [
                        'name' => "Interview - Round {$interview->interview_round}",
                        'status' => $interviewStatus,
                        'date' => $interview->interview_date,
                        'result' => $interview->result,
                        'rejection_reason' => $rejectionReason,
                        'notes' => "Mode: {$interview->interview_mode}, Interviewer: {$interview->interviewer}"
                    ];
                    
                    // Check for rejection - case insensitive and multiple variations
                    $resultLower = strtolower(trim($interview->result ?? ''));
                    if (in_array($resultLower, ['reject', 'rejected', 'not selected'])) {
                        $journey['rejection_reason'] = $rejectionReason;
                        $journey['final_status'] = 'interview_reject';
                    }
                }
            }
            
            // Check if hired
            if (!in_array($journey['final_status'], ['rejected', 'interview_reject'])) {
                $employee = Employee::where(function($q) use ($lead) {
                    $q->where('email', $lead->email)
                      ->orWhere('phone', $lead->phone)
                      ->orWhere('phone', $lead->number);
                })->first();
                
                if ($employee) {
                    // Check if employee was not selected after hiring
                    if ($employee->action_status === 'not_selected') {
                        $journey['current_stage'] = 'not_selected';
                        $journey['final_status'] = 'rejected';
                        $journey['rejection_reason'] = $employee->action_reason ?? 'Not selected after certification period';
                        $journey['stages'][] = [
                            'name' => 'Not Selected',
                            'status' => 'rejected',
                            'date' => $employee->updated_at,
                            'notes' => $employee->action_reason ?? 'Not selected after certification period'
                        ];
                    } else {
                        $journey['current_stage'] = 'hired';
                        $journey['final_status'] = 'hired';
                        $journey['stages'][] = [
                            'name' => 'Hired',
                            'status' => 'completed',
                            'date' => $employee->created_at,
                            'notes' => "Employee ID: {$employee->employee_id}"
                        ];
                    }
                }
            }
            
            $journeys->push($journey);
        }
        
        return view('auth.admin.candidate-journey.index', compact('journeys', 'query'));
    }
    
    public function show($id)
    {
        // Extract type and ID
        $parts = explode('-', $id);
        $type = $parts[0];
        $actualId = $parts[1];
        
        $journey = null;
        
        if ($type === 'IC') {
            $candidate = InterestedCandidate::findOrFail($actualId);
            // Build detailed journey (similar to index but more detailed)
            $journey = $this->buildDetailedJourney($candidate);
        }
        
        return view('auth.admin.candidate-journey.show', compact('journey'));
    }
    
    private function buildDetailedJourney($candidate)
    {
        // Detailed journey building logic
        return [
            'name' => $candidate->name,
            'email' => $candidate->email,
            'phone' => $candidate->number,
            'timeline' => []
        ];
    }
}
