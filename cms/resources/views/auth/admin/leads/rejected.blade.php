@extends('auth.layouts.app')
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
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
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

.reason-modal-close:hover { background: #f0f0f0; color: #333; }

.reason-modal-body { padding: 20px; }
.reason-modal-body p { margin: 0 0 15px 0; color: #555; font-size: 14px; }
.reason-modal-body label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px; }
.required { color: #dc3545; }

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

.reason-modal-body small { display: block; margin-top: 5px; color: #999; font-size: 12px; }

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

.btn-cancel { background: #f0f0f0; color: #666; }
.btn-cancel:hover { background: #e0e0e0; }
.btn-submit { background: #2eacb3; color: #fff; }
.btn-submit:hover { background: #268a8f; }

    /* ===============================
   STATUS POPUP NOTIFICATION
================================ */

.header-top {
    display: flex;
    justify-content: center;
    align-items: center;
}

.status-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 20px;
    min-width: 300px;
    z-index: 10000;
    transform: translateX(400px);
    opacity: 0;
    transition: all 0.4s ease;
    border-left: 4px solid #28a745;
}

.status-popup.show {
    transform: translateX(0);
    opacity: 1;
}

.status-popup.success {
    border-left-color: #28a745;
}

.status-popup.error {
    border-left-color: #dc3545;
}

.status-popup.warning {
    border-left-color: #ffc107;
}

.status-popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.status-popup-title {
    font-weight: 600;
    color: #333;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-popup-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.status-popup-close:hover {
    background: #f0f0f0;
    color: #333;
}

.status-popup-message {
    color: #666;
    font-size: 14px;
    line-height: 1.4;
}

.status-popup-details {
    margin-top: 8px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
    font-size: 13px;
    color: #555;
}

/* ===============================
   STATUS DROPDOWN DESIGN
================================ */

.callback-status-select {
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
}

/* Dropdown Arrow */
.callback-status-select {
    background-image: url("data:image/svg+xml;utf8,<svg fill='%23666' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M5 7l5 5 5-5H5z'/></svg>");
}

/* STATUS COLORS */

.callback-status-select[data-status="call_backs"],
.callback-status-select option[value="call_backs"] {
    background-color: #fff3cd;
    color: #856404;
}

.callback-status-select[data-status="interested"] {
    background-color: #d4edda;
    color: #155724;
}

.callback-status-select[data-status="rejected"] {
    background-color: #f8d7da;
    color: #721c24;
}

.callback-status-select[data-status="not_interested"] {
    background-color: #e2e3e5;
    color: #383d41;
}

.callback-status-select[data-status="wrong_number"] {
    background-color: #cce5ff;
    color: #004085;
}

.callback-status-select:focus {
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

/* ===============================
   MODERN SEARCH DESIGN
================================ */

/* ===============================
   PREMIUM HRMS SEARCH BAR
================================ */

.search-form {
    flex: 1;
    max-width: 500px;
}

.search-box {
    position: relative;
    width: 80%;
}

.search-box input {
    width: 80%;
    height: 42px;
    padding: 0 45px 0 40px;
    border-radius: 30px !important; /* Full round */
    border: 1px solid #e0e6ed !important;
    background: #f8fafc;
    font-size: 14px;
    transition: all 0.3s ease;
}


/* Focus Effect */
.search-box input:focus {
    background: #ffffff;
    border-color: #4f46e5;
    box-shadow: 0 4px 12px rgba(79,70,229,0.15);
    outline: none;
}

/* Search Icon */
.search-box i.fa-search {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

/* Clear Button */
.clear-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #eef2ff;
    color: #4f46e5;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s ease;
}

.clear-btn:hover {
    background: #4f46e5;
    color: #fff;
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
            <h4>Rejected Leads</h4>
            <div class="search-container">
                <form method="GET" action="{{ route('admin.leads.rejected') }}" class="search-form" id="searchForm">
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" name="search" id="searchInput" placeholder="Search by name, number, role, status, or reason..." value="{{ request('search') }}" autocomplete="off">
                        <!-- <button type="submit" class="search-btn">Search</button> -->
                        @if(request('search'))
                            <a href="{{ route('admin.leads.rejected') }}" class="clear-btn">Clear</a>
                        @endif
                    </div>
                </form>
                <div class="results-info">
                    <span id="resultsCount">{{ $leads->total() }} results</span>
                </div>
            </div>
        </div>

        <div class="card-body">
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($leads as $lead)
                        <tr class="lead-row" data-name="{{ strtolower($lead->platform === 'indeed' ? $lead->number : $lead->name) }}" data-number="{{ $lead->platform === 'indeed' ? str_replace(["'+91 ", " "], ["", ""], $lead->role) : $lead->number }}" data-role="{{ strtolower($lead->platform === 'indeed' ? 'php developer' : $lead->role) }}" data-status="{{ strtolower($lead->condition_status) }}" data-reason="{{ strtolower($lead->reason ?? '') }}">
                            <td>
                                @if($lead->platform === 'indeed')
                                    {{ $lead->number }} {{-- For Indeed leads, name is in number field --}}
                                @else
                                    {{ $lead->name }}
                                @endif
                            </td>
                            <td>
                                @if($lead->platform === 'indeed')
                                    {{ str_replace("'+91 ", "", $lead->role) }} {{-- For Indeed leads, phone is in role field --}}
                                @else
                                    {{ $lead->number }}
                                @endif
                            </td>
                            <td>
                                <div>
                                    @if($lead->platform === 'indeed')
                                        <span class="fw-medium">PHP Developer</span> {{-- Default role for Indeed leads --}}
                                    @else
                                        <span class="fw-medium">{{ $lead->role }}</span>
                                    @endif
                                    @if($lead->platform)
                                        <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $lead->platform)) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <select class="callback-status-select" data-id="{{ $lead->id }}" data-status="rejected">
                                    <option value="rejected" selected>Rejected</option>
                                    <option value="call_backs">Call Backs</option>
                                    <option value="not_interested">Not Interested</option>
                                    <option value="wrong_number">Wrong Number</option>
                                    <option value="interested">ShortListed</option>
                                </select>
                            </td>
                            <td>{{ $lead->reason ?? 'No reason provided' }}</td>
                            <td>{{ $lead->updated_at->format('d M Y, h:i A') }}</td>
                            <td>
                                @php
                                    $phoneNumber = $lead->platform === 'indeed' 
                                        ? str_replace(["'+91 ", " "], ["", ""], $lead->role)
                                        : $lead->number;
                                @endphp
                                <a href="https://wa.me/91{{ $phoneNumber }}" target="_blank" class="whatsapp-btn">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.leads.cv', $lead->id) }}" class="view-btn">CV</a>
                            </td>
                        </tr>
                        @empty
                        <tr id="noResults">
                            <td colspan="8" class="text-center">No rejected leads found</td>
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

<style>
/* ================= MAIN CONTENT ================= */
.main-content {
    margin-left: 130px;

    min-height: 100vh;
    background: #f8f9fa;
}

/* ================= CARD ================= */
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
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.card-header h4 {
    margin: 0;
    font-weight: 600;
    align-self: flex-start;
}

.search-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    gap: 15px;
}

/* ================= SEARCH ================= */
.search-form {
    flex: 1;
    max-width: 500px;
    display: flex;
    align-items: center;
    gap: 0;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0;
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
    padding: 8px 12px 8px 35px;
    border: 1px solid #ddd;
    border-radius: 6px 0 0 6px;
    font-size: 14px;
    background: #fff;
    border-right: none;
}

.search-btn {
    padding: 8px 16px;
    background: #2eacb3;
    color: #fff;
    border: 1px solid #2eacb3;
    border-radius: 0;
    font-size: 14px;
    cursor: pointer;
}

.clear-btn {
    padding: 8px 12px;
    background: #6c757d;
    color: #fff;
    text-decoration: none;
    border-radius: 0 6px 6px 0;
    font-size: 14px;
}

.results-info {
    font-size: 14px;
    color: #666;
    font-weight: 500;
    background: #f8f9fa;
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

/* Hidden rows for search */
.lead-row.hidden {
    display: none;
}

/* Search loading state */
.search-loading {
    opacity: 0.6;
    pointer-events: none;
}

/* ================= TABLE ================= */
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
    white-space: nowrap;
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

.text-center {
    text-align: center;
}

/* ================= BADGES ================= */
.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

/* ================= BUTTONS ================= */
.view-btn {
    background: #2eacb3;
    color: #fff;
    padding: 6px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
}

.view-btn:hover {
    background: #084298;
    color: #fff;
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

/* ================= PAGINATION ================= */
.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 5px;
}

.page-item {
    display: flex;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    color: #2eacb3;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.2s;
}

.page-link:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.page-item.active .page-link {
    background: #2eacb3;
    border-color: #2eacb3;
    color: #fff;
}

/* ================= MOBILE ================= */
@media (max-width: 992px) {
    .main-content {
        margin-left: 0;
        width: 100%;
    }
    
    .card-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .search-form {
        max-width: 100%;
    }
}
</style>

<script>
let currentSelect = null;
let currentLeadId = null;
let currentNewStatus = null;
let currentOldStatus = null;
let currentStatusText = null;
let currentCandidateName = null;

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
        showStatusPopup('Please provide a reason (minimum 10 characters)', 'error');
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
            showStatusPopup(currentCandidateName + '\'s status changed to ' + currentStatusText, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            currentSelect.value = currentOldStatus;
            currentSelect.setAttribute('data-status', currentOldStatus);
            showStatusPopup(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        currentSelect.value = currentOldStatus;
        currentSelect.setAttribute('data-status', currentOldStatus);
        showStatusPopup('Error: ' + error.message, 'error');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const resultsCount = document.getElementById('resultsCount');
    const leadRows = document.querySelectorAll('.lead-row');
    const noResults = document.getElementById('noResults');
    
    // Auto search function
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        leadRows.forEach(row => {
            const name = row.dataset.name || '';
            const number = row.dataset.number || '';
            const role = row.dataset.role || '';
            const status = row.dataset.status || '';
            const reason = row.dataset.reason || '';
            
            const isMatch = name.includes(searchTerm) || 
                          number.includes(searchTerm) || 
                          role.includes(searchTerm) ||
                          status.includes(searchTerm) ||
                          reason.includes(searchTerm);
            
            if (isMatch || searchTerm === '') {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update results count
        resultsCount.textContent = `${visibleCount} results`;
        
        // Show/hide no results message
        if (noResults) {
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.style.display = 'table-row';
                noResults.innerHTML = '<td colspan="8" class="text-center">No matching results found</td>';
            } else if (leadRows.length === 0) {
                noResults.style.display = 'table-row';
            } else {
                noResults.style.display = 'none';
            }
        }
    }
    
    // Add event listener for real-time search
    searchInput.addEventListener('input', function() {
        performSearch();
    });
    
    // Clear search on Escape key
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            performSearch();
        }
    });
    
    // Initial search if there's a value
    if (searchInput.value) {
        performSearch();
    }
    
    // Status change functionality
    document.querySelectorAll('.callback-status-select').forEach(select => {
        select.setAttribute('data-status', select.value);
        
        select.addEventListener('change', function() {
            currentSelect = this;
            currentLeadId = this.dataset.id;
            currentNewStatus = this.value;
            currentOldStatus = 'rejected';
            currentStatusText = this.options[this.selectedIndex].text;
            const row = this.closest('tr');
            currentCandidateName = row.querySelector('td:first-child').textContent;
            
            document.getElementById('statusChangeText').textContent = 
                `You are changing ${currentCandidateName}'s status to "${currentStatusText}"`;
            document.getElementById('reasonModal').style.display = 'flex';
            document.getElementById('reasonInput').focus();
        });
    });
    
    function showStatusPopup(message, type) {
        const popup = document.createElement('div');
        popup.className = 'status-popup ' + type;
        popup.innerHTML = '<div class="status-popup-header"><div class="status-popup-title">' + message + '</div></div>';
        document.body.appendChild(popup);
        
        setTimeout(() => popup.classList.add('show'), 100);
        setTimeout(() => {
            popup.classList.remove('show');
            setTimeout(() => popup.remove(), 400);
        }, 3000);
    }
});
</script>

@endsection