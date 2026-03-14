@extends('auth.layouts.app')

@section('content')
<style>
.admin-letters-content {
    margin-left: 130px;
}

@media (max-width: 991px) {
    .admin-letters-content {
        margin-left: 0;
    }
}

.letter-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    min-height: 200px;
}

.letter-full-content {
    max-width: 100%;
    margin: 0 auto;
    background: #f4f6f9;
}

.letter-full-content * {
    max-width: 100%;
}





</style>

<div class="admin-letters-content">
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa-solid fa-file-contract me-2"></i>Employee Letters</h5>
            <span class="badge bg-primary">{{ $groupedLetters->sum(fn($letters) => $letters->count()) }} Total Letters</span>
        </div>
        <div class="card-body">
            @if($groupedLetters->count() > 0)
                @foreach($groupedLetters as $email => $letters)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-user me-2"></i>{{ $email }}
                            <span class="badge bg-info ms-2">{{ $letters->count() }} Letters</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject</th>
                                        <th>Preview</th>
                                        <th>Sent Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($letters as $index => $letter)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $letter->subject }}</strong></td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit(strip_tags($letter->content), 40) }}
                                            </small>
                                        </td>
                                        <td>{{ $letter->sent_at->format('d M Y, g:i A') }}</td>
                                        <td>
                                            <span class="badge {{ $letter->status == 'sent' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($letter->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#letterModal{{ $letter->id }}">
                                                <i class="fa-solid fa-eye me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach

                @foreach($groupedLetters as $email => $letters)
                    @foreach($letters as $letter)
                    <div class="modal fade" id="letterModal{{ $letter->id }}" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <div>
                                        <h5 class="modal-title mb-1">{{ $letter->subject }}</h5>
                                        <small class="text-muted">
                                            <strong>To:</strong> {{ $letter->to_email }} | 
                                            <strong>Sent:</strong> {{ $letter->sent_at->format('d M Y, g:i A') }} | 
                                            <span class="badge {{ $letter->status == 'sent' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($letter->status) }}
                                            </span>
                                        </small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0" style="background: #f4f6f9;">
                                    <div class="letter-full-content">
                                        {!! $letter->content !!}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fa-solid fa-times me-1"></i>Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No letters found</h5>
                    <p class="text-muted">No letters have been sent yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
</div>

@endsection
