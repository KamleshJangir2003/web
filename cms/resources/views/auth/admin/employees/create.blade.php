@extends('auth.layouts.app')

@section('content')
<style>
    .container-fluid{
    margin-top: 60px !important;
    padding-left: 130px !important;
}
</style>
<div class="container-fluid">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <!-- <h4 class="page-title">Add Employee</h4> -->
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.employee.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- PERSONAL DETAILS -->
                <h5 class="mb-3">Personal Details</h5>
                <div class="row">
<div class="row">
<div class="col-md-6 mb-3">
    <label>Full Name <span class="text-danger">*</span></label>
    <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
</div>



                    <div class="col-md-6 mb-3">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Father Name <span class="text-danger">*</span></label>
                        <input type="text" name="father_name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Mother Name <span class="text-danger">*</span></label>
                        <input type="text" name="mother_name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Contact Number <span class="text-danger">*</span></label>
                        <input type="text" name="contact_number" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Guardian Number <span class="text-danger">*</span></label>
                        <input type="text" name="guardian_number" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
    <label>Gender <span class="text-danger">*</span></label>
    <select name="gender" class="form-control" required>
        <option value="">Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
    </select>
</div>
<div class="col-md-6 mb-3">
    <label>Shift <span class="text-danger">*</span></label>
    <select name="shift" id="shiftSelect" class="form-control" required>
        <option value="">Select Shift</option>
        <option value="day">Day Shift (9:30AM - 6:30PM)</option>
        <option value="night">Night Shift 1 (7:30PM - 4:30AM)</option>
        <option value="night">Night Shift 2 (8PM - 5:10AM)</option>
        <option value="custom">Custom Shift</option>
    </select>
</div>

<div class="col-md-6 mb-3 d-none" id="customShiftFields">
    <label>Start Time</label>
    <input type="time" name="start_time" class="form-control">
    
    <label class="mt-2">End Time</label>
    <input type="time" name="end_time" class="form-control">
</div>



                </div>

                <hr>
             

<!-- ADDRESS DETAILS -->
<h5 class="mb-3">Current Address</h5>
<div class="row border p-3 mb-4 rounded bg-light">

    <div class="col-md-12 mb-3">
        <label>Full Address <span class="text-danger">*</span></label>
        <textarea name="current_address" class="form-control" rows="3" required></textarea>
    </div>

    <div class="col-md-4 mb-3">
        <label>City <span class="text-danger">*</span></label>
        <input type="text" name="current_city" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>State <span class="text-danger">*</span></label>
        <input type="text" name="current_state" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Pincode <span class="text-danger">*</span></label>
        <input type="text" name="current_pincode" class="form-control" required>
    </div>

</div>


<h5 class="mb-3">Permanent Address</h5>

<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="sameAddress">
    <label class="form-check-label" for="sameAddress">
        Same as Current Address
    </label>
</div>

<div class="row border p-3 rounded bg-light">

    <div class="col-md-12 mb-3">
        <label>Full Address <span class="text-danger">*</span></label>
        <textarea name="permanent_address" class="form-control" rows="3" required></textarea>
    </div>

    <div class="col-md-4 mb-3">
        <label>City <span class="text-danger">*</span></label>
        <input type="text" name="permanent_city" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>State <span class="text-danger">*</span></label>
        <input type="text" name="permanent_state" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Pincode <span class="text-danger">*</span></label>
        <input type="text" name="permanent_pincode" class="form-control" required>
    </div>

</div>

<hr>

                <!-- PREVIOUS COMPANY DETAILS -->
                <h5 class="mb-3">Previous Employment Details</h5>
                <div class="row">

                    <!-- <div class="col-md-6 mb-3">
                        <label>Last Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_company_name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Last Salary (In Hand) <span class="text-danger">*</span></label>
                        <input type="number" name="last_salary_in_hand" class="form-control" required>
                    </div>-->
                    <div class="col-md-6 mb-3">
                        <label>UAN Number <span class="text-danger"></span></label>
                        <input type="text" name="uan_number" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>ESIC Number<span class="text-danger"></span></label>
                        <input type="number" name="esic_number" class="form-control">
                    </div> 

                   

                </div>

                <hr>

                <!-- BANK DETAILS -->
                <h5 class="mb-3">Bank Details</h5>
                <div class="row">
                <div class="col-md-6 mb-3">
                        <label>Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="Account_Holder_Name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Bank Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_number" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>IFSC Code <span class="text-danger">*</span></label>
                        <input type="text" name="ifsc_code" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" required>
                    </div>

                   

                    

                </div>

                <hr>
             

<!-- LOGIN CREDENTIALS -->
<!-- <h5 class="mb-3">Login Credentials</h5>
<div class="row">

    <div class="col-md-6 mb-3">
        <label>Create Password <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Re-enter Password <span class="text-danger">*</span></label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

</div>

<hr> -->

<h5 class="mb-3">Job Details</h5>

<div class="row">
<div class="col-md-6 mb-3">
    <label>Designation <span class="text-danger">*</span></label>
    <select name="designation" class="form-control" required>
        <option value="">Select Designation</option>
        <option value="Customer Support Executive">Customer Support Executive</option>
        <option value="Python Developer">Python Developer</option>
        <option value="PHP Developer">PHP Developer</option>
        <option value="Front End Developer">Front End Developer</option>
        <option value="Lead Consultant">Lead Consultant</option>
        <option value="Manager">Manager</option>
        <option value="Team Leader">Team Leader</option>
        <option value="HR">HR</option>
        <option value="Office Boy">Office Boy</option>
        <option value="Digital Marketing">Digital Marketing</option>
        <option value="Admin">Admin</option>
        <option value="Driver">Driver</option>
    </select>
</div>

</div>
<hr>
                <!-- SELFIE -->
                <!-- <h5 class="mb-3">Employee Selfie</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Upload Selfie <span class="text-danger">*</span></label>
                        <input type="file" name="selfie" class="form-control" required>
                    </div>
                </div> -->

                <!-- BUTTONS -->
               <div class="mt-4">
    <button type="submit" class="btn btn-primary">
        Save Employee
    </button>

    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
        Cancel
    </a>
</div>


            </form>

        </div>
    </div>

</div>
<script>
document.getElementById('shiftSelect').addEventListener('change', function() {
    var customFields = document.getElementById('customShiftFields');
    
    if (this.value === 'custom') {
        customFields.classList.remove('d-none');
    } else {
        customFields.classList.add('d-none');
    }
});
</script>

<script>
document.getElementById('sameAddress').addEventListener('change', function () {
    if (this.checked) {
        document.querySelector('[name="permanent_address"]').value = document.querySelector('[name="current_address"]').value;
        document.querySelector('[name="permanent_city"]').value = document.querySelector('[name="current_city"]').value;
        document.querySelector('[name="permanent_state"]').value = document.querySelector('[name="current_state"]').value;
        document.querySelector('[name="permanent_pincode"]').value = document.querySelector('[name="current_pincode"]').value;
    } else {
        document.querySelector('[name="permanent_address"]').value = '';
        document.querySelector('[name="permanent_city"]').value = '';
        document.querySelector('[name="permanent_state"]').value = '';
        document.querySelector('[name="permanent_pincode"]').value = '';
    }
});
</script>

@endsection
