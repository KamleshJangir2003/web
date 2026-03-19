@extends('employee.layouts.app')

@section('title', 'Employee Dashboard')
@section('page-title', 'Employee Dashboard')

@section('content')
<style>
    .employee-dashboard {
        padding: 10px 4px 30px;
    }

    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.18);
    }

    /* .welcome-card .card-body {
        padding: 28px;
    } */

    .welcome-title {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .welcome-subtitle {
        color: rgba(255, 255, 255, 0.88);
        font-size: 1rem;
        margin-bottom: 0;
        font-weight: 400;
    }

    .dashboard-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        transition: all 0.25s ease;
        overflow: hidden;
        background: #fff;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10);
    }

    .stats-card {
        border-left: 4px solid #4f46e5;
    }

    .stats-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        background: rgba(79, 70, 229, 0.10);
    }

    .stats-icon.primary {
        color: #2563eb;
        background: rgba(37, 99, 235, 0.10);
    }

    .stats-icon.info {
        color: #06b6d4;
        background: rgba(6, 182, 212, 0.10);
    }

    .stats-icon.success {
        color: #16a34a;
        background: rgba(22, 163, 74, 0.10);
    }

    .stats-icon.warning {
        color: #f59e0b;
        background: rgba(245, 158, 11, 0.12);
    }

    .stats-label {
        font-size: 0.92rem;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .stats-value {
        font-size: 1.9rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 6px;
        color: #111827;
    }

    .stats-status {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .section-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .section-card .card-header {
        background: #fff;
        border-bottom: 1px solid #edf2f7;
        padding: 18px 22px;
    }

    .section-card .card-header h5 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #111827;
    }

    .section-card .card-body {
        padding: 22px;
    }

    .activity-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding-bottom: 18px;
        margin-bottom: 18px;
        border-bottom: 1px dashed #e5e7eb;
    }

    .activity-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .activity-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        font-size: 16px;
    }

    .activity-icon.success { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .activity-icon.info { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .activity-icon.primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }

    .activity-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .activity-desc {
        color: #6b7280;
        margin-bottom: 4px;
        font-size: 0.95rem;
    }

    .activity-time {
        color: #9ca3af;
        font-size: 0.86rem;
    }

    .quick-btn {
        border-radius: 14px;
        padding: 12px 16px;
        font-weight: 600;
        font-size: 0.96rem;
        transition: 0.25s ease;
    }

    .quick-btn:hover {
        transform: translateY(-1px);
    }

    .info-list p {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        margin-bottom: 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
        color: #374151;
    }

    .info-list p:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-label {
        font-weight: 600;
        color: #111827;
        min-width: 120px;
    }

    .birthday-card {
        border-radius: 18px;
        border: none;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .birthday-card .card-header {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #111827;
        border: none;
        padding: 18px 22px;
    }

    .birthday-user {
        background: #fffaf0;
        border-radius: 14px;
        padding: 14px;
        height: 100%;
    }

    .birthday-user h6 {
        margin-bottom: 4px;
        font-weight: 700;
        color: #111827;
    }

    @media (max-width: 991px) {
        .welcome-title {
            font-size: 1.6rem;
        }

        .stats-value,
        .stats-status {
            font-size: 1.45rem;
        }
    }

    @media (max-width: 767px) {
        .employee-dashboard {
            padding: 8px 0 20px;
        }

        .welcome-card .card-body,
        .section-card .card-body,
        .section-card .card-header {
            padding: 16px;
        }

        .welcome-title {
            font-size: 1.45rem;
        }

        .welcome-subtitle {
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            font-size: 18px;
        }

        .stats-value,
        .stats-status {
            font-size: 1.35rem;
        }

        .info-list p {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .quick-btn {
            font-size: 0.92rem;
            padding: 11px 14px;
        }
    }
</style>

<div class="container-fluid employee-dashboard">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card welcome-card">
                <div class="card-body">
                    <h2 class="welcome-title">
                        Welcome back, {{ $user->full_name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}!
                    </h2>
                    <p class="welcome-subtitle">
                        {{ $user->department ?? 'Department Not Set' }} Department • {{ date('l, F j, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card dashboard-card stats-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon primary">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-label">Today's Status</div>
                            <div class="stats-status {{ $todayAttendance ? 'text-success' : 'text-warning' }}">
                                {{ $todayAttendance ? 'Present' : 'Not Marked' }}
                            </div>
                            <small class="text-muted">
                                {{ $todayAttendance ? 'Checked in at ' . date('g:i A', strtotime($todayAttendance->in_time)) : 'Mark your attendance' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card dashboard-card stats-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon info">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-label">Documents</div>
                            <div class="stats-value">{{ $documentsCount }}</div>
                            <small class="text-muted">{{ $pendingDocs }} pending approval</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card dashboard-card stats-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-label">This Month</div>
                            <div class="stats-value">{{ $monthlyAttendance }}/{{ date('j') }}</div>
                            <small class="text-muted">Days present</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card dashboard-card stats-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon warning">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-label">Approved Docs</div>
                            <div class="stats-value">{{ $approvedDocs }}</div>
                            <small class="text-muted">Documents verified</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Activities -->
        <div class="col-lg-8">
            <div class="card section-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activities</h5>
                    <small class="text-muted">Last 7 days</small>
                </div>
                <div class="card-body">
                    @if($todayAttendance)
                        <div class="activity-item">
                            <div class="activity-icon success">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="activity-title">Attendance Marked</div>
                                <div class="activity-desc">Checked in for today</div>
                                <div class="activity-time">{{ date('g:i A', strtotime($todayAttendance->in_time)) }}</div>
                            </div>
                        </div>
                    @endif

                    @if($documentsCount > 0)
                        <div class="activity-item">
                            <div class="activity-icon info">
                                <i class="fas fa-file"></i>
                            </div>
                            <div>
                                <div class="activity-title">Documents Status</div>
                                <div class="activity-desc">{{ $documentsCount }} documents uploaded, {{ $approvedDocs }} approved</div>
                                <div class="activity-time">
                                    {{ $pendingDocs > 0 ? $pendingDocs . ' pending review' : 'All documents approved' }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="activity-item">
                        <div class="activity-icon primary">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <div class="activity-title">Account Created</div>
                            <div class="activity-desc">Employee account registered</div>
                            <div class="activity-time">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>

                    @if(!$todayAttendance && $documentsCount == 0)
                        <div class="text-center py-4 text-muted">
                            No recent activities found.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Info -->
        <div class="col-lg-4">
            <div class="card section-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$todayAttendance)
                            <form method="POST" action="#" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary quick-btn w-100">
                                    <i class="fas fa-clock me-2"></i>Mark Attendance
                                </button>
                            </form>
                        @else
                            <button class="btn btn-success quick-btn w-100" disabled>
                                <i class="fas fa-check me-2"></i>Attendance Marked
                            </button>
                        @endif

                        <a href="{{ route('employee.documents') }}" class="btn btn-outline-info quick-btn">
                            <i class="fas fa-file-upload me-2"></i>Upload Documents
                        </a>

                        <a href="{{ route('employee.leaves.index') }}" class="btn btn-warning quick-btn text-dark">
                            <i class="fas fa-calendar me-2"></i>Request Leave
                        </a>

                        <button class="btn btn-outline-secondary quick-btn">
                            <i class="fas fa-download me-2"></i>Download Salary Slip
                        </button>
                    </div>
                </div>
            </div>

            <div class="card section-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Your Info</h5>
                </div>
                <div class="card-body info-list">
                    <p><span class="info-label">Employee ID</span> <span>{{ $user->employee_id }}</span></p>
                    <p><span class="info-label">Department</span> <span>{{ $user->department }}</span></p>
                    <p><span class="info-label">Email</span> <span>{{ $user->email }}</span></p>
                    <p><span class="info-label">Phone</span> <span>{{ $user->phone ?? 'Not provided' }}</span></p>
                    <p><span class="info-label">Joining Date</span> <span>{{ $user->joining_date ? date('d M Y', strtotime($user->joining_date)) : 'Not set' }}</span></p>
                    <p><span class="info-label">Current CTC</span> <span>{{ $user->current_ctc ? '₹' . number_format($user->current_ctc, 2) : 'Not set' }}</span></p>
                    <p class="mb-0">
                        <span class="info-label">Status</span>
                        <span class="badge {{ $user->is_approved ? 'bg-success' : 'bg-warning text-dark' }}">
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
                <div class="card birthday-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-birthday-cake me-2"></i>Today's Birthdays 🎉</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($todayBirthdays as $birthday)
                                <div class="col-md-4">
                                    <div class="birthday-user d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-2x text-warning me-3"></i>
                                        <div>
                                            <h6>{{ $birthday->first_name }} {{ $birthday->last_name }}</h6>
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