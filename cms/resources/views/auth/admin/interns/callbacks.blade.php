@extends('auth.layouts.app')

@section('title', 'Intern Callbacks')

@section('content')
<style>
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
}

.table td {
    vertical-align: middle;
    padding: 14px 10px;
    font-size: 14px;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f5faff;
}

.notes-box {
    border-left: 4px solid #4e73df;
    background: #f4f7ff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    max-width: 220px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.status-select {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #dcdcdc;
    font-size: 13px;
    min-width: 140px;
    transition: 0.2s;
}

.status-select:focus {
    border-color: #4e73df;
    outline: none;
    box-shadow: 0 0 0 2px rgba(78,115,223,0.15);
}

.btn-warning {
    background: #ffc107;
    border: none;
    font-weight: 500;
}

.btn-danger {
    background: #dc3545;
    border: none;
    font-weight: 500;
}

.btn-success {
    background: #25d366;
    border: none;
}

.btn-warning:hover,
.btn-danger:hover,
.btn-success:hover {
    opacity: 0.85;
    transition: 0.2s;
}

.btn-secondary {
    border-radius: 6px;
    padding: 8px 14px;
    font-size: 14px;
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fefefe;
    padding: 0;
    border: 1px solid #888;
    width: 500px;
    border-radius: 8px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding: 20px;
    border-top: 1px solid #eee;
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

@media (max-width: 768px) {
    .table td {
        font-size: 12px;
    }

    .notes-box {
        max-width: 150px;
    }
}

.main-content{
    margin-top: 50px;
}
.card-header{
    display: flex;
    justify-content: space-between;
    align-items: center;

}

    /* Mobile Fix */
    @media (max-width:768px){

.main-content{
margin-left:0 !important;
padding-top:20px !important;
margin-top:20px !important;
}

}
</style>

<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h5>Intern Callbacks</h4>
            <a href="{{ route('admin.interns.index') }}" class="btn btn-secondary">Back to Interns</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Internship Type</th>
                            <th>Callback Date</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>WhatsApp</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($callbacks as $callback)
                        <tr>
                            <td>{{ $callback->name }}</td>
                            <td>{{ $callback->number }}</td>
                            <td>{{ $callback->role }}</td>
                            <td>{{ $callback->callback_date->format('d M Y') }}</td>
                            <td>
                                <div class="notes-box">
                                    {{ Str::limit($callback->notes, 30) }}
                                </div>
                            </td>
                            <td>
                                <select class="status-select" data-id="{{ $callback->id }}">
                                    <option value="">Select Status</option>
                                    <option value="interested">ShortListed</option>
                                    <option value="not_interested">Not Interested</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="wrong_number">Wrong Number</option>
                                </select>
                            </td>
                            <td>
                                @php
                                $message = "Follow up for internship opportunity.\n\n".
                                "Position: {$callback->role} Intern\n".
                                "Company: Kwikster Innovative Optimisations Pvt Ltd.\n\n".
                                "Are you still interested?\n\n".
                                "Best Regards,\nHR Team";
                                @endphp
                                <a href="https://wa.me/91{{ $callback->number }}?text={{ urlencode($message) }}" target="_blank" class="btn btn-success btn-sm">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editCallback({{ $callback->id }}, '{{ $callback->callback_date }}', '{{ $callback->notes }}')">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCallback({{ $callback->id }})">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No callbacks found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($callbacks->hasPages())
                {{ $callbacks->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Edit Callback Modal -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Edit Callback</h5>
            <span class="close">&times;</span>
        </div>
        <form id="editForm">
            <div class="modal-body">
                <div class="form-group">
                    <label>Callback Date</label>
                    <input type="date" name="callback_date" required>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentCallbackId = null;

function editCallback(id, date, notes) {
    currentCallbackId = id;
    document.querySelector('#editForm input[name="callback_date"]').value = date;
    document.querySelector('#editForm textarea[name="notes"]').value = notes;
    document.getElementById('editModal').style.display = 'flex';
}

function deleteCallback(id) {
    if (confirm('Are you sure you want to delete this callback?')) {
        fetch(`/admin/interns/callbacks/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Callback deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error deleting callback', 'error');
            }
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentCallbackId = null;
}

// Edit form submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/admin/interns/callbacks/${currentCallbackId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            callback_date: formData.get('callback_date'),
            notes: formData.get('notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Callback updated successfully!', 'success');
            setTimeout(() => {
                closeEditModal();
                location.reload();
            }, 1000);
        } else {
            showNotification('Error updating callback', 'error');
        }
    });
});

// Close modals when clicking outside
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    if (event.target == editModal) {
        closeEditModal();
    }
}

// Status change handler
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const callbackId = this.dataset.id;
        const status = this.value;
        const row = this.closest('tr');
        
        if (!status) return;
        
        const reason = prompt('Please provide a reason for status change:');
        if (!reason) {
            this.value = '';
            return;
        }
        
        fetch(`/admin/interns/callbacks/${callbackId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status, reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Status updated successfully!', 'success');
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        row.remove();
                    }
                }, 1000);
            } else {
                showNotification('Error updating status', 'error');
                this.value = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error occurred', 'error');
            this.value = '';
        });
    });
});
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