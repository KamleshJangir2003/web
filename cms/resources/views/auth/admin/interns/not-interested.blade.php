@extends('auth.layouts.app')
<style>
    .card-header{
       
        display: flex;
    }
    .btn-secondary{
        margin-left: 600px;
    }
</style>
@section('title', 'Not Interested Interns')

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Not Interested Interns</h4>
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
                            <td colspan="6" class="text-center">No not interested interns found</td>
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