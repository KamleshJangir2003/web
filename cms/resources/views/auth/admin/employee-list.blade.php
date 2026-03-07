@extends('auth.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
.employee-list-wrapper {
    padding: 25px;
    margin-left: 130px;
    margin-top: 50px;
}
.employee-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}
.employee-card:hover {
    transform: translateY(-2px);
}
.employee-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}
.status-active {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
}
.status-pending {
    background: #ffc107;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
}
</style>

<div class="employee-list-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Employee Details List</h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row g-4">
        @forelse($employees as $employee)
        <div class="col-xl-4 col-md-6">
            <div class="card employee-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $employee->selfie ? asset('storage/' . $employee->selfie) : 'https://via.placeholder.com/60' }}" 
                             alt="Profile" class="employee-avatar me-3">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $employee->first_name }} {{ $employee->last_name }}</h6>
                            <small class="text-muted">{{ $employee->department ?? 'N/A' }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Email:</span>
                            <span class="fw-medium">{{ $employee->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phone:</span>
                            <span class="fw-medium">{{ $employee->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Status:</span>
                            <span class="{{ $employee->is_approved ? 'status-active' : 'status-pending' }}">
                                {{ $employee->is_approved ? 'Active' : 'Pending' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Documents:</span>
                            <span class="fw-medium">
                                @php
                                    $docCount = $employee->documents ? $employee->documents->count() : 0;
                                    $verifiedCount = $employee->documents ? $employee->documents->where('status', 'verified')->count() : 0;
                                @endphp
                                <span class="badge bg-info">{{ $docCount }} Total</span>
                                @if($verifiedCount > 0)
                                    <span class="badge bg-success ms-1">{{ $verifiedCount }} Verified</span>
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Joined:</span>
                            <span class="fw-medium">{{ $employee->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('admin.employees.details', $employee->id) }}" class="btn btn-primary btn-sm me-1">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.employees.document', $employee->id) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-file-earmark-text"></i> Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people" style="font-size: 48px; color: #6c757d;"></i>
                    <h5 class="mt-3 text-muted">No Employees Found</h5>
                    <p class="text-muted">There are no employees to display at the moment.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection