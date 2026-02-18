@extends('auth.layouts.app')
<style>
    .card-header{
        margin-top: 70px;
        display: flex;
    }
    .btn-secondary{
        margin-left: 600px;
    }
</style>
<style>
    
</style>
@section('title', 'Interested Interns')

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Interested Interns</h4>
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
                            <!-- <th>Stipend</th> -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $intern)
                        <tr>
                            <td>{{ $intern->name }}</td>
                            <td>{{ $intern->number }}</td>
                            <td>{{ $intern->role }}</td>
                            <!-- <td>{{ $intern->stipend ? '₹' . number_format($intern->stipend) : 'Not Set' }}</td> -->
                            <td>
                                @if(!$intern->mentor_id)
                                    <a href="{{ route('admin.interns.ongoing', $intern->id) }}" class="btn btn-sm btn-success">Ongoing</a>
                                @else
                                    <span class="badge badge-success">{{ $intern->final_result ?? 'Ongoing' }}</span>
                                    @if($intern->course)
                                        <small class="text-muted d-block">{{ $intern->course }}</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No interested interns found</td>
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

<!-- Ongoing Intern Modal -->
<div class="modal fade" id="ongoingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setup Ongoing Internship</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="ongoingForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="intern_id" name="intern_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Intern Name</label>
                                <input type="text" id="intern_name" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Course/Program *</label>
                                <select name="course" class="form-control" required>
                                    <option value="">Select Course</option>
                                    <option value="Web Development">Web Development</option>
                                    <option value="Mobile App Development">Mobile App Development</option>
                                    <option value="Digital Marketing">Digital Marketing</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="UI/UX Design">UI/UX Design</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select HR for Commission *</label>
                                <select name="hr_id" class="form-control" required>
                                    <option value="">Select HR</option>
                                    @php
                                        $hrs = \App\Models\Employee::where('department', 'HR')->get();
                                    @endphp
                                    @foreach($hrs as $hr)
                                        <option value="{{ $hr->id }}">{{ $hr->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Mentor Teacher *</label>
                                <select name="mentor_id" class="form-control" required>
                                    <option value="">Select Mentor</option>
                                    @php
                                        $mentors = \App\Models\Employee::whereIn('department', ['Development', 'Design', 'Marketing'])->get();
                                    @endphp
                                    @foreach($mentors as $mentor)
                                        <option value="{{ $mentor->id }}">{{ $mentor->full_name }} ({{ $mentor->department }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date *</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Duration (Months) *</label>
                                <select name="internship_duration" class="form-control" required>
                                    <option value="">Select Duration</option>
                                    <option value="1">1 Month</option>
                                    <option value="2">2 Months</option>
                                    <option value="3">3 Months</option>
                                    <option value="6">6 Months</option>
                                    <option value="12">12 Months</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stipend Amount (₹)</label>
                                <input type="number" name="stipend" class="form-control" min="0" placeholder="Enter stipend amount">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Upload Documents</label>
                                <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                <small class="text-muted">Upload resume, certificates, etc. (PDF, DOC, JPG, PNG)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Profile Details</label>
                        <textarea name="profile_details" class="form-control" rows="3" placeholder="Add additional profile information, skills, background, etc."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes/Comments</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes or comments"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Setup Ongoing Internship</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection