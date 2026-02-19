@extends('employee.layouts.app')
<style>
.main-content-wrapper {
    margin-left: 250px; /* Sidebar width ke according adjust karo */
    padding: 20px;
}

@media (max-width: 991px) {
    .main-content-wrapper {
        margin-left: 0;
    }
}

.letter-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    min-height: 200px;
}
</style>

@section('title', 'My Letters')
@section('page-title', 'My Letters')

@section('content')
<div class="main-content-wrapper">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Letters Sent to You</h5>
                    <span class="badge bg-primary">{{ $letters->count() }} Total</span>
                </div>
                <div class="card-body">
                    @if($letters->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject</th>
                                        <th>Sent Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($letters as $index => $letter)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $letter->subject }}</strong>
                                        </td>
                                        <td>{{ $letter->sent_at->format('d M Y, g:i A') }}</td>
                                        <td>
                                            <span class="badge {{ $letter->status == 'sent' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($letter->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#letterModal{{ $letter->id }}">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Letter Modal -->
                                    <div class="modal fade" id="letterModal{{ $letter->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $letter->subject }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <strong>To:</strong> {{ $letter->to_email }}<br>
                                                        <strong>Sent:</strong> {{ $letter->sent_at->format('d M Y, g:i A') }}<br>
                                                        <strong>Status:</strong> 
                                                        <span class="badge {{ $letter->status == 'sent' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ ucfirst($letter->status) }}
                                                        </span>
                                                    </div>
                                                    <hr>
                                                    <div class="letter-content">
                                                        {!! $letter->content !!}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No letters found</h5>
                            <p class="text-muted">You haven't received any letters yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.letter-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    min-height: 200px;
}
</style>
@endsection
