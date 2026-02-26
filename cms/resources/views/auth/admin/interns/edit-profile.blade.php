@extends('auth.layouts.app')

@section('title', 'Edit Intern Profile')

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Edit Profile - {{ $intern->name }}</h4>
            <a href="{{ route('admin.interns.ongoing-list') }}" class="btn btn-secondary">Back to Ongoing Interns</a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.interns.update-profile', $intern->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Intern Name</label>
                            <input type="text" class="form-control" value="{{ $intern->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Course/Program *</label>
                            <select name="course" class="form-control" required>
                                <option value="">Select Course</option>
                                <option value="Web Development" {{ $intern->course == 'Web Development' ? 'selected' : '' }}>Web Development</option>
                                <option value="Mobile App Development" {{ $intern->course == 'Mobile App Development' ? 'selected' : '' }}>Mobile App Development</option>
                                <option value="Digital Marketing" {{ $intern->course == 'Digital Marketing' ? 'selected' : '' }}>Digital Marketing</option>
                                <option value="Data Science" {{ $intern->course == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                <option value="UI/UX Design" {{ $intern->course == 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                <option value="Python Programming" {{ $intern->course == 'Python Programming' ? 'selected' : '' }}>Python Programming</option>
                                <option value="Java Programming" {{ $intern->course == 'Java Programming' ? 'selected' : '' }}>Java Programming</option>
                                <option value="React Development" {{ $intern->course == 'React Development' ? 'selected' : '' }}>React Development</option>
                                <option value="Node.js Development" {{ $intern->course == 'Node.js Development' ? 'selected' : '' }}>Node.js Development</option>
                                <option value="Flutter Development" {{ $intern->course == 'Flutter Development' ? 'selected' : '' }}>Flutter Development</option>
                                <option value="SEO & Content Marketing" {{ $intern->course == 'SEO & Content Marketing' ? 'selected' : '' }}>SEO & Content Marketing</option>
                                <option value="Social Media Marketing" {{ $intern->course == 'Social Media Marketing' ? 'selected' : '' }}>Social Media Marketing</option>
                                <option value="Graphic Design" {{ $intern->course == 'Graphic Design' ? 'selected' : '' }}>Graphic Design</option>
                                <option value="Video Editing" {{ $intern->course == 'Video Editing' ? 'selected' : '' }}>Video Editing</option>
                                <option value="Other" {{ $intern->course == 'Other' ? 'selected' : '' }}>Other</option>
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
                                    <option value="{{ $hr->id }}" {{ $intern->hr_id == $hr->id ? 'selected' : '' }}>{{ $hr->full_name }}</option>
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
                                    $mentors = \App\Models\Employee::whereIn('department', ['Development', 'Design', 'Marketing', 'Training', 'Technical'])->get();
                                @endphp
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}" {{ $intern->mentor_id == $mentor->id ? 'selected' : '' }}>{{ $mentor->full_name }} ({{ $mentor->department }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $intern->start_date ? $intern->start_date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Duration (Months) *</label>
                            <select name="internship_duration" class="form-control" required>
                                <option value="">Select Duration</option>
                                <option value="1" {{ $intern->internship_duration == 1 ? 'selected' : '' }}>1 Month</option>
                                <option value="2" {{ $intern->internship_duration == 2 ? 'selected' : '' }}>2 Months</option>
                                <option value="3" {{ $intern->internship_duration == 3 ? 'selected' : '' }}>3 Months</option>
                                <option value="6" {{ $intern->internship_duration == 6 ? 'selected' : '' }}>6 Months</option>
                                <option value="12" {{ $intern->internship_duration == 12 ? 'selected' : '' }}>12 Months</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Stipend Amount (₹)</label>
                            <input type="number" name="stipend" class="form-control" min="0" value="{{ $intern->stipend }}" placeholder="Enter stipend amount">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="text" class="form-control" value="{{ $intern->end_date ? $intern->end_date->format('d M Y') : 'Auto calculated' }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Profile Details</label>
                    <textarea name="profile_details" class="form-control" rows="3" placeholder="Add additional profile information, skills, background, etc.">{{ $intern->profile_details }}</textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Notes/Comments</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes or comments">{{ $intern->notes }}</textarea>
                </div>
                
                @if($intern->documents)
                <div class="mb-3">
                    <label class="form-label">Uploaded Documents</label>
                    <div class="row">
                        @php
                            $documents = json_decode($intern->documents, true);
                        @endphp
                        @if(isset($documents['aadhar_card']))
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fa-solid fa-id-card fa-2x mb-2 text-primary"></i>
                                    <p class="mb-1"><strong>Aadhar Card</strong></p>
                                    <a href="{{ asset('uploads/intern_documents/' . $documents['aadhar_card']) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($documents['pan_card']))
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fa-solid fa-credit-card fa-2x mb-2 text-success"></i>
                                    <p class="mb-1"><strong>PAN Card</strong></p>
                                    <a href="{{ asset('uploads/intern_documents/' . $documents['pan_card']) }}" target="_blank" class="btn btn-sm btn-success">View</a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($documents['education_document']))
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fa-solid fa-graduation-cap fa-2x mb-2 text-warning"></i>
                                    <p class="mb-1"><strong>Education Document</strong></p>
                                    <a href="{{ asset('uploads/intern_documents/' . $documents['education_document']) }}" target="_blank" class="btn btn-sm btn-warning">View</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Update Profile</button>
                    @if($intern->final_result == 'Ongoing')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#completeModal">Complete Internship</button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Internship</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Internship Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Internship</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="completeForm">
                    <div class="mb-3">
                        <label class="form-label">Completion Date *</label>
                        <input type="date" name="completion_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Performance Rating</label>
                        <select name="performance_rating" class="form-control">
                            <option value="">Select Rating</option>
                            <option value="Excellent">Excellent</option>
                            <option value="Very Good">Very Good</option>
                            <option value="Good">Good</option>
                            <option value="Average">Average</option>
                            <option value="Below Average">Below Average</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sendEmailCheck" checked>
                        <label class="form-check-label" for="sendEmailCheck">Send certificate via Email</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sendWhatsAppCheck" checked>
                        <label class="form-check-label" for="sendWhatsAppCheck">Send certificate via WhatsApp</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCompleteBtn">Complete & Generate Certificate</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Internship Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Internship</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cancelForm">
                    <div class="mb-3">
                        <label class="form-label">Cancellation Date *</label>
                        <input type="date" name="cancellation_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cancellation Reason *</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Confirm Cancellation</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Complete Internship
    document.getElementById('confirmCompleteBtn')?.addEventListener('click', function() {
        const form = document.getElementById('completeForm');
        const formData = new FormData(form);
        
        if (document.getElementById('sendEmailCheck').checked) {
            formData.append('send_email', '1');
        }
        if (document.getElementById('sendWhatsAppCheck').checked) {
            formData.append('send_whatsapp', '1');
        }
        
        this.disabled = true;
        this.textContent = 'Processing...';
        
        fetch('{{ route("admin.interns.complete-internship", $intern->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Open WhatsApp if message is prepared
                if (data.whatsapp_message && data.whatsapp_number) {
                    const whatsappUrl = `https://wa.me/${data.whatsapp_number.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(data.whatsapp_message)}`;
                    window.open(whatsappUrl, '_blank');
                }
                
                alert(data.message + '\n\nCertificate URL: ' + data.certificate_url);
                window.location.href = '{{ route("admin.interns.profiles") }}';
            } else {
                alert('Error: ' + data.message);
                this.disabled = false;
                this.textContent = 'Complete & Generate Certificate';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to complete internship');
            this.disabled = false;
            this.textContent = 'Complete & Generate Certificate';
        });
    });
    
    // Cancel Internship
    document.getElementById('confirmCancelBtn')?.addEventListener('click', function() {
        const form = document.getElementById('cancelForm');
        const formData = new FormData(form);
        
        this.disabled = true;
        this.textContent = 'Processing...';
        
        fetch('{{ route("admin.interns.cancel-internship", $intern->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = '{{ route("admin.interns.profiles") }}';
            } else {
                alert('Error: ' + data.message);
                this.disabled = false;
                this.textContent = 'Confirm Cancellation';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to cancel internship');
            this.disabled = false;
            this.textContent = 'Confirm Cancellation';
        });
    });
});
</script>
@endsection