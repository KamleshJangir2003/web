@extends('auth.layouts.app')
<style>
    .action-buttons {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}

.action-btn {
    border-radius: 20px;
    font-size: 12px;
    padding: 4px 10px;
}

.dropdown-menu {
    border-radius: 10px;
    padding: 6px 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.dropdown-item {
    font-size: 13px;
    padding: 8px 14px;
}

.dropdown-item:hover {
    background-color: #f1fdf4;
}

</style>
@section('content')
<div class="main-content">
    <div class="page-header">
        <h1></h1>
        <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Interviews
        </a>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                    <th>Employee Name</th>
                        <th>Job Role</th>
                        <th>Interview Round</th>
                        <th>Selected Date</th>
                        <th>Interviewer</th>
                        
                        <th>Contact</th>
                        <th>Joining Date</th>
                        
                        <th>In Hand</th>
                        <th>CTC</th>
                        
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($selectedInterviews as $interview)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $interview->candidate_name }}</strong><br>
                                    <!-- <small class="text-muted">ID: #{{ $interview->lead_id }}</small> -->
                                </div>
                            </td>
                            <td>
                                <span class="job-role">{{ $interview->job_role }}</span>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $interview->interview_round }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ formatDate($interview->updated_at) }}</strong><br>
                                    <small>{{ $interview->updated_at->format('g:i A') }}</small>
                                </div>
                            </td>
                            <td>{{ $interview->interviewer }}</td>
                            <td>
                                <div>
                                    <small> {{ $interview->candidate_email }}</small><br>
                                    @if($interview->lead && $interview->lead->number)
                                        <small> {{ $interview->lead->number }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <input type="date" class="form-control form-control-sm" 
                                       id="joining_date_{{ $interview->id }}" 
                                       value="{{ $interview->joining_date ?? '' }}"
                                       style="width: 140px; font-size: 12px;">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" 
                                       id="in_hand_salary_{{ $interview->id }}" 
                                       value="{{ $interview->in_hand_salary ?? '' }}"
                                       placeholder="In Hand" 
                                       style="width: 100px; font-size: 12px;">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" 
                                       id="current_ctc_{{ $interview->id }}" 
                                       value="{{ $interview->current_ctc ?? '' }}"
                                       placeholder="CTC" 
                                       style="width: 100px; font-size: 12px;">
                            </td>
                            
                            <td>
    <div class="action-buttons">
        <span class="badge badge-success">✅ Selected</span>

        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle action-btn"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                Actions
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                       href="javascript:void(0)"
                       onclick="saveEmploymentDetails({{ $interview->id }})">
                        <i class="fas fa-save me-2 text-success"></i> Save Details
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                       href="javascript:void(0)"
                       onclick="sendWelcomeLetter({{ $interview->id }})">
                        <i class="fas fa-envelope me-2 text-primary"></i> Send Welcome Letter
                    </a>
                </li>
            </ul>
        </div>
    </div>
</td>

                        </tr>
                    @empty
                    @endforelse
                    
                    @forelse($directEmployees as $employee)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}</strong><br>
                                    <small class="text-muted">Direct Add</small>
                                </div>
                            </td>
                            <td>
                                <span class="job-role">{{ $employee->department }}</span>
                            </td>
                            <td>
                                <span class="badge badge-info">Direct Entry</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ formatDate($employee->created_at) }}</strong><br>
                                    <small>{{ $employee->created_at->format('g:i A') }}</small>
                                </div>
                            </td>
                            <td>-</td>
                            <td>
                                <div>
                                    <small>{{ $employee->email }}</small><br>
                                    @if($employee->phone)
                                        <small>{{ $employee->phone }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <input type="date" class="form-control form-control-sm" 
                                       id="emp_joining_date_{{ $employee->id }}" 
                                       value="{{ $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '' }}"
                                       style="width: 140px; font-size: 12px;">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" 
                                       id="emp_current_ctc_{{ $employee->id }}" 
                                       value="{{ $employee->current_ctc ?? '' }}"
                                       placeholder="CTC" 
                                       style="width: 100px; font-size: 12px;">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" 
                                       id="emp_in_hand_salary_{{ $employee->id }}" 
                                       value="{{ $employee->in_hand_salary ?? '' }}"
                                       placeholder="In Hand" 
                                       style="width: 100px; font-size: 12px;">
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <span class="badge badge-success">✅ Selected</span>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle action-btn"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Actions
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                   href="javascript:void(0)"
                                                   onclick="saveDirectEmployeeDetails({{ $employee->id }})">
                                                    <i class="fas fa-save me-2 text-success"></i> Save Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                   href="javascript:void(0)"
                                                   onclick="sendDirectWelcomeLetter({{ $employee->id }})">
                                                    <i class="fas fa-envelope me-2 text-primary"></i> Send Welcome Letter
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @if($selectedInterviews->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>No Selected Employees</h5>
                                    <p class="text-muted">No employees have been selected from interviews yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    margin: 0;
    font-size: 1.5rem;
    color: #28a745;
}

.content-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
   
    margin-bottom: 0;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px 10px;
    font-size: 13px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    display: inline-block;
}

.badge-info { background-color: #28a745 !important; color: white !important; }
.badge-success { background-color: #28a745; color: white; }

.job-role {
    font-weight: 500;
    color: #495057;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    transition: all 0.3s ease;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
    transform: translateY(-1px);
}

.empty-state {
    padding: 40px 20px;
    text-align: center;
}

.empty-state i {
    opacity: 0.3;
}

.empty-state h5 {
    margin-bottom: 10px;
    color: #6c757d;
}

.text-muted {
    color: #6c757d !important;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
}

.btn-primary {
    background-color: #2eacb3;
    color: white;
    font-size: 11px;
    padding: 4px 8px;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-sm {
    font-size: 11px;
    padding: 4px 8px;
}

.btn-success {
    background-color: #28a745;
    color: white;
    font-size: 11px;
    padding: 4px 8px;
}

.btn-success:hover {
    background-color: #1e7e34;
}

.form-control-sm {
    padding: 2px 6px;
    font-size: 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.text-success {
    color: #28a745 !important;
    font-weight: 500;
}
</style>
@endsection

@section('scripts')
<script>
function showNotif(msg, type='success') {
    const n = document.createElement('div');
    n.style.cssText = `position:fixed;top:20px;right:20px;background:${type==='error'?'#dc3545':type==='warning'?'#ffc107':'#28a745'};color:white;padding:15px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.3);z-index:9999;font-size:14px;max-width:350px;white-space:pre-line`;
    n.textContent = msg;
    document.body.appendChild(n);
    setTimeout(() => n.remove(), 3500);
}

function showConfirm(msg, onYes) {
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:10000;display:flex;align-items:center;justify-content:center">
            <div style="background:white;padding:25px;border-radius:10px;max-width:400px;text-align:center">
                <div style="font-size:40px;margin-bottom:15px">❓</div>
                <p style="margin-bottom:20px;color:#333;font-size:15px">${msg}</p>
                <div style="display:flex;gap:10px;justify-content:center">
                    <button onclick="this.closest('div').parentElement.parentElement.remove()" style="padding:10px 20px;background:#6c757d;color:white;border:none;border-radius:5px;cursor:pointer">Cancel</button>
                    <button id="confirmYes" style="padding:10px 20px;background:#28a745;color:white;border:none;border-radius:5px;cursor:pointer">Yes, Send</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    modal.querySelector('#confirmYes').onclick = () => { modal.remove(); onYes(); };
}

function saveEmploymentDetails(interviewId) {
    const joiningDate = document.getElementById(`joining_date_${interviewId}`).value;
    const currentCtc = document.getElementById(`current_ctc_${interviewId}`).value;
    const inHandSalary = document.getElementById(`in_hand_salary_${interviewId}`).value;
    
    if (!joiningDate || !currentCtc || !inHandSalary) {
        showNotif('Please fill all employment details\n\nRequired fields:\n• Joining Date\n• CTC\n• In Hand Salary', 'warning');
        return;
    }
    
    fetch(`/admin/interviews/${interviewId}/employment-details`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            joining_date: joiningDate,
            current_ctc: currentCtc,
            in_hand_salary: inHandSalary
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotif('Employment details saved successfully!');
        } else {
            showNotif('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showNotif('Error saving employment details', 'error');
        console.error(error);
    });
}

function saveDirectEmployeeDetails(employeeId) {
    const joiningDate = document.getElementById(`emp_joining_date_${employeeId}`).value;
    const currentCtc = document.getElementById(`emp_current_ctc_${employeeId}`).value;
    const inHandSalary = document.getElementById(`emp_in_hand_salary_${employeeId}`).value;
    
    if (!joiningDate || !currentCtc || !inHandSalary) {
        showNotif('Please fill all employment details\n\nRequired fields:\n• Joining Date\n• CTC\n• In Hand Salary', 'warning');
        return;
    }
    
    fetch(`/admin/employees/${employeeId}/update-details`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            joining_date: joiningDate,
            current_ctc: currentCtc,
            in_hand_salary: inHandSalary
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotif('Employee details saved successfully!');
        } else {
            showNotif('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showNotif('Error saving employee details', 'error');
        console.error(error);
    });
}

function sendDirectWelcomeLetter(employeeId) {
    const joiningDate = document.getElementById(`emp_joining_date_${employeeId}`).value;
    const currentCtc = document.getElementById(`emp_current_ctc_${employeeId}`).value;
    const inHandSalary = document.getElementById(`emp_in_hand_salary_${employeeId}`).value;
    
    if (!joiningDate || !currentCtc || !inHandSalary) {
        showNotif('Please save employment details first!\n\nYou need to:\n1. Fill Joining Date, CTC, and In Hand Salary\n2. Click "Save Details" button\n3. Then send welcome letter', 'warning');
        return;
    }
    
    // Check if values are actually saved (not just filled)
    fetch(`/admin/employees/${employeeId}/check-details`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(checkData => {
        if (!checkData.details_saved) {
            showNotif('Please click "Save Details" button first before sending welcome letter!', 'warning');
            return;
        }
        
        showConfirm('Send welcome letter to this employee?', () => {
        const loadingModal = document.createElement('div');
        loadingModal.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1001; display: flex; align-items: center; justify-content: center;">
                <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
                    <div style="margin-bottom: 15px;">📧</div>
                    <h4 style="margin-bottom: 10px;">Sending Welcome Letter...</h4>
                    <p style="color: #666;">Please wait while we send the welcome letter.</p>
                </div>
            </div>
        `;
        document.body.appendChild(loadingModal);
        
        fetch(`/admin/employees/${employeeId}/send-welcome`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                joining_date: joiningDate,
                current_ctc: currentCtc,
                in_hand_salary: inHandSalary
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.remove();
            
            if (data.success) {
                showNotif('Welcome letter sent successfully!');
                location.reload();
            } else {
                showNotif('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            loadingModal.remove();
            showNotif('Error sending welcome letter', 'error');
            console.error(error);
        });
        });
    })
    .catch(error => {
        showNotif('Error checking details status', 'error');
        console.error(error);
    });
}

function sendWelcomeLetter(interviewId) {
    const joiningDate = document.getElementById(`joining_date_${interviewId}`).value;
    const currentCtc = document.getElementById(`current_ctc_${interviewId}`).value;
    const inHandSalary = document.getElementById(`in_hand_salary_${interviewId}`).value;
    
    if (!joiningDate || !currentCtc || !inHandSalary) {
        showNotif('Please save employment details first before sending welcome letter!', 'warning');
        return;
    }
    
    const candidateName = document.querySelector(`#joining_date_${interviewId}`).closest('tr').querySelector('strong').textContent;
    showConfirm(`Send welcome letter to ${candidateName}?`, () => {
    const loadingModal = document.createElement('div');
    loadingModal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1001; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
                <div style="margin-bottom: 15px;">📧</div>
                <h4 style="margin-bottom: 10px;">Sending Welcome Letter...</h4>
                <p style="color: #666;">Please wait while we send the welcome letter.</p>
            </div>
        </div>
    `;
    document.body.appendChild(loadingModal);
    
    fetch(`/admin/interviews/${interviewId}/welcome-letter`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            joining_date: joiningDate,
            current_ctc: currentCtc,
            in_hand_salary: inHandSalary
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.remove();
        
        if (data.success) {
            showNotif('Welcome letter sent successfully!');
            location.reload();
        } else {
            showNotif('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        loadingModal.remove();
        showNotif('Error sending welcome letter', 'error');
        console.error(error);
    });
    });
}
</script>
@endsection