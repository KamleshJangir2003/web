@extends('auth.layouts.app')

@section('content')
<div class="container-fluid" style="padding-left: 130px !important;">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Shift Management</h4>
                </div>
                <div class="card-body">
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                        <i class="fas fa-plus me-2"></i>Add New Shift
                    </button>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Shift Name</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Late After (minutes)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shifts as $shift)
                            <tr>
                                <td>{{ $shift->shift_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                <td>{{ $shift->late_after }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editShift({{ $shift }})">Edit</button>
                                    <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this shift?')">Delete</button>
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
</div>

<!-- Add Shift Modal -->
<div class="modal fade" id="addShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('shifts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Shift Name</label>
                        <input type="text" name="shift_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Late After (minutes)</label>
                        <input type="number" name="late_after" class="form-control" value="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
