@extends('employee.layouts.app')

@section('title', 'My Leaves')
@section('page-title', 'My Leaves')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('employee.leaves.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Request New Leave
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Leave Requests</h5>
        </div>
        <div class="card-body">
            @if($leaves->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Applied On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $leave)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                                <td>{{ $leave->reason }}</td>
                                <td>
                                    @if($leave->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($leave->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $leave->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-4">No leave requests yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
