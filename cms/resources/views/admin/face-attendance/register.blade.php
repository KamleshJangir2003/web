@extends('auth.layouts.app')
<style>
    /* =========================
   DESKTOP UI FIX ONLY
   ========================= */
@media (min-width: 769px){

.container-fluid{
    padding-left: 130px !important;
    padding-right: 30px;
}

/* Card Styling */
.card{
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* Header */
.card-header{
    font-weight: 600;
    font-size: 16px;
}

/* Video Improve */
#video{
    width: 100%;
    max-width: 420px;
    height: 320px;
    border-radius: 10px;
    object-fit: cover;
    border: 3px solid #eee;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Center Camera */
.text-center{
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Buttons */
.btn{
    min-width: 160px;
    margin: 6px;
    border-radius: 8px;
    font-weight: 500;
}

/* Select dropdown */
#employeeSelect{
    border-radius: 8px;
    padding: 10px;
}

/* Table Improve */
.table{
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

.table thead{
    background: #28a745;
    color: white;
}

.table th{
    font-size: 13px;
}

.table td{
    font-size: 13px;
}

/* Scroll table */
.card-body{
    max-height: 500px;
    overflow-y: auto;
}

/* Status badge spacing */
.badge{
    font-size: 12px;
    padding: 6px 10px;
}

}
</style>
@section('content')

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-primary text-white">
                        Register Employee Face
                    </div>

                    <div class="card-body">

                        <div id="status-message"></div>

                        <label>Select Employee</label>

                        <select id="employeeSelect" class="form-select mb-3">

                            <option value="">Select Employee</option>

                            @foreach($employees as $emp)

                                <option value="{{ $emp->employee_id }}">
                                    {{ $emp->employee_id }} - {{ $emp->full_name }}
                                </option>

                            @endforeach

                        </select>


                        <div class="text-center mb-3">

                            <video id="video" width="100%" autoplay muted></video>

                            <canvas id="canvas" style="display:none"></canvas>

                        </div>

                        <div class="text-center">

                            <button id="startCamera" class="btn btn-primary">
                                Start Camera
                            </button>

                            <button id="captureFace" class="btn btn-success">
                                Capture & Register
                            </button>

                        </div>

                    </div>
                </div>

            </div>


            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-success text-white">
                        Registered Employees
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered">

                            <thead>

                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($employees as $emp)

                                    <tr>

                                        <td>{{ $emp->employee_id }}</td>

                                        <td>{{ $emp->full_name }}</td>

                                        <td>

                                            @if($emp->face_encoding)

                                                <span class="badge bg-success">
                                                    Registered
                                                </span>

                                            @else

                                                <span class="badge bg-warning">
                                                    Not Registered
                                                </span>

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



    <script>

        const API_BASE_URL = "https://face-recognition-attendance-dsq4.onrender.com";

        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");

        const startCameraBtn = document.getElementById("startCamera");
        const captureFaceBtn = document.getElementById("captureFace");

        let stream = null;


        function showStatus(message, type = "info") {

            document.getElementById("status-message").innerHTML =

                `<div class="alert alert-${type}">
                    ${message}
                </div>`;

        }



        async function startCamera() {

            try {

                stream = await navigator.mediaDevices.getUserMedia({ video: true });

                video.srcObject = stream;

                showStatus("Camera Started", "success");

            } catch (error) {

                showStatus("Camera Error : " + error.message, "danger");

            }

        }



        function captureImage(callback) {

            const ctx = canvas.getContext("2d");

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            ctx.drawImage(video, 0, 0);

            canvas.toBlob(blob => {

                callback(blob);

            }, "image/jpeg");

        }



        async function registerFace() {

            const empId = document.getElementById("employeeSelect").value;

            if (!empId) {

                showStatus("Select employee first", "warning");
                return;

            }

            captureImage(async function (blob) {

                const formData = new FormData();

                formData.append("image", blob, "capture.jpg");
                formData.append("employee_id", empId);


                try {

                    const response = await fetch(API_BASE_URL + "/register-face", {

                        method: "POST",
                        body: formData

                    });

                    const result = await response.json();

                    console.log(result);

                    if (response.ok) {

                        // API success
                        showStatus("Face Registered Successfully", "success");

                        // Laravel database update
                        fetch('/admin/face-attendance/update-face-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                employee_id: empId
                            })
                        }).then(() => {
                            // Update UI status badge in the table
                            const rows = document.querySelectorAll('table tbody tr');
                            rows.forEach(row => {
                                const idCell = row.querySelector('td:nth-child(1)');
                                if (idCell && idCell.textContent.trim() === empId) {
                                    const statusCell = row.querySelector('td:nth-child(3)');
                                    if (statusCell) {
                                        statusCell.innerHTML = '<span class="badge bg-success">Registered</span>';
                                    }
                                }
                            });
                        });

                    } else {

                        showStatus("Error : " + JSON.stringify(result), "danger");

                    }

                } catch (error) {

                    showStatus("Network Error " + error.message, "danger");

                }

            });

        }

        startCameraBtn.addEventListener("click", startCamera);

        captureFaceBtn.addEventListener("click", registerFace);

    </script>

@endsection