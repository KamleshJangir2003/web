@extends('auth.layouts.app')
<style>
    .main-content{
        padding-left: 130px;
     
    }
</style>
@section('title', 'Employee Credentials')
@section('page-title', 'Employee Login Management')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Employee Login Credentials</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Password</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->department }}</td>
                                    <td>
                                        @if($employee->temp_password)
                                            <code>{{ $employee->temp_password }}</code>
                                        @else
                                            <span class="text-danger">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->temp_password)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                        @if($employee->last_login)
                                            <br><small class="text-muted">Last: {{ $employee->last_login->format('d M Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$employee->temp_password)
                                            <form action="{{ route('admin.employee.generate-password', $employee->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Generate Password</button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-success" onclick="showCredentials({{ $employee->id }}, '{{ $employee->email }}', '{{ $employee->temp_password }}')">View Password</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for showing credentials -->
<div class="modal fade" id="credentialsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Login Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <strong>Employee Login Credentials:</strong>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Email:</strong></label>
                                    <input type="text" class="form-control" id="modalEmail" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Password:</strong></label>
                                    <input type="text" class="form-control" id="modalPassword" readonly>
                                </div>
                                <div class="alert alert-warning">
                                    <small><i class="fa-solid fa-exclamation-triangle"></i> Please save these credentials and share with employee.</small>
                                </div>
                            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showCredentials(id, email, password) {
    document.getElementById('modalEmail').value = email;
    document.getElementById('modalPassword').value = password;
    new bootstrap.Modal(document.getElementById('credentialsModal')).show();
}
</script>
@endsection