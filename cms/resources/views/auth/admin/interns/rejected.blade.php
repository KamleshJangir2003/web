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
padding-top:10px !important;
}

.card-header{
flex-direction:column;
align-items:flex-start;
gap:0px;
}

.btn-secondary{
margin-left:0;
}

}
</style>
@section('title', 'Rejected Interns')

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Rejected Interns</h4>
            <a href="{{ route('admin.interns.index') }}" class="btn btn-secondary">Back to All Interns</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Internship Type</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $intern)
                        <tr>
                            <td>{{ $intern->name }}</td>
                            <td>{{ $intern->number }}</td>
                            <td>{{ $intern->role }}</td>
                            <td>{{ $intern->reason ?: 'Not specified' }}</td>
                            <td>{{ $intern->updated_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.interns.profile', $intern->id) }}" class="btn btn-sm btn-primary">View Profile</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No rejected interns found</td>
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