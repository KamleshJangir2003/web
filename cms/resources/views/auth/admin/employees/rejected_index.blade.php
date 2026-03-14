@extends('auth.layouts.app')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .container-fluid {
        padding-left: 130px !important;
    }
    .status-badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 500;
    }
    .status-rejected { background: #f8d7da; color: #721c24; }
    .table th {
        font-size: 13px;
        white-space: nowrap;
    }
    .table td {
        font-size: 12px;
        vertical-align: middle;
    }
    .fw-medium {
        font-weight: 500;
    }
    .reason-text {
        max-width: 300px;
        word-wrap: break-word;
    }
</style>

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show container-fluid">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-semibold">Documents Rejected Employees</h4>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Rejection Reason</th>
                        <th>Rejected Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rejectedEmployees as $emp)
                    <tr>
                        <td>
                            <div class="fw-medium">{{ $emp->full_name ?? ($emp->first_name . ' ' . $emp->last_name) }}</div>
                            <small class="text-muted">{{ $emp->phone ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $emp->email }}</td>
                        <td>{{ ucfirst($emp->department ?? 'N/A') }}</td>
                        <td>
                            <div class="reason-text" title="{{ $emp->action_reason }}">
                                {{ $emp->action_reason ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $emp->updated_at ? $emp->updated_at->format('M d, Y') : 'N/A' }}
                            </small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#rehireModal" data-emp-id="{{ $emp->id }}" data-emp-name="{{ $emp->full_name ?? ($emp->first_name . ' ' . $emp->last_name) }}">
                                <i class="fa-solid fa-redo me-1"></i> Rehire
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No rejected employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <small class="text-muted">Showing {{ $rejectedEmployees->count() }} rejected employees</small>
        </div>
    </div>
</div>

<!-- Rehire Modal -->
<div class="modal fade" id="rehireModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Rehire Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rehireForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to rehire <strong id="empName"></strong>?</p>
                    <p class="text-muted">This will move the employee back to the documents pending list.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Rehire Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const rehireModal = document.getElementById('rehireModal');
rehireModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const empId = button.getAttribute('data-emp-id');
    const empName = button.getAttribute('data-emp-name');
    
    document.getElementById('empName').textContent = empName;
    document.getElementById('rehireForm').action = `/admin/employees/${empId}/rehire-from-rejected`;
});
</script>

@endsection
