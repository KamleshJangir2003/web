@extends('auth.layouts.app')
<style>
   .card-header{
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
}

.btn-secondary{
margin-left:auto;
}

/* Mobile Fix */
@media (max-width:768px){

.main-content{
margin-left:0 !important;
padding-top:0px !important;
}

.card-header{
flex-direction:column;
align-items:flex-start;
gap:10px;
}

.btn-secondary{
margin-left:0;
}

}
</style>
@section('title', 'Ongoing Interns')

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Ongoing Interns</h4>
            <a href="{{ route('admin.interns.index') }}" class="btn btn-secondary">Back to All Interns</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Mentor</th>
                            <th>HR</th>
                            <th>Start Date</th>
                            <th>Duration</th>
                            <th>Stipend</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $intern)
                        <tr>
                            <td>{{ $intern->name }}</td>
                            <td>{{ $intern->course ?? 'Not Set' }}</td>
                            <td>{{ $intern->mentor->full_name ?? 'Not Assigned' }}</td>
                            <td>{{ $intern->hr->full_name ?? 'Not Assigned' }}</td>
                            <td>{{ $intern->start_date ? $intern->start_date->format('d M Y') : 'Not Set' }}</td>
                            <td>{{ $intern->internship_duration ? $intern->internship_duration . ' months' : 'Not Set' }}</td>
                            <td>{{ $intern->stipend ? '₹' . number_format($intern->stipend) : 'Not Set' }}</td>
                            <td>
                                @if($intern->final_result == 'Completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($intern->final_result == 'Cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-primary">Ongoing</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.interns.edit-profile', $intern->id) }}" class="btn btn-sm btn-primary">Edit Profile</a>
                                <a href="{{ route('admin.interns.payment', $intern->id) }}" class="btn btn-sm btn-warning">Payment</a>
                                @if($intern->final_result == 'Completed' && $intern->certificate_path)
                                    <a href="{{ url('uploads/certificates/' . $intern->certificate_path) }}" class="btn btn-sm btn-success" target="_blank">Download Certificate</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No ongoing interns found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($interns->hasPages())
                {{ $interns->links() }}
            @endif
        </div>
    </div>
</div>
@endsection