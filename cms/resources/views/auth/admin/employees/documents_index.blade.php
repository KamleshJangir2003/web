@extends('auth.layouts.app')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .container-fluid{
    
    padding-left: 130px !important;
}
.status-badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
}
.status-not-started { background: #f8f9fa; color: #6c757d; }
.status-pending { background: #fff3cd; color: #856404; }
.status-completed { background: #d1edff; color: #0c63e4; }
.status-in-progress { background: #e2e3e5; color: #495057; }
.document-stats {
    font-size: 12px;
    color: #6c757d;
}
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
.text-success {
    color: #28a745 !important;
}
.text-primary {
    color: #2eacb3 !important;
}
.text-info {
    color: #17a2b8 !important;
}
.form-select-sm {
    font-size: 12px;
    padding: 4px 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: #fff;
}
.form-select-sm:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.status-rejected { background: #f8d7da; color: #721c24; }
</style>
@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show container-fluid">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show container-fluid">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-semibold">Employee Documents Status</h4>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Joining Date</th>
                        <th>CTC</th>
                        <th>In Hand</th>
                        <th class="text-center">Hired Status</th>
                        <th class="text-center">Document Status</th>
                        <th class="text-center">Progress</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td>
                            <div class="fw-medium">{{ $emp->full_name ?? ($emp->first_name . ' ' . $emp->last_name) }}</div>
                            <small class="text-muted">{{ $emp->phone ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $emp->email }}</td>
                        <td>{{ ucfirst($emp->department ?? 'N/A') }}</td>
                        <td>
                            @if($emp->joining_date)
                                <span class="text-success fw-medium">{{ $emp->joining_date->format('M d, Y') }}</span>
                            @else
                                <span class="text-muted">Not Set</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->current_ctc)
                                <span class="text-primary fw-medium">₹{{ number_format($emp->current_ctc) }}</span>
                            @else
                                <span class="text-muted">Not Set</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->in_hand_salary)
                                <span class="text-info fw-medium">₹{{ number_format($emp->in_hand_salary) }}</span>
                            @else
                                <span class="text-muted">Not Set</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(isset($emp->is_interview_candidate) && $emp->is_interview_candidate)
                                <span class="badge bg-warning text-dark">Pending Documents</span>
                            @else
                                @php
                                    $stats = $emp->document_stats;
                                    $allDocsUploaded = $stats['uploaded'] >= 6 && $stats['missing'] == 0;
                                    $offerLetterSent = \App\Models\EmailLog::where('to_email', $emp->email)
                                        ->where('subject', 'like', '%Offer Letter%')
                                        ->where('status', 'sent')
                                        ->exists();
                                    $canHire = $allDocsUploaded && $offerLetterSent;
                                @endphp
                                <form method="POST" action="{{ route('admin.employees.update-hired-status', $emp->id) }}?t={{ time() }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="hired_status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()" {{ !$canHire && ($emp->hired_status ?? 'not_hired') == 'not_hired' ? 'disabled' : '' }}>
                                        <option value="not_hired" {{ ($emp->hired_status ?? 'not_hired') == 'not_hired' ? 'selected' : '' }}>Not Hired</option>
                                        <option value="hired" {{ ($emp->hired_status ?? 'not_hired') == 'hired' ? 'selected' : '' }} {{ !$canHire ? 'disabled' : '' }}>Hired</option>
                                    </select>
                                </form>
                                @if(!$canHire && ($emp->hired_status ?? 'not_hired') == 'not_hired')
                                    <small class="text-muted d-block mt-1">
                                        @if(!$allDocsUploaded)
                                            Upload docs ({{ $stats['uploaded'] }}/6)
                                        @else
                                            Send offer letter
                                        @endif
                                    </small>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $stats = $emp->document_stats;
                                $statusClass = 'status-' . $stats['status'];
                                $statusText = match($stats['status']) {
                                    'not_started' => 'Not Started',
                                    'pending' => 'Pending Review',
                                    'completed' => 'All Verified',
                                    'in_progress' => 'Uploaded',
                                    default => 'Unknown'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            <div class="document-stats mt-1">
                                @if($stats['verified'] > 0)
                                    <span class="text-success">✓ {{ $stats['verified'] }} Verified</span>
                                @endif
                                @if($stats['uploaded'] > 0 && $stats['status'] == 'in_progress')
                                    <span class="text-info">📄 {{ $stats['uploaded'] }} Uploaded</span>
                                @endif
                                @if($stats['missing'] > 0)
                                    <span class="text-danger">❌ {{ $stats['missing'] }} Missing</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="progress" style="height: 8px; width: 80px; margin: 0 auto;">
                                @php
                                    $percentage = ($stats['uploaded'] / $stats['total_required']) * 100;
                                    $progressClass = $percentage == 100 ? 'bg-success' : ($percentage > 50 ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <div class="progress-bar {{ $progressClass }}" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <small class="text-muted">{{ $stats['uploaded'] }}/{{ $stats['total_required'] }}</small>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ route('admin.employees.document', ['userId' => $emp->id]) }}"
                                   class="btn btn-sm btn-primary"
                                   title="View Documents">
                                    <i class="fa-solid fa-file-lines me-1"></i> Documents
                                </a>
                                <button type="button" class="btn btn-sm btn-danger reject-btn" data-bs-toggle="modal" data-bs-target="#rejectModal" data-emp-id="{{ $emp->id }}" data-emp-name="{{ $emp->full_name ?? ($emp->first_name . ' ' . $emp->last_name) }}">
                                    <i class="fa-solid fa-ban me-1"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            No employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <small class="text-muted">Showing {{ $employees->count() }} employees</small>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Employee</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to reject <strong id="empName"></strong>?</p>
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="4" placeholder="Enter reason for rejection..." required></textarea>
                        <small class="text-muted">This reason will be communicated to the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectModal = document.getElementById('rejectModal');
    const rejectForm = document.getElementById('rejectForm');
    const rejectionReason = document.getElementById('rejectionReason');
    const empName = document.getElementById('empName');
    
    // When modal is shown
    rejectModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const empId = button.getAttribute('data-emp-id');
        const empNameText = button.getAttribute('data-emp-name');
        
        console.log('Modal opened for employee ID:', empId);
        console.log('Employee name:', empNameText);
        
        empName.textContent = empNameText;
        rejectForm.action = '/admin/employees/' + empId + '/reject';
        rejectionReason.value = '';
        
        console.log('Form action set to:', rejectForm.action);
    });
    
    // Form submission
    rejectForm.addEventListener('submit', function(e) {
        const reason = rejectionReason.value.trim();
        
        console.log('Form submitted');
        console.log('Rejection reason:', reason);
        console.log('Reason length:', reason.length);
        
        if (!reason || reason.length < 10) {
            e.preventDefault();
            alert('Please enter a rejection reason (minimum 10 characters)');
            return false;
        }
        
        console.log('Form validation passed, submitting to:', this.action);
    });
});
</script>
@endsection
