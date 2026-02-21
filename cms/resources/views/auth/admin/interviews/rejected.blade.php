@extends('auth.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h1>❌ Rejected Interviews ({{ $interviews->total() }})</h1>
        <div class="header-actions">
            <a href="{{ route('admin.interviews.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Interviews
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="content-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Job Role</th>
                        <th>Interview Round</th>
                        <th>Date & Time</th>
                        <th>Interviewer</th>
                        <th>Status</th>
                        <th>Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interviews as $interview)
                        <tr>
                            <td>
                                <strong>{{ $interview->candidate_name }}</strong>
                            </td>
                            <td>{{ $interview->job_role }}</td>
                            <td>
                                <span class="badge badge-info">{{ $interview->interview_round }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($interview->interview_date)->format('M d, Y') }}</strong><br>
                                    <small>{{ date('g:i A', strtotime($interview->start_time)) }} - {{ date('g:i A', strtotime($interview->end_time)) }}</small>
                                </div>
                            </td>
                            <td>{{ $interview->interviewer }}</td>
                            <td>
                                <span class="badge badge-danger">❌ Rejected</span>
                            </td>
                            <td>
                                {{ $interview->rejection_reason ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No rejected interviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $interviews->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.page-header h1 {
    margin: 0;
    font-size: 1.5rem;
}

.content-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
    min-width: 800px;
    margin-bottom: 0;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px 8px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    display: inline-block;
}

.badge-info { background-color: #28a745 !important; color: white !important; }
.badge-danger { background-color: #dc3545 !important; color: white !important; }

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #2eacb3;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
}

.alert {
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
</style>
@endsection
