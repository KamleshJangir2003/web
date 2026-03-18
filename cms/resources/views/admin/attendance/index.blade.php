@extends('auth.layouts.app')
<style>
    /* ===== FIX CONTENT SHIFT WITH SIDEBAR ===== */

.container-fluid {
    padding-left: 130px !important;

    
}

/* Agar sidebar fixed width 250px hai */
.main-content {
    margin-left: 250px;
    transition: all 0.3s ease;
}

/* Card full width */
.card {
    width: 100%;
}

/* Prevent overflow */
body {
    overflow-x: hidden;
}

</style>
@section('content')
<div class="container-fluid ">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance Management System</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('salary_generated'))
                        @php $salaryData = session('salary_generated'); @endphp
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            <strong>Salary Auto-Generated!</strong> 
                            Salary for {{ $salaryData['count'] }} employees has been automatically generated for {{ $salaryData['month_name'] }} {{ $salaryData['year'] }}.
                            <a href="{{ route('admin.salary.index', ['month' => $salaryData['month'], 'year' => $salaryData['year']]) }}" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-eye me-1"></i>View Salary Records
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-2">
                            <label class="form-label">View Type</label>
                            <select name="view_type" class="form-select" onchange="this.form.submit()">
                                <option value="daily" {{ ($view_type ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ ($view_type ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ ($view_type ?? '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>
                        
                        @if(($view_type ?? 'daily') === 'daily')
                        <div class="col-md-2">
                            <label class="form-label">Shift</label>
                            <select name="shift" class="form-select" onchange="this.form.submit()">
                                <option value="Day" {{ ($selected_shift ?? 'Day') === 'Day' ? 'selected' : '' }}>Day Shift (IST)</option>
                                <option value="Night" {{ ($selected_shift ?? '') === 'Night' ? 'selected' : '' }}>Night Shift (US)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $selected_date }}" onchange="this.form.submit()">
                        </div>
                        @elseif($view_type === 'weekly')
                        <div class="col-md-2">
                            <label class="form-label">Week</label>
                            <input type="week" name="week" class="form-control" value="{{ $selected_week }}" onchange="this.form.submit()">
                        </div>
                        @elseif($view_type === 'monthly')
                        <div class="col-md-2">
                            <label class="form-label">Month</label>
                            <input type="month" name="month" class="form-control" value="{{ $selected_month }}" onchange="this.form.submit()">
                        </div>
                        @endif
                        
                        <div class="col-md-2">
                            <label class="form-label">Department</label>
                            <select name="department" class="form-select" onchange="this.form.submit()">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ $department_filter === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search Employee</label>
                            <input type="text" name="search" class="form-control" placeholder="Name or Email" value="{{ $search_employee }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100"><i class="fas fa-search me-1"></i>Search</button>
                        </div>
                    </form>

                    <!-- Attendance Form -->
                    @if(($view_type ?? 'daily') === 'daily')
                    <form method="POST" action="{{ route('admin.attendance.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $selected_date }}">
                        <input type="hidden" name="shift" value="{{ $selected_shift ?? 'Day' }}">
                        
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>{{ ($selected_shift ?? 'Day') === 'Day' ? 'Day Shift (IST)' : 'Night Shift (US Time)' }}</strong> attendance for {{ date('d M Y', strtotime($selected_date)) }}
                            @if(($selected_shift ?? 'Day') === 'Night')
                                <br><small class="text-muted"><i class="fas fa-clock me-1"></i>Night shift operates on US timezone (EST/PST)</small>
                            @endif
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Shift</th>
                                        <th>shift_status</th>
                                        <th>In Time</th>
                                        <th>Out Time</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $emp)
                                        @php $att = $attendance_data[$emp->id] ?? null; @endphp
                                        <tr>
                                        <td>{{ $emp->employee_id ?? 'N/A' }}</td>
                                            <td>{{ $emp->full_name ?: ($emp->first_name . ' ' . $emp->last_name) }}</td>
                                            <td>{{ $emp->email }}</td>
                                            <td>{{ $emp->department ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $emp->shift === 'Day' ? 'primary' : 'dark' }}">
                                                    {{ $emp->shift === 'Day' ? 'Day (IST)' : 'Night (US)' }}
                                                </span>
                                            </td>
                                            <td>
                                                <select name="employees[{{ $emp->id }}][shift_status]" class="form-select form-select-sm shift-status-dropdown" data-employee-id="{{ $emp->id }}">
                                                    <option value="">Select shift_status</option>
                                                    @php
                                                        $isWeekOff = date('w', strtotime($selected_date)) == 0; // Sunday = 0
                                                        $currentStatus = $att->shift_status ?? '';
                                                        // Normalize status for comparison
                                                        $currentStatus = ucfirst(str_replace('_', ' ', strtolower($currentStatus)));
                                                    @endphp
                                                    @if($isWeekOff)
                                                        <option value="Week Off" {{ $currentStatus === 'Week Off' ? 'selected' : '' }}>Week Off</option>
                                                    @else
                                                        <option value="Present" {{ $currentStatus === 'Present' ? 'selected' : '' }}>Present</option>
                                                        <option value="Absent" {{ $currentStatus === 'Absent' ? 'selected' : '' }}>Absent</option>
                                                        <option value="Half Day" {{ $currentStatus === 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                                        <option value="Paid Leave" {{ $currentStatus === 'Paid Leave' ? 'selected' : '' }}>Paid Leave</option>
                                                        <option value="Comp Off" {{ $currentStatus === 'Comp Off' ? 'selected' : '' }}>Comp Off</option>
                                                        <option value="Unauthorized Leave" {{ $currentStatus === 'Unauthorized Leave' ? 'selected' : '' }}>Unauthorized Leave</option>
                                                        <option value="Holiday" {{ $currentStatus === 'Holiday' ? 'selected' : '' }}>Holiday</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td>
                                                <input type="time" name="employees[{{ $emp->id }}][entry_time]" class="form-control form-control-sm" value="{{ $att->entry_time ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="time" name="employees[{{ $emp->id }}][exit_time]" class="form-control form-control-sm" value="{{ $att->exit_time ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="text" name="employees[{{ $emp->id }}][reason]" class="form-control form-control-sm" placeholder="Reason" value="{{ $att->reason ?? '' }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted">No employees found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($employees->count() > 0)
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Save Attendance
                                </button>
                                <button type="button" class="btn btn-success btn-lg ms-2" onclick="generateSalaryNow()">
                                    <i class="fas fa-money-bill-wave me-2"></i>Generate Salary Now
                                </button>
                            </div>
                        @endif
                    </form>
                    @else
                    <!-- Weekly/Monthly View -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Half Day</th>
                                    <th>Paid Leave</th>
                                    <th>Comp Off</th>
                                    <th>Unauthorized Leave</th>
                                    <th>Holiday</th>
                                    <th>Week Off</th>
                                    <th>Total Working Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendance_summary ?? [] as $summary)
                                <tr>
                                    <td>{{ $summary['name'] }}</td>
                                    <td>{{ $summary['department'] }}</td>
                                    <td><span class="badge bg-success">{{ $summary['present'] }}</span></td>
                                    <td><span class="badge bg-danger">{{ $summary['absent'] }}</span></td>
                                    <td><span class="badge bg-info">{{ $summary['half_day'] }}</span></td>
                                    <td><span class="badge bg-primary">{{ $summary['paid_leave'] }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $summary['comp_off'] }}</span></td>
                                    <td><span class="badge bg-warning">{{ $summary['unauthorized_leave'] }}</span></td>
                                    <td><span class="badge bg-dark">{{ $summary['holiday'] }}</span></td>
                                    <td><span class="badge bg-light text-dark">{{ $summary['week_off'] }}</span></td>
                                    <td><strong>{{ $summary['total'] }}</strong></td>
                                </tr>
                                @empty
                                <tr><td colspan="11" class="text-center text-muted">No attendance data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Generate Salary Button for Monthly View -->
                    @if(($view_type ?? 'daily') === 'monthly' && count($attendance_summary ?? []) > 0)
                        <div class="text-center mt-4">
                            <a href="{{ route('admin.salary.index') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-money-bill-wave me-2"></i>Generate Monthly Salary
                            </a>
                            <p class="text-muted mt-2">Monthly attendance complete! Now generate salary for employees.</p>
                        </div>
                        <hr class="my-4">
                    @endif

                    <!-- Saved Attendance Display -->
                    @if(($view_type ?? 'daily') === 'daily')
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-list me-2"></i>Saved Attendance for {{ date('d M Y', strtotime($selected_date)) }}</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Department</th>
                                        <th>Present/Absent</th>
                                        <th>In Time</th>
                                        <th>Out Time</th>
                                        <th>Early (min)</th>
                                        <th>Overtime (hrs)</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $emp)
                                        @if(isset($attendance_data[$emp->id]))
                                            @php $att = $attendance_data[$emp->id]; @endphp
                                            <tr>
                                                <td>{{ $emp->employee_id ?? 'N/A' }}</td>
                                                <td>{{ $emp->full_name ?: ($emp->first_name . ' ' . $emp->last_name) }}</td>
                                                <td>{{ $emp->department ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $att->shift_status === 'Present' ? 'success' : ($att->shift_status === 'Absent' ? 'danger' : 'warning') }}">
                                                        {{ $att->shift_status }}
                                                    </span>
                                                </td>
                                                <td>{{ $att->entry_time ?? ($att->in_time ? date('h:i A', strtotime($att->in_time)) : '-') }}</td>
                                                <td>{{ $att->exit_time ?? ($att->out_time ? date('h:i A', strtotime($att->out_time)) : '-') }}</td>
                                                <td>
                                                    @if(isset($att->early_checkout_minutes) && $att->early_checkout_minutes > 0)
                                                        {{ floor($att->early_checkout_minutes / 60) }}h {{ $att->early_checkout_minutes % 60 }}m
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ isset($att->overtime_hours) && $att->overtime_hours > 0 ? $att->overtime_hours : '-' }}</td>
                                                <td>{{ isset($att->reason) && $att->reason ? $att->reason : '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tbody>

                            </table>
                        </div>
                        <hr class="my-4">

                        <h5 class="mb-3">
                            <i class="fas fa-history me-2"></i>Attendance Logs
                        </h5>

                        <div class="table-responsive">
                        <table class="table table-hover table-bordered">

                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Entry</th>
                                <th>Exit</th>
                                <th>Shift</th>
                                <th>Status</th>
                                <th>Total Work</th>
                                <th>Overtime</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($filtered_logs as $log)

                            <tr>
                                <td>{{ $log->employee_code ?? $log->employee_id }}</td>
                                <td>{{ $log->date }}</td>
                                <td>{{ $log->entry_time }}</td>
                                <td>{{ $log->exit_time }}</td>
                                <td>{{ $log->shift_type }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $log->shift_status }}
                                    </span>
                                </td>
                                <td>{{ $log->total_work_time ?? '-' }}</td>
                                <td>{{ $log->overtime_hours ?? '-' }}</td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No attendance logs found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                        </div>
                    @endif
                </div>
               
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus, .form-select:focus { 
    border-color: #667eea; 
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); 
}
.btn-primary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
    border: none; 
}
</style>

<script>
function generateSalaryNow() {
    if (confirm('Generate salary for current month based on attendance data?')) {
        const currentDate = new Date();
        const month = currentDate.getMonth() + 1;
        const year = currentDate.getFullYear();
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.salary.generate") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const monthInput = document.createElement('input');
        monthInput.type = 'hidden';
        monthInput.name = 'month';
        monthInput.value = month;
        
        const yearInput = document.createElement('input');
        yearInput.type = 'hidden';
        yearInput.name = 'year';
        yearInput.value = year;
        
        form.appendChild(csrfToken);
        form.appendChild(monthInput);
        form.appendChild(yearInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-refresh attendance logs every 30 seconds
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection