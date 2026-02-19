@extends('auth.layouts.app')

@section('title', 'Setup Ongoing Internship')
<style>
    .main-content{
        margin-top: 70px;
    }
    .card-header{
        display: flex;
        
    }
    .btn-secondary{
        margin-left: 400px;
    }
</style>
@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Setup Ongoing Internship - {{ $intern->name }}</h4>
            <a href="{{ route('admin.interns.interested') }}" class="btn btn-secondary">Back to Interested Interns</a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.interns.setup-ongoing', $intern->id) }}" method="POST" enctype="multipart/form-data">
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
                                <option value="Web Development">Web Development</option>
                                <option value="Mobile App Development">Mobile App Development</option>
                                <option value="Digital Marketing">Digital Marketing</option>
                                <option value="Data Science">Data Science</option>
                                <option value="UI/UX Design">UI/UX Design</option>
                                <option value="Python Programming">Python Programming</option>
                                <option value="Java Programming">Java Programming</option>
                                <option value="React Development">React Development</option>
                                <option value="Node.js Development">Node.js Development</option>
                                <option value="Flutter Development">Flutter Development</option>
                                <option value="SEO & Content Marketing">SEO & Content Marketing</option>
                                <option value="Social Media Marketing">Social Media Marketing</option>
                                <option value="Graphic Design">Graphic Design</option>
                                <option value="Video Editing">Video Editing</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Select HR for Commission *</label>
                            <div class="d-flex gap-2">
                                <select name="hr_id" id="hr_select" class="form-control" required>
                                    <option value="">Select HR</option>
                                    @php
                                        $hrs = \App\Models\Employee::where('department', 'HR')->get();
                                    @endphp
                                    @foreach($hrs as $hr)
                                        <option value="{{ $hr->id }}">{{ $hr->full_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addHrModal">
                                    +New
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Select Mentor Teacher *</label>
                            <div class="d-flex gap-2">
                                <select name="mentor_id" id="mentor_select" class="form-control" required>
                                    <option value="">Select Mentor</option>
                                    @php
                                        $mentors = \App\Models\Employee::whereIn('department', ['Development', 'Design', 'Marketing', 'Training', 'Technical'])->get();
                                    @endphp
                                    @foreach($mentors as $mentor)
                                        <option value="{{ $mentor->id }}">{{ $mentor->full_name }} ({{ $mentor->department }})</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMentorModal">
                                    +New
                                </button>
                            </div>
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
                            <label class="form-label">Total Training Amount (₹)</label>
                            <input type="number" name="stipend" class="form-control" min="0" placeholder="Enter Amount">
                        </div>
                    </div>
                </div>
                
                <!-- Document Upload Section -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Aadhar Card *</label>
                            <input type="file" name="aadhar_card" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required>
                            <small class="text-muted">Upload Aadhar Card (PDF, DOC, JPG, PNG)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">PAN Card *</label>
                            <input type="file" name="pan_card" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required>
                            <small class="text-muted">Upload PAN Card (PDF, DOC, JPG, PNG)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Last Education Document *</label>
                            <input type="file" name="education_document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required>
                            <small class="text-muted">Upload Last Education Certificate (PDF, DOC, JPG, PNG)</small>
                        </div>
                    </div>
                </div>
                
                <!-- <div class="mb-3">
                    <label class="form-label">Profile Details</label>
                    <textarea name="profile_details" class="form-control" rows="3" placeholder="Add additional profile information, skills, background, etc."></textarea>
                </div> -->
                
                <div class="mb-3">
                    <label class="form-label">Notes/Comments</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes or comments"></textarea>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Setup Ongoing Internship</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add HR Modal -->
<div class="modal fade" id="addHrModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New HR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addHrForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <input type="hidden" name="department" value="HR">
                    <input type="hidden" name="user_type" value="employee">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addHr()">Add HR</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Mentor Modal -->
<div class="modal fade" id="addMentorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Mentor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMentorForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <!-- <div class="mb-3">
                        <label class="form-label">Department *</label>
                        <select name="department" class="form-control" required>
                            <option value="">Select Department</option>
                            <option value="Development">Development</option>
                            <option value="Design">Design</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Training">Training</option>
                            <option value="Technical">Technical</option>
                        </select>
                    </div> -->
                    <input type="hidden" name="user_type" value="employee">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addMentor()">Add Mentor</button>
            </div>
        </div>
    </div>
</div>

<script>
function addHr() {
    const form = document.getElementById('addHrForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.employees.quick-add") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new option to HR select
            const hrSelect = document.getElementById('hr_select');
            const newOption = new Option(data.employee.full_name, data.employee.id);
            hrSelect.add(newOption);
            hrSelect.value = data.employee.id;
            
            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('addHrModal'));
            modal.hide();
            form.reset();
            
            alert('HR added successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding HR');
    });
}

function addMentor() {
    const form = document.getElementById('addMentorForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.employees.quick-add") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new option to Mentor select
            const mentorSelect = document.getElementById('mentor_select');
            const newOption = new Option(data.employee.full_name + ' (' + data.employee.department + ')', data.employee.id);
            mentorSelect.add(newOption);
            mentorSelect.value = data.employee.id;
            
            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('addMentorModal'));
            modal.hide();
            form.reset();
            
            alert('Mentor added successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding Mentor');
    });
}
</script>

</div>
@endsection