@extends('auth.layouts.app')
<style>
    .main-content{
        padding-top: 30px;
    }
    .card-header{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@section('title', 'Intern Profile - ' . $intern->name)

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Intern Profile: {{ $intern->name }}</h4>
            <a href="{{ route('admin.interns.index') }}" class="btn btn-secondary">Back to Interns</a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $intern->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $intern->number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $intern->email ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Internship Type:</strong></td>
                            <td>{{ $intern->role }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge badge-{{ $intern->condition_status == 'Interested' ? 'success' : 'secondary' }}">
                                    {{ $intern->condition_status ?: 'Pending' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Internship Details</h5>
                    <table class="table">
                        <tr>
                            <td><strong>Duration:</strong></td>
                            <td>{{ $intern->internship_duration ? $intern->internship_duration . ' months' : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Start Date:</strong></td>
                            <td>{{ $intern->start_date ? $intern->start_date->format('d M Y') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>End Date:</strong></td>
                            <td>{{ $intern->end_date ? $intern->end_date->format('d M Y') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Stipend:</strong></td>
                            <td>{{ $intern->stipend ? '₹' . number_format($intern->stipend) : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mentor:</strong></td>
                            <td>{{ $intern->mentor ? $intern->mentor->first_name . ' ' . $intern->mentor->last_name : 'Not assigned' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Resume Upload</h5>
                    @if($intern->resume)
                        <p>Current Resume: <a href="{{ asset('uploads/intern_resumes/' . $intern->resume) }}" target="_blank">{{ $intern->resume }}</a></p>
                    @endif
                    
                    <form action="{{ route('admin.interns.resume.upload', $intern->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
                            <button type="submit" class="btn btn-primary">Upload Resume</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection