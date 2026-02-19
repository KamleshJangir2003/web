@extends('auth.layouts.app')
<style>
    .main-content{
        margin-top: 70px;
    }
    .card-header{
        display: flex;
    }
   
</style>
@section('title', 'Intern Payment')

@section('content')
<div class="main-content">
    <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">

<!-- Left Side Heading -->
<h4 class="mb-0">
    Payment Management - {{ $intern->name }}
</h4>

<!-- Right Side Button -->
<a href="{{ route('admin.interns.ongoing-list') }}" 
   class="btn btn-secondary">
    Back to Ongoing Interns →
</a>

</div>


        <div class="card-body">
            <!-- Intern Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Intern Details</h5>
                            <p><strong>Name:</strong> {{ $intern->name }}</p>
                            <p><strong>Course:</strong> {{ $intern->course ?? 'Not Set' }}</p>
                            <p><strong>Mentor:</strong> {{ $intern->mentor->full_name ?? 'Not Assigned' }}</p>
                            <p><strong>Duration:</strong> {{ $intern->internship_duration ?? 'Not Set' }} months</p>
                            <p><strong>Start Date:</strong> {{ $intern->start_date ? $intern->start_date->format('d M Y') : 'Not Set' }}</p>
                            <p><strong>End Date:</strong> {{ $intern->end_date ? $intern->end_date->format('d M Y') : 'Not Set' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Payment Info</h5>
                            <p><strong>Total Fees:</strong> {{ $intern->stipend ? '₹' . number_format($intern->stipend) : 'Not Set' }}</p>
                            <p><strong>Total Paid:</strong> <span class="text-success">₹{{ number_format($intern->total_paid ?? 0) }}</span></p>
                            <p><strong>Pending Amount:</strong> <span class="text-danger">₹{{ $intern->stipend ? number_format($intern->stipend - ($intern->total_paid ?? 0)) : '0' }}</span></p>
                            <p><strong>HR Commission:</strong> {{ $intern->hr->full_name ?? 'Not Assigned' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Actions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Payment Actions</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" onclick="addPayment()">Add Payment</button>
                                <button type="button" class="btn btn-warning" onclick="updateStipend()">Update Payment</button>
                                <button type="button" class="btn btn-info" onclick="viewPaymentHistory()">Payment History</button>
                                <button type="button" class="btn btn-primary" onclick="generatePayslip()">Generate Receipt</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Payment History</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Payment Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($intern->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('M Y') }}</td>
                                            <td>₹{{ number_format($payment->amount) }}</td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                            <td>
                                                <small class="text-muted">{{ $payment->payment_method ?? 'N/A' }}</small>
                                                @if($payment->notes)
                                                    <br><small>{{ $payment->notes }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No payment records found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment History - {{ $intern->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>Total Payments</h6>
                                <h4 class="text-primary">{{ $intern->payments->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>Total Paid</h6>
                                <h4 class="text-success">₹{{ number_format($intern->total_paid ?? 0) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>Pending</h6>
                                <h4 class="text-danger">₹{{ number_format(($intern->stipend ?? 0) - ($intern->total_paid ?? 0)) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>Last Payment</h6>
                                <h6 class="text-info">{{ $intern->payments->last() ? $intern->payments->last()->payment_date->format('d M Y') : 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($intern->payments->sortByDesc('payment_date') as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td><span class="badge bg-success">₹{{ number_format($payment->amount) }}</span></td>
                                <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewPaymentDetails({{ $payment->id }})" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No payment records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportPaymentHistory()">Export to Excel</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addPaymentForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Payment Amount (₹) *</label>
                        <input type="number" name="amount" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date *</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Payment notes or reference"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="savePayment()">Add Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Stipend Modal -->
<div class="modal fade" id="updateStipendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stipend</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStipendForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">New Stipend Amount (₹)</label>
                        <input type="number" name="stipend" class="form-control" min="0" value="{{ $intern->stipend }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Change</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Enter reason for stipend change"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveStipend()">Update Stipend</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function addPayment() {
    const modal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
    modal.show();
}

function savePayment() {
    const form = document.getElementById('addPaymentForm');
    const formData = new FormData(form);
    
    $.ajax({
        url: '{{ route("admin.interns.add-payment", $intern->id) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.success) {
                alert('Payment added successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPaymentModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON?.errors;
            if(errors) {
                let errorMsg = Object.values(errors).flat().join('\n');
                alert('Validation Errors:\n' + errorMsg);
            } else {
                alert('An error occurred. Please try again.');
            }
        }
    });
}

function updateStipend() {
    const modal = new bootstrap.Modal(document.getElementById('updateStipendModal'));
    modal.show();
}

function saveStipend() {
    const form = document.getElementById('updateStipendForm');
    const formData = new FormData(form);
    
    $.ajax({
        url: '{{ route("admin.interns.update-stipend", $intern->id) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.success) {
                alert('Stipend updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('updateStipendModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON?.errors;
            if(errors) {
                let errorMsg = Object.values(errors).flat().join('\n');
                alert('Validation Errors:\n' + errorMsg);
            } else {
                alert('An error occurred. Please try again.');
            }
        }
    });
}

function viewPaymentHistory() {
    const modal = new bootstrap.Modal(document.getElementById('paymentHistoryModal'));
    modal.show();
}

function viewPaymentDetails(paymentId) {
    alert('Payment details for ID: ' + paymentId + ' - Feature coming soon!');
}

function exportPaymentHistory() {
    alert('Export to Excel functionality - Coming soon!');
}

function generatePayslip() {
    window.open('{{ route("admin.interns.generate-payslip", $intern->id) }}', '_blank');
}
</script>
@endsection