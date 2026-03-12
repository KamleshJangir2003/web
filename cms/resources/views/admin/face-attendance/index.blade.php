@extends('auth.layouts.app')

@section('content')
<style>
.container-fluid { 
    padding-left: 130px !important; 
}

#video { 
    width: 100%; 
    max-width: 640px; 
    border-radius: 10px; 
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
    width:100%;
    height:420px;      /* camera height increase */
    object-fit:cover;  /* video stretch na ho */
    border-radius:10px;
}

.card-body{
    padding:15px;
}

.btn-lg{
    width:100%;
    margin-bottom:10px;
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
                    <h4 class="mb-0"><i class="fas fa-user-check me-2"></i>Face Recognition Attendance</h4>
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
                                <button id="startCamera" class="btn btn-primary btn-lg">
                                    <i class="fas fa-camera me-2"></i>Start Camera
                                </button>
                                <button id="markAttendance" class="btn btn-success btn-lg" disabled>
                                    <i class="fas fa-check me-2"></i>Mark Attendance
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
                                            <th>Name</th>
                                            <th>Check-In</th>
                                            <th>Check-Out</th>
                                            <th>Late (min)</th>
                                            <th>Early (min)</th>
                                            <th>Overtime (hrs)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance-tbody">
                                        @forelse($todayAttendance as $att)
                                        <tr>
                                            <td>{{ $att->employee->employee_id ?? 'N/A' }}</td>
                                            <td>{{ $att->employee->first_name }} {{ $att->employee->last_name }}</td>
                                            <td>{{ $att->in_time ? date('h:i A', strtotime($att->in_time)) : '-' }}</td>
                                            <td>{{ $att->out_time ? date('h:i A', strtotime($att->out_time)) : '-' }}</td>
                                            <td>{{ $att->late_minutes ?? '-' }}</td>
                                            <td>{{ $att->early_checkout_minutes ?? '-' }}</td>
                                            <td>{{ $att->overtime_hours ?? '-' }}</td>
                                            <td>
                                                @if($att->status === 'Present')
                                                    <span class="badge bg-success">On Time</span>
                                                @elseif($att->status === 'Half Day')
                                                    <span class="badge bg-warning">Half Day</span>
                                                @elseif($att->status === 'Week Off')
                                                    <span class="badge bg-info">Week Off</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $att->status)) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="8" class="text-center text-muted">No attendance marked yet</td></tr>
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

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
// Check WebGL support
function checkWebGLSupport() {
    try {
        const canvas = document.createElement('canvas');
        const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
        if (!gl) {
            showStatus('WebGL is not supported. Using CPU backend (slower).', 'warning');
            return false;
        }
        return true;
    } catch (e) {
        showStatus('WebGL check failed. Using CPU backend.', 'warning');
        return false;
    }
}

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const startCameraBtn = document.getElementById('startCamera');
const markAttendanceBtn = document.getElementById('markAttendance');
const statusMessage = document.getElementById('status-message');

let modelsLoaded = false;
let stream = null;

// Set backend before loading models
checkWebGLSupport();

async function loadModels() {
    const MODEL_URL = '/models';
    try {
        // Force CPU backend if WebGL fails
        await faceapi.tf.setBackend('cpu');
        await faceapi.tf.ready();
        
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        modelsLoaded = true;
        showStatus('Models loaded successfully', 'success');
    } catch (error) {
        showStatus('Error loading models: ' + error.message, 'danger');
        console.error('Model loading error:', error);
    }
}

async function startCamera() {
    if (!modelsLoaded) {
        await loadModels();
    }
    
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: {} });
        video.srcObject = stream;
        startCameraBtn.disabled = true;
        markAttendanceBtn.disabled = false;
        showStatus('Camera started. Position your face and click Mark Attendance', 'info');
    } catch (error) {
        showStatus('Error accessing camera: ' + error.message, 'danger');
    }
}

async function detectFace() {
    try {
        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }))
            .withFaceLandmarks()
            .withFaceDescriptor();
        return detection;
    } catch (error) {
        console.error('Face detection error:', error);
        return null;
    }
}

async function markAttendance() {
    markAttendanceBtn.disabled = true;
    showStatus('Detecting face...', 'info');
    
    const detection = await detectFace();
    
    if (!detection) {
        showStatus('No face detected. Please try again.', 'warning');
        markAttendanceBtn.disabled = false;
        return;
    }
    
    showStatus('Face detected. Matching...', 'info');
    
    try {
        const response = await fetch('/admin/face-attendance/all-faces');
        
        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const errorData = await response.json();
                console.error('Server error:', errorData);
                showStatus(`Server error: ${errorData.message || 'Unknown error'}`, 'danger');
            } else {
                const text = await response.text();
                console.error('Server returned HTML:', text.substring(0, 500));
                showStatus('Server error: Expected JSON but got HTML. Check Laravel logs.', 'danger');
            }
            markAttendanceBtn.disabled = false;
            return;
        }
        
        const data = await response.json();
        console.log('Response data:', data);
    
        if (!data.success || data.employees.length === 0) {
            showStatus('No registered faces found. Please register first.', 'warning');
            markAttendanceBtn.disabled = false;
            return;
        }
        
        let bestMatch = null;
        let minDistance = 0.65;
        let secondBestDistance = Infinity;
        
        for (const emp of data.employees) {
            const savedDescriptor = JSON.parse(emp.descriptor);
            const distance = faceapi.euclideanDistance(detection.descriptor, savedDescriptor);
            console.log(`${emp.name}: distance = ${distance.toFixed(3)}`);
            
            if (distance < minDistance) {
                secondBestDistance = minDistance;
                minDistance = distance;
                bestMatch = emp;
            } else if (distance < secondBestDistance) {
                secondBestDistance = distance;
            }
        }
        
        console.log('Best match:', bestMatch?.name, 'Distance:', minDistance.toFixed(3));
        console.log('Second best distance:', secondBestDistance.toFixed(3));
        
        if (!bestMatch) {
            showStatus('Face not recognized. Distance too high. Please try again or register.', 'danger');
            document.getElementById('face-preview').style.display = 'none';
            markAttendanceBtn.disabled = false;
            return;
        }
        
        // Strict verification: Check if match is significantly better than second best
        const confidenceGap = secondBestDistance - minDistance;
        if (confidenceGap < 0.08) {
            showStatus('Face match is ambiguous. Please try again with better lighting.', 'danger');
            document.getElementById('face-preview').style.display = 'none';
            markAttendanceBtn.disabled = false;
            return;
        }
        
        // Show face preview with employee details
        const confidence = Math.round((1 - minDistance) * 100);
        document.getElementById('preview-emp-id').textContent = bestMatch.employee_id;
        document.getElementById('preview-emp-name').textContent = bestMatch.name;
        document.getElementById('preview-confidence').textContent = confidence + '%';
        document.getElementById('face-preview').style.display = 'block';
        
        const markResponse = await fetch('/admin/face-attendance/mark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: bestMatch.id,
                face_descriptor: JSON.stringify(Array.from(detection.descriptor))
            })
        });
        
        if (!markResponse.ok) {
            const errorText = await markResponse.text();
            console.error('Mark attendance error:', errorText);
            showStatus('Server error marking attendance. Check console.', 'danger');
            markAttendanceBtn.disabled = false;
            return;
        }
        
        const result = await markResponse.json();
        console.log('Mark response:', result);
        
        if (result.success) {
            const actionType = result.type === 'check_in' ? 'Check-In' : 'Check-Out';
            let statusBadge = result.type === 'check_in' 
                ? (result.status === 'Half Day' 
                    ? '<span class="badge bg-warning">Half Day</span>'
                    : '<span class="badge bg-success">On Time</span>')
                : '<span class="badge bg-info">Checked Out</span>';
            
            let message = `${actionType} successful for ${result.employee_name} at ${result.time}`;
            if (result.type === 'check_out') {
                if (result.early_checkout_minutes > 0) {
                    message += ` (${result.early_checkout_minutes} min early)`;
                }
                if (result.overtime_hours > 0) {
                    message += ` (${result.overtime_hours} hrs overtime)`;
                }
            }
            message += `. <a href="/admin/attendance" class="alert-link">View Attendance Page</a>`;
            
            showStatus(message, 'success');
            addToAttendanceList(bestMatch.employee_id, result.employee_name, result.time, statusBadge, actionType, result);
        } else {
            showStatus(result.message, 'danger');
        }
    } catch (error) {
        console.error('Fetch error:', error);
        showStatus('Network error: ' + error.message, 'danger');
    }
    
    markAttendanceBtn.disabled = false;
}

function showStatus(message, type) {
    statusMessage.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
}

function addToAttendanceList(empId, name, time, statusBadge, actionType, result) {
    const tbody = document.getElementById('attendance-tbody');
    if (tbody.querySelector('td[colspan]')) {
        tbody.innerHTML = '';
    }
    
    const earlyMin = result.early_checkout_minutes || '-';
    const overtimeHrs = result.overtime_hours || '-';
    const lateMin = result.late_minutes || '-';
    
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${empId}</td>
        <td>${name}</td>
        <td>${checkIn}</td>
        <td>${checkOut}</td>
        <td>${lateMin}</td>
        <td>${earlyMin}</td>
        <td>${overtimeHrs}</td>
        <td>${statusBadge}</td>
    `;
    tbody.insertBefore(row, tbody.firstChild);
}

startCameraBtn.addEventListener('click', startCamera);
markAttendanceBtn.addEventListener('click', markAttendance);

// Auto start camera on page load
document.addEventListener("DOMContentLoaded", async function () {
    await loadModels();
    await startCamera();
});
</script>
@endsection
