@extends('auth.layouts.app')

@section('title', 'Employee Credentials')
@section('page-title', 'Employee Login Management')

<style>
.main-content{
    padding-left:130px;
    
}

/* Table UI Improve */
.table td, .table th{
    vertical-align:middle;
    font-size:14px;
}

.badge{
    font-size:12px;
    padding:6px 10px;
}
</style>

@section('content')
<div class="container-fluid">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Employee Login Credentials</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Password</th>
                            <th>Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($employees as $employee)
                        <tr>

                            {{-- Name --}}
                            <td>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </td>

                            {{-- Email --}}
                            <td>{{ $employee->email }}</td>

                            {{-- Department --}}
                            <td>{{ $employee->department }}</td>

                            {{-- Password Column --}}
                            <td>
                                @if($employee->temp_password && !empty($employee->temp_password))
                                    <span class="text-primary fw-semibold">{{ $employee->temp_password }}</span>
                                @elseif($employee->password && !empty($employee->password))
                                    <span class="text-success fw-semibold">
                                        Password Set
                                    </span>
                                @else
                                    <span class="text-danger fw-semibold">
                                        Not Generated
                                    </span>
                                @endif
                            </td>

                            {{-- Status Column --}}
                            <td>
                                @if($employee->password && !empty($employee->password))
                                    <span class="badge bg-success">Active</span>
                                @elseif($employee->temp_password && !empty($employee->temp_password))
                                    <span class="badge bg-warning">Pending Activation</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                                
                                {{-- Password Display --}}
                                @if($employee->temp_password && !empty($employee->temp_password))
                                    <div class="mt-2">
                                        <small class="text-muted">Password: {{ $employee->temp_password }}</small>
                                    </div>
                                @endif
                            </td>

                            {{-- Action Column --}}
                            <td>
                                <form action="{{ route('admin.employee.generate-password',$employee->id) }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm btn-primary">
                                        Generate Password
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>


{{-- Modal For Showing Credentials --}}
<div class="modal fade" id="credentialsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Employee Login Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label>Email</label>
                    <input type="text" id="modalEmail"
                           class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="text" id="modalPassword"
                           class="form-control" readonly>
                </div>

            </div>

        </div>
    </div>
</div>


<script>
function showCredentials(email,password){
    document.getElementById('modalEmail').value = email;
    document.getElementById('modalPassword').value = password;

    new bootstrap.Modal(document.getElementById('credentialsModal')).show();
}
</script>

@endsection
