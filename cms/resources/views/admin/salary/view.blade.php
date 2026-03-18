@extends('auth.layouts.app')
<style>
    /* ===== FIX CONTENT SHIFT WITH SIDEBAR ===== */

.container-fluid {
    padding-left: 130px !important;
    padding-right: 25px !important;
    margin-top: 50px;
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

.form-control:focus, .form-select:focus { 
    border-color: #667eea; 
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); 
}
.btn-primary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
    border: none; 
}
</style>

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;" class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Salary Details</h4>
                    <a href="{{ route('admin.salary.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Salary List
                    </a>
                </div>
                <div class="card-body">
                    
                    <!-- Employee Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Employee Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $salaryRecord->employee->first_name }} {{ $salaryRecord->employee->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Employee ID:</strong></td>
                                            <td>{{ $salaryRecord->employee->employee_id ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Department:</strong></td>
                                            <td>{{ $salaryRecord->employee->department ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Designation:</strong></td>
                                            <td>{{ $salaryRecord->employee->job_title ?: ($salaryRecord->employee->department ?: 'N/A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Shift:</strong></td>
                                            <td>
                                                <span class="badge {{ $salaryRecord->shift == 'Day' ? 'bg-warning' : 'bg-info' }}">
                                                    {{ $salaryRecord->shift ?? 'Day' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-success">Salary Period</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Month:</strong></td>
                                            <td>{{ date('F', mktime(0, 0, 0, $salaryRecord->month, 1)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Year:</strong></td>
                                            <td>{{ $salaryRecord->year }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Days:</strong></td>
                                            <td>{{ \Carbon\Carbon::create($salaryRecord->year, $salaryRecord->month)->daysInMonth }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Working Days:</strong></td>
                                            <td><span class="badge bg-success">{{ number_format($salaryRecord->working_days, 1) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Generated On:</strong></td>
                                            <td>{{ $salaryRecord->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Breakdown -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance Breakdown</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($attendanceBreakdown as $status => $count)
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="card text-center h-100 
                                                    @if($status == 'Present') border-success
                                                    @elseif($status == 'Absent') border-danger
                                                    @elseif($status == 'Half Day') border-warning
                                                    @elseif($status == 'Paid Leave') border-primary
                                                    @else border-secondary
                                                    @endif">
                                                    <div class="card-body">
                                                        <h3 class="
                                                            @if($status == 'Present') text-success
                                                            @elseif($status == 'Absent') text-danger
                                                            @elseif($status == 'Half Day') text-warning
                                                            @elseif($status == 'Paid Leave') text-primary
                                                            @else text-secondary
                                                            @endif">{{ $count }}</h3>
                                                        <p class="card-text small">{{ $status }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Calculation -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Salary Calculation</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold">In-Hand Salary (Monthly)</td>
                                                    <td class="text-end fw-bold text-primary">₹{{ number_format($salaryRecord->basic_salary, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Per Day Salary</td>
                                                    <td class="text-end">₹{{ number_format($salaryRecord->basic_salary / \Carbon\Carbon::create($salaryRecord->year, $salaryRecord->month)->daysInMonth, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Working Days</td>
                                                    <td class="text-end">{{ number_format($salaryRecord->working_days, 1) }} / {{ \Carbon\Carbon::create($salaryRecord->year, $salaryRecord->month)->daysInMonth }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Earned Salary (Working Days × Per Day)</td>
                                                    <td class="text-end">₹{{ number_format($salaryRecord->net_salary, 2) }}</td>
                                                </tr>
                                                @if($salaryRecord->deduction > 0)
                                                <tr class="table-warning">
                                                    <td class="fw-bold">Not Earned (Absent/Leave Days)</td>
                                                    <td class="text-end fw-bold text-warning">- ₹{{ number_format($salaryRecord->deduction, 2) }}</td>
                                                </tr>
                                                @endif
                                                <tr class="table-success">
                                                    <td class="fw-bold fs-5">Net Take Home Salary</td>
                                                    <td class="text-end fw-bold fs-5 text-success">₹{{ number_format($salaryRecord->net_salary, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('admin.salary.slip', $salaryRecord->id) }}" class="btn btn-success btn-lg me-3" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>Generate Salary Slip
                            </a>
                            <a href="{{ route('admin.salary.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection