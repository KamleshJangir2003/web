@extends('employee.layouts.app')

@section('title', 'Employee Dashboard')
@section('page-title', 'Employee Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-black">
                    <h2 class="mb-1">Welcome back, {{ $user->full_name ?? $user->first_name . ' ' . $user->last_name }}!</h2>
                    <p class="mb-0 opacity-75">{{ $user->department }} Department • {{ date('l, F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">Today's Status</h6>
                            <h4 class="mb-0 {{ $todayAttendance ? 'text-success' : 'text-warning' }}">
                                {{ $todayAttendance ? 'Present' : 'Not Marked' }}
                            </h4>
                            <small class="text-muted">
                                {{ $todayAttendance ? 'Checked in at ' . date('g:i A', strtotime($todayAttendance->in_time)) : 'Mark your attendance' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">Documents</h6>
                            <h4 class="mb-0">{{ $documentsCount }}</h4>
                            <small class="text-muted">{{ $pendingDocs }} pending approval</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-check fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">This Month</h6>
                            <h4 class="mb-0">{{ $monthlyAttendance }}/{{ date('j') }}</h4>
                            <small class="text-muted">Days present</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">Approved Docs</h6>
                            <h4 class="mb-0">{{ $approvedDocs }}</h4>
                            <small class="text-muted">Documents verified</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activities -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activities</h5>
                    <small class="text-muted">Last 7 days</small>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($todayAttendance)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success rounded-circle p-2">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Attendance Marked</h6>
                                <p class="text-muted mb-1">Checked in for today</p>
                                <small class="text-muted">{{ $todayAttendance ? date('g:i A', strtotime($todayAttendance->in_time)) : 'Not marked today' }}</small>
                            </div>
                        </div>
                        @endif
                        
                        @if($documentsCount > 0)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-info rounded-circle p-2">
                                    <i class="fas fa-file text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Documents Status</h6>
                                <p class="text-muted mb-1">{{ $documentsCount }} documents uploaded, {{ $approvedDocs }} approved</p>
                                <small class="text-muted">{{ $pendingDocs > 0 ? $pendingDocs . ' pending review' : 'All documents approved' }}</small>
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle p-2">
                                    <i class="fas fa-user-plus text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Account Created</h6>
                                <p class="text-muted mb-1">Employee account registered</p>
                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Info -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$todayAttendance)
                        <form method="POST" action="#" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-clock me-2"></i>Mark Attendance
                            </button>
                        </form>
                        @else
                        <button class="btn btn-success w-100" disabled>
                            <i class="fas fa-check me-2"></i>Attendance Marked
                        </button>
                        @endif
                        
                        <a href="{{ route('employee.documents') }}" class="btn btn-outline-info">
                            <i class="fas fa-file-upload me-2"></i>Upload Documents
                        </a>
                        
                        <a href="{{ route('employee.leaves.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-calendar me-2"></i>Request Leave
                        </a>
                        
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-download me-2"></i>Download Salary Slip
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Your Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Employee ID:</strong> {{ $user->employee_id }}</p>
                    <p><strong>Department:</strong> {{ $user->department }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
                    <p><strong>Joining Date:</strong> {{ $user->joining_date ? date('d M Y', strtotime($user->joining_date)) : 'Not set' }}</p>
                    <p><strong>Current CTC:</strong> {{ $user->current_ctc ? '₹' . number_format($user->current_ctc, 2) : 'Not set' }}</p>
                    <p class="mb-0"><strong>Status:</strong> 
                        <span class="badge {{ $user->is_approved ? 'bg-success' : 'bg-warning' }}">
                            {{ $user->is_approved ? 'Active' : 'Pending Approval' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(isset($todayBirthdays) && $todayBirthdays->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-birthday-cake me-2"></i>Today's Birthdays 🎉</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($todayBirthdays as $birthday)
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="mb-0">{{ $birthday->first_name }} {{ $birthday->last_name }}</h6>
                                    <small class="text-muted">{{ $birthday->department }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection