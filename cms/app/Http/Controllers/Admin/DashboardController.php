<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\Callback;
use App\Models\InterestedCandidate;
use App\Models\Interview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache stats for 5 minutes
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return $this->getOptimizedStats();
        });

        // Get pending users with minimal data
        $pendingUsers = Employee::where('is_approved', false)
            ->select('id', 'first_name', 'last_name', 'email', 'department', 'user_type')
            ->limit(10)
            ->get();

        // Birthday check - cached for 1 hour
        $todayBirthdays = Cache::remember('today_birthdays', 3600, function () {
            return Employee::whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [date('m-d')])
                ->whereNotNull('dob')
                ->select('id', 'first_name', 'last_name', 'dob')
                ->get();
        });

        // Today's callbacks - cached for 10 minutes
        $todayCallbacks = Cache::remember('today_callbacks', 600, function () {
            return DB::table('callbacks')
                ->whereDate('callback_date', date('Y-m-d'))
                ->where('status', 'call_backs')
                ->limit(10)
                ->get();
        });

        // Active job openings - cached for 30 minutes
        $activeJobOpenings = Cache::remember('active_job_openings', 1800, function () {
            return \App\Models\JobOpening::where('status', 'active')
                ->select('id', 'job_title', 'shift', 'salary', 'status')
                ->get();
        });

        // Paginated employees - only load 50 at a time
        $allEmployees = Employee::where('user_type', 'employee')
            ->where('is_approved', true)
            ->where('action_status', 'selected')
            ->where('hired_status', 'hired')
            ->where('employee_status', 'active')
            ->select('id', 'first_name', 'last_name', 'email', 'phone', 'department', 'platform')
            ->orderBy('first_name')
            ->limit(50)
            ->get();

        // Recent logs - only 5
        $recentLogs = ActivityLog::orderBy('created_at', 'desc')
            ->select('user_name', 'action', 'module', 'created_at')
            ->limit(5)
            ->get();

        // Dashboard data - exactly as shown on respective pages
        $leads = Lead::whereDoesntHave('interviews')
            ->where(function($q) {
                $q->whereNull('condition_status')
                  ->orWhere('condition_status', '');
            })
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            
        $callbacks = Callback::where('status', 'call_backs')
            ->orderBy('callback_date', 'desc')
            ->limit(10)
            ->get();
            
        $interestedCandidates = Lead::where('condition_status', 'Interested')
            ->whereDoesntHave('interviews')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
            
        $interviews = Interview::with('lead')
            ->where('result', '!=', 'Selected')
            ->where('result', '!=', 'Rejected')
            ->where('status', '!=', 'Rescheduled')
            ->orderBy('interview_date', 'desc')
            ->limit(10)
            ->get();
            
        $selectedInterviews = Interview::with('lead')
            ->where('result', 'Selected')
            ->where('welcome_letter_sent', false)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
            
        $employeesWithDocuments = Employee::where('user_type', 'employee')
            ->where('is_approved', true)
            ->where('hired_status', 'not_hired')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $hiredEmployees = Employee::where('user_type', '!=', 'admin')
            ->where('hired_status', 'hired')
            ->where('employee_status', 'active')
            ->whereNotNull('joining_date')
            ->whereRaw('DATE_ADD(joining_date, INTERVAL certification_period DAY) <= CURDATE()')
            ->orderBy('first_name')
            ->limit(10)
            ->get();

        return view('auth.admin.dashboard', [
            'user' => Auth::user(),
            'stats' => $stats,
            'pendingUsers' => $pendingUsers,
            'todayBirthdays' => $todayBirthdays,
            'activeJobOpenings' => $activeJobOpenings,
            'todayCallbacks' => $todayCallbacks,
            'allEmployees' => $allEmployees,
            'recentLogs' => $recentLogs,
            'leads' => $leads,
            'callbacks' => $callbacks,
            'interestedCandidates' => $interestedCandidates,
            'interviews' => $interviews,
            'selectedInterviews' => $selectedInterviews,
            'employeesWithDocuments' => $employeesWithDocuments,
            'hiredEmployees' => $hiredEmployees,
        ]);
    }

    private function getPayrollMonthDates()
    {
        $today = date('d');
        if ($today >= 23) {
            $startDate = date('Y-m-23');
            $endDate = date('Y-m-22', strtotime('+1 month'));
        } else {
            $startDate = date('Y-m-23', strtotime('-1 month'));
            $endDate = date('Y-m-22');
        }
        return ['start' => $startDate, 'end' => $endDate];
    }

    private function getOptimizedStats()
    {
        // Single query for employee counts
        $employeeCounts = DB::table('employees')
            ->selectRaw('
              COUNT(
    CASE 
        WHEN user_type = "employee"
        AND is_approved = 1
        AND hired_status = "hired"
        AND employee_status = "active"
        THEN 1 
    END
) as totalEmployees,
                COUNT(CASE WHEN is_approved = 0 THEN 1 END) as pendingApprovals,
                COUNT(CASE WHEN user_type = "admin" THEN 1 END) as totalAdmins,
                COUNT(CASE WHEN user_type = "client" THEN 1 END) as totalClients,
                COUNT(CASE WHEN user_type = "employee" AND is_approved = 1 AND action_status = "selected" AND LOWER(gender) = "male" THEN 1 END) as maleCount,
                COUNT(CASE WHEN user_type = "employee" AND is_approved = 1 AND action_status = "selected" AND LOWER(gender) = "female" THEN 1 END) as femaleCount
            ')
            ->first();

        // Single query for other counts
        $otherCounts = DB::selectOne('
            SELECT 
                (SELECT COUNT(*) FROM leads) as totalLeads,
                (SELECT COUNT(*) FROM callbacks WHERE status = "call_backs") as totalCallbacks,
                (SELECT COUNT(*) FROM interviews) as totalInterviews,
                (SELECT COUNT(*) FROM interviews WHERE result = "Rejected") as rejectedInterviews,
                (SELECT COUNT(*) FROM interviews WHERE result = "Selected") as selectedEmployee,
                (SELECT COUNT(*) FROM interviews WHERE status = "Scheduled") as scheduledInterviews,
                (SELECT COUNT(*) FROM interns WHERE LOWER(TRIM(final_result)) = "ongoing") as activeInterns,
                (SELECT COALESCE(SUM(stipend), 0) FROM interns WHERE final_result IN ("Ongoing", "Selected", "Completed")) as totalInternPayment,
                (SELECT COALESCE(SUM(ip.amount), 0) FROM intern_payments ip INNER JOIN interns i ON ip.intern_id = i.id WHERE i.final_result IN ("Ongoing", "Selected", "Completed")) as receivedInternPayment,
                (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE employee_id IS NOT NULL) as totalReimbursement,
                (SELECT COALESCE(SUM(amount), 0) FROM admin_expenses) as totalExpenses,
                (SELECT COALESCE(SUM(in_hand_salary), 0) FROM employees WHERE user_type = "employee" AND is_approved = 1 AND hired_status = "hired" AND employee_status = "active") as totalEmployeeSalary
        ');

        $ticketCounts = DB::table('tickets')
            ->selectRaw('
                COUNT(CASE WHEN viewed_at IS NULL THEN 1 END) as newTickets,
                COUNT(*) as totalTickets
            ')
            ->first();

        $interestedCount = DB::table('leads')
            ->where('condition_status', 'Interested')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('interviews')
                    ->whereColumn('interviews.lead_id', 'leads.id');
            })
            ->count();

        $totalHired = $employeeCounts->totalEmployees;
        $malePercentage = $totalHired > 0 ? round(($employeeCounts->maleCount / $totalHired) * 100) : 0;
        $femalePercentage = $totalHired > 0 ? round(($employeeCounts->femaleCount / $totalHired) * 100) : 0;

        return [
            'totalEmployees' => $employeeCounts->totalEmployees,
            'pendingApprovals' => $employeeCounts->pendingApprovals,
            'totalAdmins' => $employeeCounts->totalAdmins,
            'totalClients' => $employeeCounts->totalClients,
            'totalLeads' => $otherCounts->totalLeads,
            'totalCallbacks' => $otherCounts->totalCallbacks,
            'totalInterviews' => $otherCounts->totalInterviews,
            'rejectedInterviews' => $otherCounts->rejectedInterviews,
            'newTickets' => $ticketCounts->newTickets,
            'totalTickets' => $ticketCounts->totalTickets,
            'interested' => $interestedCount,
            'scheduledInterviews' => $otherCounts->scheduledInterviews,
            'employeeHired' => $totalHired,
            'selectedEmployee' => $otherCounts->selectedEmployee,
            'totalHiredEmployees' => $totalHired,
            'maleEmployees' => $employeeCounts->maleCount,
            'femaleEmployees' => $employeeCounts->femaleCount,
            'malePercentage' => $malePercentage,
            'femalePercentage' => $femalePercentage,
            'activeInterns' => $otherCounts->activeInterns,
            'totalInternPayment' => $otherCounts->totalInternPayment,
            'receivedInternPayment' => $otherCounts->receivedInternPayment,
            'totalReimbursement' => $otherCounts->totalReimbursement,
            'totalExpenses' => $otherCounts->totalExpenses,
            'totalEmployeeSalary' => $otherCounts->totalEmployeeSalary,
        ];
    }
}
