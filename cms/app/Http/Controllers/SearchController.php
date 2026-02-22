<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        try {
            $results = [];
            
            // Employees
            $employees = Employee::where('first_name', 'LIKE', "%{$query}%")
                ->orWhere('last_name', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->orWhere('contact_number', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();
                
            foreach($employees as $emp) {
                // Determine page name based on status
                $pageName = match($emp->hired_status) {
                    'hired' => 'Hired Employees',
                    'rejected' => 'Rejected Employees',
                    'pending' => 'Pending Employees',
                    default => 'All Employees'
                };
                
                // Always go to detail page
                $url = '/admin/employees/' . $emp->id . '/details';
                
                $results[] = [
                    'id' => $emp->id,
                    'name' => trim($emp->first_name . ' ' . $emp->last_name),
                    'number' => $emp->phone ?: $emp->contact_number,
                    'type' => 'Employee',
                    'page' => $pageName,
                    'url' => $url,
                    'status' => $emp->hired_status ?? 'pending',
                    'role' => $emp->role ?? 'N/A',
                    'platform' => $emp->platform ?? 'N/A'
                ];
            }
            
            // Leads
            $leads = DB::table('leads')
                ->where('name', 'LIKE', "%{$query}%")
                ->orWhere('number', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();
                
            foreach($leads as $lead) {
                // Determine page name and URL based on status
                $statusData = match($lead->condition_status) {
                    'Rejected' => ['page' => 'Rejected Leads', 'url' => '/admin/leads/rejected'],
                    'Interested' => ['page' => 'Interested Leads', 'url' => '/admin/leads/interested'],
                    'Not Interested' => ['page' => 'Not Interested Leads', 'url' => '/admin/leads/not-interested'],
                    'Wrong Number' => ['page' => 'Wrong Number Leads', 'url' => '/admin/leads/wrong-number'],
                    'Callback' => ['page' => 'Callback Leads', 'url' => '/admin/callbacks'],
                    default => ['page' => 'All Leads', 'url' => '/admin/leads']
                };
                
                $results[] = [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'number' => $lead->number,
                    'type' => 'Lead',
                    'page' => $statusData['page'],
                    'url' => $statusData['url'],
                    'status' => $lead->condition_status ?? 'pending',
                    'role' => $lead->role ?? 'N/A',
                    'platform' => $lead->platform ?? 'N/A'
                ];
            }
            
            // Callbacks
            $callbacks = DB::table('callbacks')
                ->where('name', 'LIKE', "%{$query}%")
                ->orWhere('number', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
                
            foreach($callbacks as $callback) {
                $results[] = [
                    'id' => $callback->id,
                    'name' => $callback->name,
                    'number' => $callback->number,
                    'type' => 'Callback',
                    'page' => 'Callbacks',
                    'url' => '/admin/callbacks',
                    'status' => $callback->status ?? 'pending',
                    'role' => $callback->role ?? 'N/A',
                    'platform' => $callback->platform ?? 'N/A'
                ];
            }
            
            // Interviews
            $interviews = DB::table('interviews')
                ->where('candidate_name', 'LIKE', "%{$query}%")
                ->orWhere('candidate_email', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
                
            foreach($interviews as $interview) {
                // Determine page name based on result
                $pageName = match($interview->result) {
                    'Selected' => 'Selected Interviews',
                    'Rejected' => 'Rejected Interviews',
                    'Pending' => 'Pending Interviews',
                    default => 'All Interviews'
                };
                
                $url = '/admin/interviews';
                
                $results[] = [
                    'id' => $interview->id,
                    'name' => $interview->candidate_name,
                    'number' => $interview->candidate_email,
                    'type' => 'Interview',
                    'page' => $pageName,
                    'url' => $url,
                    'status' => $interview->result ?? 'pending',
                    'role' => $interview->position ?? 'N/A',
                    'platform' => 'N/A'
                ];
            }
            
            return response()->json($results);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}