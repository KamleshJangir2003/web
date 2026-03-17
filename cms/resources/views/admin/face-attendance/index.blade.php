@extends('auth.layouts.app')

@section('content')
<style>
.container-fluid { 
    padding-left: 130px !important; 
}


#canvas { 
    display: none; 
}

/* Mobile Responsive */
@media (max-width: 768px) {

.container-fluid{
    padding-left:15px !important;
    padding-right:15px !important;
}
#video{
    width:215px;
    height:215px;
    border-radius: 50%;      /* camera height increase */
    object-fit:cover;  /* video stretch na ho */
    
}

.card-body{
    padding:15px;
}

.btn-lg{
    width:100%;
    margin-bottom:10px;
}
.hidden {
    display:none;
}

.table{
    font-size:12px;
}

}

/* Hide header and sidebar only on mobile */
@media (max-width: 768px){

.sidebar,
.top-header{
    display:none !important;
}

/* Full width content */
.main-content{
    margin-left:0 !important;
    padding-top:0 !important;
    margin-top:0 !important; 
}

}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="mb-0"><i class="fas fa-user-check me-2"></i>Face Recognition Attendance</h6>
                </div>
                <div class="card-body">
                    <!-- <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Position your face in front of the camera and click "Mark Attendance"
                    </div> -->

                    <div id="status-message"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <div style="position: relative; display: inline-block;">
                                    <video id="video" autoplay muted></video>
                                    <canvas id="canvas"></canvas>
                                </div>
                            </div>
                            <div id="face-preview" class="card mb-3" style="display: none;">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Face Detected</h6>
                                    <p class="mb-2"><strong>Employee ID:</strong> <span id="preview-emp-id" class="badge bg-primary"></span></p>
                                    <p class="mb-2"><strong>Name:</strong> <span id="preview-emp-name" class="text-success"></span></p>
                                    <p class="mb-0"><strong>Confidence:</strong> <span id="preview-confidence" class="badge bg-info"></span></p>
                                </div>
                            </div>
                            <div class="text-center">
                                <button id="startCamera" class="hidden">
                                    
                                </button>
                                <button id="markEntry" class="btn btn-success btn-lg" disabled>
                                    <i class="fas fa-sign-in-alt me-2"></i>Mark Entry
                                </button>
                                <button id="markExit" class="btn btn-warning btn-lg" disabled>
                                    <i class="fas fa-sign-out-alt me-2"></i>Mark Exit
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Today's Attendance</h5>
                            <div id="attendance-list" class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Date</th>
                                            <th>Entry Time</th>
                                            <th>Exit Time</th>
                                            <th>Shift Type</th>
                                            <th>Shift Status</th>
                                            <th>Total Work Time</th>
                                            <th>Overtime (hrs)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance-tbody">
                                        <tr><td colspan="8" class="text-center text-muted">No attendance data yet</td></tr>
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

<script>

const API_BASE_URL = "https://face-recognition-attendance-dsq4.onrender.com";

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const startCameraBtn = document.getElementById('startCamera');
const markEntryBtn = document.getElementById('markEntry');
const markExitBtn = document.getElementById('markExit');
const statusMessage = document.getElementById('status-message');

let stream = null;


// -----------------------------
// Start Camera
// -----------------------------
async function startCamera() {

    try {

        stream = await navigator.mediaDevices.getUserMedia({
            video: true
        });

        video.srcObject = stream;

        startCameraBtn.disabled = true;
        markEntryBtn.disabled = false;
        markExitBtn.disabled = false;

        showStatus("Camera started. Look at the camera and click Mark Entry or Mark Exit.", "info");

    } catch (error) {

        showStatus("Camera access error : " + error.message, "danger");

    }

}


// -----------------------------
// Capture Image
// -----------------------------
function captureImage() {

    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    context.drawImage(video, 0, 0, canvas.width, canvas.height);

}


// -----------------------------
// Send Image to API - Entry
// -----------------------------
async function markEntry() {

    showStatus("Processing face recognition...", "info");

    captureImage();

    canvas.toBlob(async function(blob) {

        const formData = new FormData();

        formData.append("image", blob, "capture.jpg");

        try {

            const response = await fetch(API_BASE_URL + "/attendance/entry", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (response.ok) {

                showStatus("Entry marked successfully ✔", "success");

                // After marking attendance, fetch today's full attendance
                fetchTodayAttendance();

            } else {

                showStatus("API Error : " + JSON.stringify(data), "danger");

            }

        } catch (error) {

            showStatus("Network Error : " + error.message, "danger");

        }

    }, "image/jpeg");

}


// -----------------------------
// Send Image to API - Exit
// -----------------------------
async function markExit() {

    showStatus("Processing face recognition for exit...", "info");

    captureImage();

    canvas.toBlob(async function(blob) {

        const formData = new FormData();

        formData.append("image", blob, "capture.jpg");

        try {

            const response = await fetch(API_BASE_URL + "/attendance/exit", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (response.ok) {

                showStatus("Exit marked successfully ✔", "success");

                // After marking attendance, fetch today's full attendance
                fetchTodayAttendance();

            } else {

                showStatus("API Error : " + JSON.stringify(data), "danger");

            }

        } catch (error) {

            showStatus("Network Error : " + error.message, "danger");

        }

    }, "image/jpeg");

}

// -----------------------------
// Fetch today's attendance from API
// -----------------------------
function fetchTodayAttendance() {

    const today = new Date().toLocaleDateString('en-CA');// YYYY-MM-DD
    
    fetch(API_BASE_URL + '/attendance')
        .then(async response => {
            const data = await response.json();

            const tbody = document.getElementById("attendance-tbody");
            tbody.innerHTML = '';

            if (response.ok && Array.isArray(data) && data.length > 0) {
                data.forEach(function(record) {
                    if (record && record.employee_id) {
                        addToAttendanceList(record);
                    }
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No attendance data yet</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching today attendance:', error);
        });
}

// -----------------------------
// Show Status Message
// -----------------------------
function showStatus(message, type) {

    statusMessage.innerHTML = `
        <div class="alert alert-${type}">
            ${message}
        </div>
    `;

}


// -----------------------------
// Add row to table using API response shape
// -----------------------------
function addToAttendanceList(attendance) {

    const tbody = document.getElementById("attendance-tbody");

    // Remove "No attendance data yet" row if present
    if (tbody.children.length === 1 && tbody.children[0].children.length === 1) {
        tbody.innerHTML = '';
    }

    const row = document.createElement("tr");

    const entryTime = attendance.entry_time ? new Date(attendance.entry_time).toLocaleTimeString() : '-';
    const exitTime = attendance.exit_time ? new Date(attendance.exit_time).toLocaleTimeString() : '-';

    let statusBadgeClass = 'bg-secondary';
    let statusLabel = attendance.shift_status || '-';
    if (attendance.shift_status) {
        const statusLower = attendance.shift_status.toLowerCase();
        if (statusLower === 'present' || statusLower === 'on_time') {
            statusBadgeClass = 'bg-success';
        } else if (statusLower === 'absent') {
            statusBadgeClass = 'bg-danger';
        } else if (statusLower === 'late' || statusLower === 'half_day') {
            statusBadgeClass = 'bg-warning';
        }
    }

    row.innerHTML = `
        <td>${attendance.employee_id || '-'}</td>
        <td>${attendance.date || '-'}</td>
        <td>${entryTime}</td>
        <td>${exitTime}</td>
        <td>${attendance.shift_type || '-'}</td>
        <td><span class="badge ${statusBadgeClass}">${statusLabel}</span></td>
        <td>${attendance.total_work_time || '-'}</td>
        <td>${attendance.overtime_hours || '-'}</td>
    `;

    tbody.insertBefore(row, tbody.firstChild);

}


// -----------------------------
// Event Listeners
// -----------------------------
startCameraBtn.addEventListener("click", startCamera);

markEntryBtn.addEventListener("click", markEntry);
markExitBtn.addEventListener("click", markExit);


// -----------------------------
// Auto start camera and load today's attendance
// -----------------------------
document.addEventListener("DOMContentLoaded", function(){

    startCamera();
    fetchTodayAttendance();

});

</script>
@endsection
