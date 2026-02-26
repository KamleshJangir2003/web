@extends('auth.layouts.app')

@section('title', 'Interested Interns')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .card-header{
        margin-top: 70px;
        display: flex;
    }
    .btn-secondary{
        margin-left: 600px;
    }
    .badgee{
        color: back;
    }
</style>

<script>
$(document).ready(function() {
    console.log('Script loaded');
    
    $('.status-dropdown').change(function() {
        const internId = $(this).data('intern-id');
        const newStatus = $(this).val();
        const dropdown = $(this);
        
        console.log('Status changed to:', newStatus, 'for intern:', internId);
        
        if(newStatus === 'Rejected') {
            $('#rejectInternId').val(internId);
            $('#rejectionModal').modal('show');
            dropdown.val(dropdown.data('previous-status') || 'Interested');
        } else {
            updateStatus(internId, newStatus, null);
        }
    }).each(function() {
        $(this).data('previous-status', $(this).val());
    });
    
    $('.comment-field').blur(function() {
        const internId = $(this).data('intern-id');
        const comment = $(this).val();
        
        $.ajax({
            url: `/admin/interns/${internId}/comment`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                comment: comment
            },
            success: function(response) {
                console.log('Comment saved');
            },
            error: function(xhr) {
                console.error('Error saving comment');
            }
        });
    });
    
    $('#rejectionForm').submit(function(e) {
        e.preventDefault();
        const internId = $('#rejectInternId').val();
        const reason = $('#rejectionReason').val();
        
        console.log('Submitting rejection for intern:', internId, 'with reason:', reason);
        
        updateStatus(internId, 'Rejected', reason);
    });
    
    function updateStatus(internId, status, reason) {
        console.log('Updating status:', status, 'for intern:', internId);
        
        $.ajax({
            url: `/admin/interns/${internId}/status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                reason: reason
            },
            success: function(response) {
                console.log('Response:', response);
                $('#rejectionModal').modal('hide');
                
                if(response.redirect) {
                    console.log('Redirecting to:', response.redirect);
                    window.location.href = response.redirect;
                } else {
                    alert('Status updated successfully');
                    window.location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                alert('Error updating status');
            }
        });
    }
});
</script>
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
                            <th>Status</th>
                            <th>Comment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $intern)
                        <tr>
                            <td>{{ $intern->name }}</td>
                            <td>{{ $intern->number }}</td>
                            <td>{{ $intern->role }}</td>
                            <td>
                                <select class="form-select form-select-sm status-dropdown" data-intern-id="{{ $intern->id }}">
                                    <option value="Interested" {{ $intern->condition_status == 'Interested' ? 'selected' : '' }}>ShortListed</option>
                                    <option value="Rejected" {{ $intern->condition_status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm comment-field" data-intern-id="{{ $intern->id }}" value="{{ $intern->comment ?? '' }}" placeholder="Add comment...">
                            </td>
                            <td>
                                @if(!$intern->mentor_id)
                                    <a href="{{ route('admin.interns.ongoing', $intern->id) }}" class="btn btn-sm btn-success">Ongoing</a>
                                @else
                                    <span class="badgee badge-success">{{ $intern->final_result ?? 'Ongoing' }}</span>
                                    @if($intern->course)
                                        <small class="text-muted d-block">{{ $intern->course }}</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No interested interns found</td>
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

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejection Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectionForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectInternId">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection *</label>
                        <textarea id="rejectionReason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
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