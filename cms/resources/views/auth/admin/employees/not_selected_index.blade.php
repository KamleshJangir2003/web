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
.rejection-reason {
    background-color: #ffebee;
    color: #c62828;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
}

    /* Mobile Fix */
    @media (max-width:768px){

.main-content{
margin-left:0 !important;
padding-top:0px !important;
margin-top:20px !important;
}

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
        <h4 class="mb-0 fw-semibold">Not Selected Employees</h4>
        <span class="badge bg-danger">{{ $notSelectedEmployees->count() }} Employees</span>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Joining Date</th>
                        <th>Reason</th>
                        <th>Date Rejected</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notSelectedEmployees as $employee)
                    <tr>
                        <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                        <td>
                            <!-- <div class="d-flex align-items-center">
                                <img src="https://i.pravatar.cc/40?u={{ $employee->id }}"
                                     class="rounded-circle me-2"
                                     width="40" height="40">
                                <div> -->
                                    <div class="fw-medium">{{ $employee->full_name }}</div>
                                    <small class="text-muted">{{ $employee->department ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $employee->phone ?? 'N/A' }}</td>
                        <td>
                            @if($employee->joining_date)
                                <span class="text-muted">{{ $employee->joining_date->format('M d, Y') }}</span>
                            @else
                                <span class="text-muted">Not Set</span>
                            @endif
                        </td>
                        <td>
                            <span class="rejection-reason">{{ $employee->action_reason ?? 'No reason provided' }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $employee->updated_at->format('M d, Y') }}</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.employees.rehire', $employee->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to rehire this employee?')">
                                    <i class="fas fa-user-check"></i> Rehire
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No rejected employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <small class="text-muted">Showing {{ $notSelectedEmployees->count() }} rejected employees</small>
        </div>
    </div>
</div>

@endsection