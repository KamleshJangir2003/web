@extends('auth.layouts.app')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .container-fluid{
    margin-top: 60px !important;
    padding-left: 130px !important;
}
.table th {
    font-size: 13px;
    white-space: nowrap;
    background-color: #f8f9fa;
}
.table td {
    font-size: 12px;
    vertical-align: middle;
}
.form-select-sm {
    font-size: 12px;
    padding: 4px 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: #fff;
    min-width: 80px;
}
.form-select-sm:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}
.certification-days {
    background-color: #e3f2fd;
    color: #1976d2;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
}
</style>

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-semibold">Hired Employees</h4>
        <span class="badge bg-primary">{{ $hiredEmployees->count() }} Employees</span>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Joining Date</th>
                        <th>Induction Round</th>
                        <th>Training</th>
                        <th>Certification Period</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hiredEmployees as $employee)
                    <tr>
                        <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://i.pravatar.cc/40?u={{ $employee->id }}"
                                     class="rounded-circle me-2"
                                     width="40" height="40">
                                <div>
                                    <div class="fw-medium">{{ $employee->full_name }}</div>
                                    <small class="text-muted">{{ $employee->department ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.employees.hired.update', $employee->id) }}" class="d-inline">
                                @csrf
                                <input type="date" name="joining_date" class="form-control form-control-sm" 
                                       value="{{ $employee->joining_date ? $employee->joining_date->format('Y-m-d') : date('Y-m-d') }}"
                                       onchange="this.form.submit()" style="font-size: 12px; width: 140px;">
                                <input type="hidden" name="induction_round" value="{{ $employee->induction_round ?? 'yes' }}">
                                <input type="hidden" name="training" value="{{ $employee->training ?? 'yes' }}">
                                <input type="hidden" name="certification_period" value="{{ $employee->certification_period ?? 5 }}">
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.employees.hired.update', $employee->id) }}" class="d-inline">
                                @csrf
                                <select name="induction_round" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="yes" {{ ($employee->induction_round ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ ($employee->induction_round ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                                <input type="hidden" name="training" value="{{ $employee->training ?? 'yes' }}">
                                <input type="hidden" name="certification_period" value="{{ $employee->certification_period ?? 5 }}">
                                <input type="hidden" name="action_status" value="{{ $employee->action_status ?? 'selected' }}">
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.employees.hired.update', $employee->id) }}" class="d-inline">
                                @csrf
                                <select name="training" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="yes" {{ ($employee->training ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ ($employee->training ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                                <input type="hidden" name="induction_round" value="{{ $employee->induction_round ?? 'yes' }}">
                                <input type="hidden" name="certification_period" value="{{ $employee->certification_period ?? 5 }}">
                                <input type="hidden" name="action_status" value="{{ $employee->action_status ?? 'selected' }}">
                            </form>
                        </td>
                        <td>
                            @if($employee->joining_date)
                                @php
                                    $joiningDate = $employee->joining_date->startOfDay();
                                    $today = now()->startOfDay();
                                    $daysCompleted = $joiningDate->diffInDays($today) + 1; // +1 because joining day counts as day 1
                                    $totalDays = 5;
                                    $certificationCompleted = $daysCompleted >= $totalDays;
                                @endphp
                                
                                @if($daysCompleted <= $totalDays)
                                    <span class="certification-days">Day {{ $daysCompleted }} of {{ $totalDays }}</span>
                                @else
                                    <span class="certification-days bg-success text-white">{{ $totalDays }} Days Completed</span>
                                @endif
                            @else
                                <span class="certification-days bg-secondary text-white">No joining date</span>
                                @php $certificationCompleted = false; @endphp
                            @endif
                        </td>
                        <td>
                            @if($employee->joining_date)
                                @php
                                    $joiningDate = $employee->joining_date->startOfDay();
                                    $today = now()->startOfDay();
                                    $daysCompleted = $joiningDate->diffInDays($today) + 1;
                                    $totalDays = 5;
                                    $certificationCompleted = $daysCompleted >= $totalDays;
                                @endphp
                            @else
                                @php $certificationCompleted = false; @endphp
                            @endif
                            
                            @if($certificationCompleted)
                                <form method="POST" action="{{ route('admin.employees.hired.update', $employee->id) }}" class="d-inline">
                                    @csrf
                                    <select name="action_status" class="form-select form-select-sm" onchange="toggleReasonField(this)" style="min-width: 120px;">
    <option value="" {{ empty($employee->action_status) ? 'selected' : '' }} disabled>
        Select Status
    </option>

    <option value="selected" {{ ($employee->action_status ?? '') === 'selected' ? 'selected' : '' }}>
        Selected
    </option>

    <option value="not_selected" {{ ($employee->action_status ?? '') === 'not_selected' ? 'selected' : '' }}>
        Not Selected
    </option>
</select>

                                    <input type="hidden" name="induction_round" value="{{ $employee->induction_round ?? 'yes' }}">
                                    <input type="hidden" name="training" value="{{ $employee->training ?? 'yes' }}">
                                    <input type="hidden" name="certification_period" value="{{ $employee->certification_period ?? 5 }}">
                                    
                                    @if(($employee->action_status ?? '') == 'reason')
                                    <div class="mt-2">
                                        <input type="text" name="action_reason" class="form-control form-control-sm" 
                                               placeholder="Enter reason..." value="{{ $employee->action_reason ?? '' }}"
                                               style="font-size: 11px;">
                                        <button type="submit" class="btn btn-primary btn-sm mt-1">Update</button>
                                    </div>
                                    @endif
                                </form>
                            @else
                                <div class="text-center">
                                    <span class="badge bg-warning text-dark">Certification Period Active</span>
                                    <br>
                                    <small class="text-muted">Cannot select/reject until 5 days complete</small>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hired employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <small class="text-muted">Showing {{ $hiredEmployees->count() }} hired employees</small>
        </div>
    </div>
</div>

<script>
function toggleReasonField(select) {
    const form = select.closest('form');
    const existingReasonDiv = form.querySelector('.mt-2');
    
    if (select.value === 'selected') {
        // Submit the form to update the status
        form.submit();
        return;
    }
    
    if (select.value === 'not_selected') {
        if (!existingReasonDiv) {
            const reasonDiv = document.createElement('div');
            reasonDiv.className = 'mt-2';
            reasonDiv.innerHTML = `
                <input type="text" name="action_reason" class="form-control form-control-sm" 
                       placeholder="Enter reason for not selecting..." style="font-size: 11px;" required>
                <button type="submit" class="btn btn-primary btn-sm mt-1">Update</button>
            `;
            select.parentNode.appendChild(reasonDiv);
        }
    } else if (select.value === 'reason') {
        if (!existingReasonDiv) {
            const reasonDiv = document.createElement('div');
            reasonDiv.className = 'mt-2';
            reasonDiv.innerHTML = `
                <input type="text" name="action_reason" class="form-control form-control-sm" 
                       placeholder="Enter reason..." style="font-size: 11px;">
                <button type="submit" class="btn btn-primary btn-sm mt-1">Update</button>
            `;
            select.parentNode.appendChild(reasonDiv);
        }
    } else {
        if (existingReasonDiv) {
            existingReasonDiv.remove();
        }
    }
}
</script>

@endsection