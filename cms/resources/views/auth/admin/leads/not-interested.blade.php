@extends('auth.layouts.app')
<style>
        /* Mobile Fix */
@media (max-width:768px){

.main-content{
margin-left:0 !important;
padding-top:20px !important;
margin-top:20px !important;
}

}
</style>
@section('content')
<!-- Reason Modal -->
<div id="reasonModal" class="reason-modal">
    <div class="reason-modal-content">
        <div class="reason-modal-header">
            <h5>Change Status</h5>
            <button class="reason-modal-close" onclick="closeReasonModal()">&times;</button>
        </div>
        <div class="reason-modal-body">
            <p id="statusChangeText"></p>
            <label for="reasonInput">Reason <span class="required">*</span></label>
            <textarea id="reasonInput" rows="4" placeholder="Please provide a reason for this status change..."></textarea>
            <small class="text-muted">Minimum 10 characters required</small>
        </div>
        <div class="reason-modal-footer">
            <button class="btn-cancel" onclick="closeReasonModal()">Cancel</button>
            <button class="btn-submit" onclick="submitStatusChange()">Submit</button>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="card leads-card">
        <div class="card-header">
            <h4>Not Interested Leads</h4>
        </div>

        <div class="card-body">
            <!-- Search Bar -->
            <div class="search-container">
                <form method="GET" action="{{ route('admin.leads.not-interested') }}" class="search-form" id="searchForm">
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" name="search" id="searchInput" placeholder="Search by name, number, or role..." value="{{ request('search') }}" autocomplete="off">
                        <!-- <button type="submit" class="search-btn">Search</button> -->
                        @if(request('search'))
                            <a href="{{ route('admin.leads.not-interested') }}" class="clear-btn">Clear</a>
                        @endif
                    </div>
                </form>
                <div class="results-info">
                    <span id="resultsCount">{{ $leads->total() }} results</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="leads-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Updated At</th>
                            <th>WhatsApp</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($leads->items() as $lead)
                        <tr class="lead-row" data-name="{{ strtolower($lead->name) }}" data-number="{{ $lead->number }}" data-role="{{ strtolower($lead->role) }}" data-reason="{{ strtolower($lead->reason ?? '') }}">
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->number }}</td>
                            <td>
                                <div>
                                    <span class="fw-medium">{{ $lead->role }}</span>
                                    @if($lead->platform)
                                        <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $lead->platform)) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <select class="status-select" data-id="{{ $lead->id }}" data-status="not_interested">
                                    <option value="not_interested" selected>Not Interested</option>
                                    <option value="call_backs">Call Backs</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="wrong_number">Wrong Number</option>
                                    <option value="interested">ShortListed</option>
                                </select>
                            </td>
                            <td>{{ $lead->reason ?? 'No reason provided' }}</td>
                            <td>{{ $lead->updated_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="https://wa.me/91{{ $lead->number }}" target="_blank" class="whatsapp-btn">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr id="noResults">
                            <td colspan="7" style="text-align:center;">No not interested leads found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($leads->hasPages())
            <div class="pagination-container">
                {{ $leads->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
let currentSelect = null;
let currentLeadId = null;
let currentNewStatus = null;
let currentOldStatus = null;
let currentStatusText = null;
let currentCandidateName = null;

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function closeReasonModal() {
    document.getElementById('reasonModal').style.display = 'none';
    document.getElementById('reasonInput').value = '';
    if (currentSelect) {
        currentSelect.value = currentOldStatus;
        currentSelect.setAttribute('data-status', currentOldStatus);
    }
}

function submitStatusChange() {
    const reason = document.getElementById('reasonInput').value.trim();
    
    if (!reason || reason.length < 10) {
        showNotification('Please provide a reason (minimum 10 characters)', 'error');
        return;
    }
    
    closeReasonModal();
    
    fetch(`/admin/leads/${currentLeadId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            condition_status: currentNewStatus === 'call_backs' ? 'Call Back' : 
                             currentNewStatus === 'rejected' ? 'Rejected' : 
                             currentNewStatus === 'not_interested' ? 'Not Interested' : 
                             currentNewStatus === 'wrong_number' ? 'Wrong Number' : 
                             currentNewStatus === 'interested' ? 'Interested' : currentNewStatus,
            reason: reason
        })
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        if(data.success) {
            currentSelect.setAttribute('data-status', currentNewStatus);
            showNotification(currentCandidateName + '\'s status changed to ' + currentStatusText, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            currentSelect.value = currentOldStatus;
            currentSelect.setAttribute('data-status', currentOldStatus);
            showNotification(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        currentSelect.value = currentOldStatus;
        currentSelect.setAttribute('data-status', currentOldStatus);
        showNotification('Error: ' + error.message, 'error');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const resultsCount = document.getElementById('resultsCount');
    
    let searchTimeout;
    
    // Status change functionality
    document.querySelectorAll('.status-select').forEach(select => {
        select.setAttribute('data-status', select.value);
        
        select.addEventListener('change', function() {
            currentSelect = this;
            currentLeadId = this.dataset.id;
            currentNewStatus = this.value;
            currentOldStatus = this.getAttribute('data-status');
            currentStatusText = this.options[this.selectedIndex].text;
            const row = this.closest('tr');
            currentCandidateName = row.querySelector('td:first-child').textContent;
            
            document.getElementById('statusChangeText').textContent = 
                `You are changing ${currentCandidateName}'s status to "${currentStatusText}"`;
            document.getElementById('reasonModal').style.display = 'flex';
            document.getElementById('reasonInput').focus();
        });
    });
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('.lead-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const number = row.dataset.number || '';
                const role = row.dataset.role || '';
                const reason = row.dataset.reason || '';
                
                const isMatch = name.includes(searchTerm) || 
                               number.includes(searchTerm) || 
                               role.includes(searchTerm) || 
                               reason.includes(searchTerm);
                
                if (isMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            resultsCount.textContent = `${visibleCount} results`;
            
            if (visibleCount === 0 && searchTerm) {
                if (!document.querySelector('#noSearchResults')) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noSearchResults';
                    noResultsRow.innerHTML = '<td colspan="7" style="text-align:center;">No results found for your search</td>';
                    tableBody.appendChild(noResultsRow);
                }
            } else {
                const noSearchResults = document.querySelector('#noSearchResults');
                if (noSearchResults) {
                    noSearchResults.remove();
                }
            }
        }, 300);
    });
});
</script>

<style>
/* ================= REASON MODAL ================= */
.reason-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    align-items: center;
    justify-content: center;
}

.reason-modal-content {
    background: #fff;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.reason-modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.reason-modal-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.reason-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #999;
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.reason-modal-close:hover {
    background: #f0f0f0;
    color: #333;
}

.reason-modal-body {
    padding: 20px;
}

.reason-modal-body p {
    margin: 0 0 15px 0;
    color: #555;
    font-size: 14px;
}

.reason-modal-body label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.required {
    color: #dc3545;
}

.reason-modal-body textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.2s;
}

.reason-modal-body textarea:focus {
    outline: none;
    border-color: #2eacb3;
    box-shadow: 0 0 0 3px rgba(46,172,179,0.1);
}

.reason-modal-body small {
    display: block;
    margin-top: 5px;
    color: #999;
    font-size: 12px;
}

.reason-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-cancel, .btn-submit {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel {
    background: #f0f0f0;
    color: #666;
}

.btn-cancel:hover {
    background: #e0e0e0;
}

.btn-submit {
    background: #2eacb3;
    color: #fff;
}

.btn-submit:hover {
    background: #268a8f;
}

/* ================= EXISTING STYLES ================= */
.leads-card {
    max-width: 1200px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.card-header h4 {
    margin: 0;
    font-weight: 600;
}

.search-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 15px;
}

.search-form {
    flex: 1;
    max-width: 400px;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 14px;
    z-index: 2;
}

.search-box input {
    flex: 1;
    padding: 10px 12px 10px 35px;
    border: 1px solid #ddd;
    border-radius: 30px;

    font-size: 14px;
    background: #fff;
    border-right: none;
}

.search-btn {
    padding: 10px 16px;
    background: #2eacb3;
    color: #fff;
    border: 1px solid #2eacb3;
    border-radius: 0;
    font-size: 14px;
    cursor: pointer;
}

.clear-btn {
    padding: 10px 12px;
    background: #6c757d;
    color: #fff;
    text-decoration: none;
    border-radius: 0 8px 8px 0;
    font-size: 14px;
}

.table-responsive {
    overflow-x: auto;
}

.leads-table {
    width: 100%;
    border-collapse: collapse;
}

.leads-table th {
    background: #f1f3f5;
    padding: 14px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
}

.leads-table td {
    padding: 14px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
    font-size: 14px;
}

.leads-table tr:hover {
    background: #f9fafb;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.status-select {
    padding: 6px 30px 6px 12px;
    font-size: 12px;
    border-radius: 20px;
    border: 1px solid #ddd;
    outline: none;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    font-weight: 500;
    min-width: 140px;
    transition: all 0.3s ease;
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 12px;
    background-image: url("data:image/svg+xml;utf8,<svg fill='%23666' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M5 7l5 5 5-5H5z'/></svg>");
}

.status-select[data-status="not_interested"] {
    background-color: #e2e3e5;
    color: #383d41;
}

.status-select[data-status="call_backs"] {
    background-color: #fff3cd;
    color: #856404;
}

.status-select[data-status="interested"] {
    background-color: #d4edda;
    color: #155724;
}

.status-select[data-status="rejected"] {
    background-color: #f8d7da;
    color: #721c24;
}

.status-select[data-status="wrong_number"] {
    background-color: #cce5ff;
    color: #004085;
}

.whatsapp-btn {
    background: #25D366;
    color: #fff;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    font-size: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.whatsapp-btn:hover {
    background: #1ebe5d;
    color: #fff;
}



.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    z-index: 9999;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.3s ease;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification.success {
    background: #10b981;
}

.notification.error {
    background: #ef4444;
}

.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
</style>
@endsection