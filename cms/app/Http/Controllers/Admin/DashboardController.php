<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ActivityLog;
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
                ->select('id', 'title', 'department', 'status')
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

        return view('auth.admin.dashboard', [
            'user' => Auth::user(),
            'stats' => $stats,
            'pendingUsers' => $pendingUsers,
            'todayBirthdays' => $todayBirthdays,
            'activeJobOpenings' => $activeJobOpenings,
            'todayCallbacks' => $todayCallbacks,
            'allEmployees' => $allEmployees,
            'recentLogs' => $recentLogs,
        ]);
    }

    private function getOptimizedStats()
    {
        // Single query for employee counts
        $employeeCounts = DB::table('employees')
            ->selectRaw('
                COUNT(CASE WHEN user_type = "employee" AND is_approved = 1 AND action_status = "selected" THEN 1 END) as totalEmployees,
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
                (SELECT COUNT(*) FROM interviews WHERE status = "Scheduled") as scheduledInterviews
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
        ];
    }
}
