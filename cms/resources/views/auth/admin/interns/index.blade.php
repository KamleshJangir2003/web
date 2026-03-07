@extends('auth.layouts.app')

@section('title', 'Interns Management')

@section('styles')
<style>
    .main-contentq{
        
        margin-left: 130px;
    }
.interns-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.interns-table {
    width: 100%;
    border-collapse: collapse;
}

.interns-table th,
.interns-table td {
    padding: 5px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.interns-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.status-select {
    padding: 5px 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: white;
}

.whatsapp-btn {
    background-color: #25d366;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
}

.location-btn {
    background-color: #007bff;
    color: white;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
}

.view-btn {
    background-color: #28a745;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
}

.schedule-btn {
    background-color: #17a2b8;
    color: white;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
}

.upload-form {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.search-container {
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-box {
    position: relative;
    flex: 1;
}

.search-box input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 500px;
    border-radius: 8px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}
</style>
<style>
    .card-header{
        
        display: flex;
    }
    .btn-secondary{
        margin-left: 600px;
    }

    /* Top Bar Layout */
.intern-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    flex-wrap: wrap;
}

/* Search Box */
.search-box {
    position: relative;
    width: 280px;
}

.search-box input {
    width: 100%;
    padding: 8px 35px 8px 35px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 14px;
}

.search-box i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}

.clear-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: red;
    text-decoration: none;
}

/* Status Buttons */
.status-buttons {
    display: flex;
    gap: 10px;
}

/* Result Count */
.results-info {
    font-weight: 500;
    color: #555;
}

   
</style>
@endsection

@section('header')
<div class="header-upload">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <form action="{{ route('admin.interns.upload') }}" method="POST" enctype="multipart/form-data" class="upload-form">
        @csrf
        <select name="role" required>
            <option value="">Select Internship Type</option>
            <option value="Web Development">Web Development</option>
            <option value="Mobile Development">Mobile Development</option>
            <option value="Data Science">Data Science</option>
            <option value="Digital Marketing">Digital Marketing</option>
            <option value="UI/UX Design">UI/UX Design</option>
            <option value="Content Writing">Content Writing</option>
        </select>
        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required>
        <button type="submit" class="upload-btn">Upload Excel</button>
        <small class="text-muted">Excel format: Column A = Number, Column B = Name</small>
    </form>
</div>
@endsection

@section('content')
<div class="main-contentq">
    <div class="card interns-card">
        <div class="card-header">
            <h4>Interns List</h4>
           
        </div>

        <div class="card-body">

<div class="intern-top-bar">

    <!-- 🔍 Search -->
    <form method="GET" action="{{ route('admin.interns.index') }}" class="search-form">
        <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" name="search"
                   placeholder="Search by name, number, or role..."
                   value="{{ request('search') }}">
            
            @if(request('search'))
                <a href="{{ route('admin.interns.index') }}" class="clear-btn">✖</a>
            @endif
        </div>
    </form>

    <!-- 🎯 Filter Buttons -->
    <!-- <div class="status-buttons">
        <a href="{{ route('admin.interns.interested') }}"
           class="btn btn-success btn-sm">
            Interested
        </a>

        <a href="{{ route('admin.interns.rejected') }}"
           class="btn btn-danger btn-sm">
            Rejected
        </a>
    </div> -->

    <!-- 📊 Result Count -->
    <div class="results-info">
        <span>{{ $interns->total() }} results</span>
    </div>

</div>

</div>

            

            <div class="table-responsive">
                <table class="interns-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Status</th>
                            <th>Internship Type</th>
                            <th>WhatsApp</th>
                            <th>Send Location</th>
                            <!-- <th>View Profile</th>
                            <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $intern)
                        <tr>
                            <td>{{ $intern->name }}</td>
                            <td>{{ $intern->number }}</td>
                            <td>
    <select class="status-select" data-id="{{ $intern->id }}">
        <option value="" selected>Select Status</option>
        <option value="Not Interested">Not Interested</option>
        <option value="Call Back">Call Back</option>
        <option value="Interested">ShortListed</option>
        <option value="Rejected">Rejected</option>
        <option value="Wrong Number">Wrong Number</option>
    </select>
</td>

                            <td>{{ $intern->role }}</td>
                            <td>
                            @php
$message = "We are pleased to inform you about our Internship Training Program opportunity.\n\n".
"Program Details\n\n".
"💼 Program: {$intern->role} Internship Training Program\n".
"🏤 Company: Kwikster Innovative Optimisations Pvt Ltd.\n".
"📌 Location: 21/284, Kaveri Path, Sector 21, Mansarovar, Jaipur\n\n".
"This program is designed to provide practical industry exposure and hands-on training.\n\n".
"Reply \"Yes\" to know more.\n\n".
"Best Regards,\nHR Team";
@endphp

                                <a href="https://wa.me/91{{ $intern->number }}?text={{ urlencode($message) }}" target="_blank" class="whatsapp-btn">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </td>
                            <td>
                                <a href="https://wa.me/91{{ $intern->number }}?text=https://share.google/j1HSKuOut2VpIweKA" target="_blank" class="location-btn">
                                    <i class="fa-solid fa-location-dot"></i> Location
                                </a>
                            </td>
                           
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;">No interns found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($interns->hasPages())
            <div class="pagination-container">
                {{ $interns->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Mentor Assignment Modal -->
<div id="mentorModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Assign Mentor</h5>
            <span class="close" onclick="closeMentorModal()">&times;</span>
        </div>
        <form id="mentorForm" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Mentor</label>
                    <select name="mentor_id" required>
                        <option value="">Choose Mentor</option>
                        @foreach(\App\Models\Employee::where('user_type', 'employee')->where('is_approved', true)->get() as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Internship Duration (months)</label>
                    <input type="number" name="internship_duration" min="1" max="12" required>
                </div>
                <div class="form-group">
                    <label>Monthly Fees (₹)</label>
                    <input type="number" name="stipend" min="0" step="100">
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Assign Mentor</button>
                <button type="button" class="btn btn-secondary" onclick="closeMentorModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Reason Modal -->
<div id="reasonModal" class="custom-modal">
    <div class="custom-modal-content">

        <div class="custom-modal-header">
            <h5>Status Change Reason</h5>
            <span class="close-btn" onclick="closeReasonModal()">&times;</span>
        </div>

        <div class="custom-modal-body">
            <p>
                Please provide a reason for changing status to 
                <strong id="statusText"></strong>
            </p>

            <textarea id="reasonText" placeholder="Enter reason..." rows="4"></textarea>
        </div>

        <div class="custom-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeReasonModal()">Cancel</button>
            <button type="button" class="btn-save" onclick="saveStatusWithReason()">Save</button>
        </div>

    </div>
</div>
<style>
    /* Overlay */
.custom-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease-in-out;
}

/* Modal Box */
.custom-modal-content {
    background: #ffffff;
    width: 420px;
    max-width: 95%;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease-in-out;
    overflow: hidden;
}

/* Header */
.custom-modal-header {
    background: linear-gradient(135deg, #4e73df, #224abe);
    color: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.custom-modal-header h5 {
    margin: 0;
    font-size: 16px;
}

/* Close button */
.close-btn {
    cursor: pointer;
    font-size: 20px;
}

/* Body */
.custom-modal-body {
    padding: 20px;
}

.custom-modal-body p {
    font-size: 14px;
    margin-bottom: 10px;
    color: #444;
}

.custom-modal-body textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
    resize: none;
    font-size: 14px;
    transition: 0.2s;
}

.custom-modal-body textarea:focus {
    border-color: #4e73df;
    outline: none;
    box-shadow: 0 0 0 2px rgba(78,115,223,0.2);
}

/* Footer */
.custom-modal-footer {
    padding: 15px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f8f9fc;
}

/* Buttons */
.btn-cancel {
    padding: 8px 15px;
    border-radius: 6px;
    border: none;
    background: #858796;
    color: white;
    cursor: pointer;
}

.btn-save {
    padding: 8px 15px;
    border-radius: 6px;
    border: none;
    background: #4e73df;
    color: white;
    cursor: pointer;
}

.btn-save:hover {
    background: #2e59d9;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { transform: translateY(-30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

</style>

<script>
let currentInternId = null;
let currentStatus = null;
let currentRow = null;

function showReasonModal(internId, status, row) {
    currentInternId = internId;
    currentStatus = status;
    currentRow = row;
    
    document.getElementById('statusText').textContent = status;
    document.getElementById('reasonText').value = '';
    document.getElementById('reasonModal').style.display = 'flex';
}

function closeReasonModal() {
    document.getElementById('reasonModal').style.display = 'none';
    if (currentRow) {
        currentRow.querySelector('.status-select').value = '';
    }
    currentInternId = null;
    currentStatus = null;
    currentRow = null;
}

function saveStatusWithReason() {
    const reason = document.getElementById('reasonText').value.trim();
    if (!reason) {
        alert('âš ï¸ Please provide a reason');
        return;
    }
    
    updateInternStatus(currentInternId, currentStatus, reason, currentRow);
    closeReasonModal();
}

function showMentorModal(internId) {
    document.getElementById('mentorForm').action = `/admin/interns/${internId}/assign-mentor`;
    document.getElementById('mentorModal').style.display = 'block';
}

function closeMentorModal() {
    document.getElementById('mentorModal').style.display = 'none';
}

// Status update
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const internId = this.dataset.id;
        const status = this.value;
        const row = this.closest('tr');
        
        if (!status) return;
        
        // Show reason modal for all statuses except Interested
        if (status !== 'Interested') {
            showReasonModal(internId, status, row);
        } else {
            updateInternStatus(internId, status, '', row);
        }
    });
});

function updateInternStatus(internId, status, reason, row) {
    const formData = new FormData();
    formData.append('status', status);
    formData.append('reason', reason);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(`/admin/interns/${internId}/status`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showNotification('Status updated successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
            row.querySelector('.status-select').value = '';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
        row.querySelector('.status-select').value = '';
    });
}
</script>

<script>
// Custom Notification System
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fa ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('show'), 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<style>
.custom-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.3s ease;
    min-width: 300px;
    max-width: 500px;
}

.custom-notification.show {
    opacity: 1;
    transform: translateX(0);
}

.custom-notification.success {
    background: #28a745;
    color: white;
}

.custom-notification.error {
    background: #dc3545;
    color: white;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.notification-content i {
    font-size: 20px;
}
</style>
@endsection