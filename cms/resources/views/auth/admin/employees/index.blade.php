@extends('auth.layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
.container-fluid{
    margin-top: 60px !important;
    padding-left: 130px !important;
}



</style>
@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-semibold">All Employees</h4>
        <input type="text"
       class="form-control w-25"
       placeholder="Search by name, email or phone"
       id="employeeSearch">

    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                
                <!-- TABLE HEAD -->
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox">
                        </th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th class="text-center">Status</th>

                        <th>Actions</th>
                    </tr>
                </thead>

                <!-- TABLE BODY -->
                <tbody>
                    @forelse($employees as $emp)
                    <tr>

                        <!-- CHECKBOX -->
                        <td>
                            <input type="checkbox">
                        </td>

                        <!-- EMPLOYEE ID -->
                        <td>{{ $emp->employee_id ?? 'N/A' }}</td>

                        <!-- NAME -->
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://i.pravatar.cc/40?u={{ $emp->id }}"
                                     class="rounded-circle me-2"
                                     width="40" height="40">
                                <span class="fw-medium">
                                    {{ $emp->full_name }}
                                </span>
                            </div>
                        </td>

                        <!-- ROLE -->
                        <td>
                            <div>
                                <span class="fw-medium">{{ ucfirst($emp->user_type) }}</span>
                                @if($emp->platform)
                                    <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $emp->platform)) }}</small>
                                @endif
                            </div>
                        </td>

                        <!-- DEPARTMENT -->
                        <td>{{ ucfirst($emp->department) }}</td>

                        <!-- MOBILE -->
                        <td>
                            <i class="bi bi-telephone-fill text-danger me-1"></i>
                            {{ $emp->phone }}
                        </td>

                        <!-- EMAIL -->
                        <td>
                            <i class="bi bi-envelope-fill text-muted me-1"></i>
                            {{ $emp->email }}
                        </td>

                        <!-- STATUS -->
   <td class="text-center align-middle">
    @php
        $statusColors = [
            'active' => 'success',
            'resigned' => 'warning',
            'terminated' => 'danger',
            'absconding' => 'danger',
            'notice_period' => 'info',
            'left' => 'secondary',
            'on_hold' => 'warning'
        ];
        $status = $emp->employee_status ?? 'active';
        $color = $statusColors[$status] ?? 'secondary';
    @endphp
    <span class="badge bg-{{ $color }}">
        {{ ucwords(str_replace('_', ' ', $status)) }}
    </span>
</td>



                        <!-- ACTIONS -->
                       <!-- ACTIONS -->
<td class="text-center">

    <!-- EDIT -->
    <a href="{{ route('admin.employees.edit', $emp->id) }}"
       class="text-primary me-2"
       title="Edit">
        <i class="bi bi-pencil-square fs-5"></i>
    </a>

    <!-- DELETE -->
    <form action="{{ route('admin.employees.delete', $emp->id) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this employee?')">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-link p-0 text-danger"
                title="Delete">
            <i class="bi bi-trash fs-5"></i>
        </button>
    </form>

</td>


                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <!-- FOOTER -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $employees->count() }} employees
            </small>

            <select class="form-select w-auto">
                <option>10</option>
                <option>25</option>
                <option>50</option>
            </select>
        </div>
    </div>

</div>
@endsection
