@extends('auth.layouts.app')
<style>
    .card-header{
       
        display: flex;
    }
    .btn-secondary{
        margin-left: 600px;
    }
</style>
@section('title', 'Intern Profiles')

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h4>Intern Profiles</h4>
            <a href="{{ route('admin.interns.index') }}" class="btn btn-secondary">Back to All Interns</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Mentor</th>
                            <th>HR</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $intern)
                        <tr>
                            <td>{{ $intern->name }}</td>
                            <td>{{ $intern->course ?? 'Not Set' }}</td>
                            <td>{{ $intern->mentor->full_name ?? 'Not Assigned' }}</td>
                            <td>{{ $intern->hr->full_name ?? 'Not Assigned' }}</td>
                            <td>
                                @if($intern->final_result == 'Completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($intern->final_result == 'Cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @elseif($intern->final_result == 'Ongoing')
                                    <span class="badge bg-primary">Ongoing</span>
                                @else
                                    <span class="badge bg-info">{{ $intern->final_result ?? 'Active' }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.interns.profile', $intern->id) }}" class="btn btn-sm btn-primary">View Profile</a>
                                @if($intern->final_result == 'Completed' && $intern->certificate_path)
                                    <a href="{{ url('uploads/certificates/' . $intern->certificate_path) }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fa fa-download"></i> Certificate
                                    </a>
                                    <button class="btn btn-sm btn-info send-certificate-btn" data-intern-id="{{ $intern->id }}" data-intern-name="{{ $intern->name }}" data-intern-email="{{ $intern->email }}" data-intern-number="{{ $intern->number }}">
                                        <i class="fa fa-paper-plane"></i> Send
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No intern profiles found</td>
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

<!-- Send Certificate Modal -->
<div class="modal fade" id="sendCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Send certificate to <strong id="internName"></strong></p>
                
                <div class="mb-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sendEmail" checked>
                        <label class="form-check-label" for="sendEmail">
                            Send via Email
                        </label>
                    </div>
                    <input type="email" class="form-control" id="emailInput" placeholder="Enter email address">
                    <small class="text-muted">Current: <span id="internEmail"></span></small>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="sendWhatsApp" checked>
                    <label class="form-check-label" for="sendWhatsApp">
                        Send via WhatsApp (<span id="internNumber"></span>)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSendBtn">Send Certificate</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedInternId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Handle Send Certificate button click
    document.querySelectorAll('.send-certificate-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedInternId = this.dataset.internId;
            const internName = this.dataset.internName;
            const internEmail = this.dataset.internEmail;
            const internNumber = this.dataset.internNumber;
            
            document.getElementById('internName').textContent = internName;
            document.getElementById('internEmail').textContent = internEmail || 'Not available';
            document.getElementById('internNumber').textContent = internNumber || 'Not available';
            
            // Set email input value
            const emailInput = document.getElementById('emailInput');
            emailInput.value = internEmail || '';
            
            // Disable WhatsApp checkbox if number not available
            const whatsappCheckbox = document.getElementById('sendWhatsApp');
            whatsappCheckbox.disabled = !internNumber;
            if (!internNumber) whatsappCheckbox.checked = false;
            
            const modal = new bootstrap.Modal(document.getElementById('sendCertificateModal'));
            modal.show();
        });
    });
    
    // Handle Confirm Send button
    document.getElementById('confirmSendBtn')?.addEventListener('click', function() {
        const sendEmail = document.getElementById('sendEmail').checked;
        const sendWhatsApp = document.getElementById('sendWhatsApp').checked;
        const emailInput = document.getElementById('emailInput').value.trim();
        
        if (!sendEmail && !sendWhatsApp) {
            showNotification('Please select at least one sending method', 'error');
            return;
        }
        
        if (sendEmail && !emailInput) {
            showNotification('Please enter an email address', 'error');
            return;
        }
        
        this.disabled = true;
        this.textContent = 'Sending...';
        
        fetch(`/admin/interns/${selectedInternId}/send-certificate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                send_email: sendEmail,
                send_whatsapp: sendWhatsApp,
                email: emailInput
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // If WhatsApp message is prepared, open WhatsApp
                if (data.whatsapp_message && data.whatsapp_number) {
                    const whatsappUrl = `https://wa.me/${data.whatsapp_number.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(data.whatsapp_message)}`;
                    window.open(whatsappUrl, '_blank');
                }
                
                showNotification(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('sendCertificateModal')).hide();
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to send certificate. Please try again.', 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = 'Send Certificate';
        });
    });
});
</script>
@endsection