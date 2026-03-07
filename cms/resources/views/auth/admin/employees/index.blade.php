@extends('auth.layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
.container-fluid {

    padding: 1px 40px 40px 150px !important;
    background: #f5f7fa;
    min-height: 100vh;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 8px;
}

.page-subtitle {
    color: #718096;
    font-size: 14px;
    margin-bottom: 25px;
}

.stats-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.stat-item {
    text-align: center;
    padding: 10px;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #2eacb3;
}

.stat-label {
    font-size: 13px;
    color: #718096;
    margin-top: 5px;
}

.search-filter-bar {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.search-input {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 15px 10px 40px;
    font-size: 14px;
    transition: all 0.3s;
    width: 100%;
}

.search-input:focus {
    border-color: #2eacb3;
    box-shadow: 0 0 0 3px rgba(46,172,179,0.1);
    outline: none;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

.data-table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    overflow: hidden;
}

.table {
    margin: 0;
    font-size: 14px;
}

.table thead th {
    background: #f7fafc;
    border: none;
    color: #4a5568;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 16px 12px;
    border-bottom: 2px solid #e2e8f0;
}

.table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.table tbody tr:hover {
    background: #f7fafc;
}

.table td {
    padding: 16px 12px;
    vertical-align: middle;
    border: none;
}

.employee-avatar {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #e2e8f0;
}

.employee-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
}

.badge {
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.badge.bg-success { background: #d4edda !important; color: #155724 !important; }
.badge.bg-warning { background: #fff3cd !important; color: #856404 !important; }
.badge.bg-danger { background: #f8d7da !important; color: #721c24 !important; }
.badge.bg-info { background: #d1ecf1 !important; color: #0c5460 !important; }
.badge.bg-secondary { background: #e2e8f0 !important; color: #4a5568 !important; }

.action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border: none;
    background: transparent;
    margin: 0 2px;
    cursor: pointer;
}

.action-icon:hover {
    transform: translateY(-2px);
}

.action-icon.view { color: #3182ce; }
.action-icon.view:hover { background: #ebf8ff; }

.action-icon.details { color: #38a169; }
.action-icon.details:hover { background: #f0fff4; }

.action-icon.edit { color: #ed8936; }
.action-icon.edit:hover { background: #fffaf0; }

.action-icon.delete { color: #e53e3e; }
.action-icon.delete:hover { background: #fff5f5; }

.table-footer {
    padding: 20px;
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
}

.stats-card {
    margin-bottom: 25px;
}

.stat-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-icon {
    font-size: 28px;
    padding: 15px;
    border-radius: 12px;
    color: white;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
}

.stat-label {
    font-size: 14px;
    color: #718096;
}

/* Color Variations */
.total .stat-icon {
    background: linear-gradient(45deg, #4e73df, #224abe);
}

.active .stat-icon {
    background: linear-gradient(45deg, #38a169, #2f855a);
}

.notice .stat-icon {
    background: linear-gradient(45deg, #ed8936, #dd6b20);
}

.resigned .stat-icon {
    background: linear-gradient(45deg, #e53e3e, #c53030);
}

</style>
@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid">

    <!-- Page Header -->
    <div class="mb-4">
       
        <p class="page-subtitle"></p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-card">
    <div class="row g-4">
        
        <!-- Total Employees -->
        <div class="col-md-3">
            <div class="stat-box total">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $employees->count() }}</div>
                    <div class="stat-label">Total Employees</div>
                </div>
            </div>
        </div>

        <!-- Active -->
        <div class="col-md-3">
            <div class="stat-box active">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div class="stat-number">
                        {{ $employees->where('employee_status', 'active')->count() }}
                    </div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
        </div>

        <!-- Notice Period -->
        <div class="col-md-3">
            <div class="stat-box notice">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="stat-number">
                        {{ $employees->where('employee_status', 'notice_period')->count() }}
                    </div>
                    <div class="stat-label">Notice Period</div>
                </div>
            </div>
        </div>

        <!-- Resigned -->
        <div class="col-md-3">
            <div class="stat-box resigned">
                <div class="stat-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div>
                    <div class="stat-number">
                        {{ $employees->where('employee_status', 'resigned')->count() }}
                    </div>
                    <div class="stat-label">Resigned</div>
                </div>
            </div>
        </div>

    </div>
</div>

    <!-- Search Bar -->
    <div class="search-filter-bar">
        <div class="position-relative">
            <i class="bi bi-search search-icon"></i>
            <input type="text"
                   class="search-input"
                   placeholder="Search by name, email, phone, department or employee ID..."
                   id="employeeSearch">
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="50"><input type="checkbox" class="form-check-input"></th>
                        <th>Employee</th>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Contact</th>
                        <th>Documents</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        
                        <!-- Employee Info -->
                        <td>
                            <div class="d-flex align-items-center">
                                @if($emp->selfie)
                                    <img src="{{ asset('uploads/selfies/' . $emp->selfie) }}" class="employee-avatar me-3">
                                @else
                                    <img src="https://i.pravatar.cc/42?u={{ $emp->id }}" class="employee-avatar me-3">
                                @endif
                                <div>
                                    <div class="employee-name">{{ $emp->full_name ?? $emp->first_name . ' ' . $emp->last_name }}</div>
                                    <small class="text-muted">{{ $emp->email }}</small>
                                </div>
                            </div>
                        </td>
                        
                        <td><span class="text-muted">{{ $emp->employee_id ?? 'N/A' }}</span></td>
                        
                        <td>
                            <div class="fw-medium">{{ ucfirst($emp->user_type) }}</div>
                            @if($emp->platform)
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $emp->platform)) }}</small>
                            @endif
                        </td>
                        
                        <td>{{ ucfirst($emp->department) }}</td>
                        
                        <td>
                            <div><i class="bi bi-telephone text-success me-1"></i><small>{{ $emp->phone }}</small></div>
                        </td>
                        
                        <td>
                            @php
                                $docCount = $emp->documents ? $emp->documents->count() : 0;
                                $verifiedCount = $emp->documents ? $emp->documents->where('status', 'verified')->count() : 0;
                            @endphp
                            <span class="badge bg-info">{{ $docCount }} Total</span>
                            @if($verifiedCount > 0)
                                <span class="badge bg-success">{{ $verifiedCount }} ✓</span>
                            @endif
                        </td>
                        
                        <td><small class="text-muted">{{ $emp->created_at ? $emp->created_at->format('d M Y') : 'N/A' }}</small></td>
                        
                        <td>
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'resigned' => 'warning',
                                    'terminated' => 'danger',
                                    'absconding' => 'danger',
                                    'notice_period' => 'info',
                                    'left' => 'secondary',
                                    'on_hold' => 'warning'
                                ];
                                $status = $emp->employee_status ?? 'active';
                                $color = $statusColors[$status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                        </td>
                        
                        <td class="text-center">
                            <a href="{{ route('admin.employees.profile.show', $emp->id) }}" class="action-icon view" title="View Profile">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.employees.details', $emp->id) }}" class="action-icon details" title="View Details">
                                <i class="bi bi-file-text"></i>
                            </a>
                            <a href="{{ route('admin.employees.edit', $emp->id) }}" class="action-icon edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- <form action="{{ route('admin.employees.delete', $emp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this employee?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-icon delete" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form> -->
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #cbd5e0;"></i>
                            <p class="text-muted mt-3">No employees found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="table-footer">
            <div class="text-muted">Showing {{ $employees->count() }} employees</div>
            <select class="form-select" style="width: auto;">
                <option>10 per page</option>
                <option>25 per page</option>
                <option>50 per page</option>
            </select>
        </div>
    </div>

</div>

<script>
// Search functionality
document.getElementById('employeeSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection
