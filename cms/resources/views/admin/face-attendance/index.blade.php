@extends('auth.layouts.app')

@section('content')
<style>
.container-fluid { padding-left: 130px !important; }
#video { width: 100%; max-width: 640px; border-radius: 10px; }
#canvas { display: none; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h4 class="mb-0"><i class="fas fa-user-check me-2"></i>Face Recognition Attendance</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Position your face in front of the camera and click "Mark Attendance"
                    </div>

                    <div id="status-message"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <div style="position: relative; display: inline-block;">
                                    <video id="video" autoplay muted></video>
                                    <canvas id="canvas"></canvas>
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
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance-tbody">
                                        @forelse($todayAttendance as $att)
                                        <tr>
                                            <td>{{ $att->employee->employee_id ?? 'N/A' }}</td>
                                            <td>{{ $att->employee->first_name }} {{ $att->employee->last_name }}</td>
                                            <td>
                                                @if($att->in_time)
                                                    {{ date('h:i A', strtotime($att->in_time)) }} (Check-In)
                                                @endif
                                                @if($att->out_time)
                                                    <br>{{ date('h:i A', strtotime($att->out_time)) }} (Check-Out)
                                                @endif
                                            </td>
                                            <td>
                                                @if($att->status === 'present')
                                                    <span class="badge bg-success">On Time</span>
                                                @elseif($att->status === 'late')
                                                    <span class="badge bg-warning">Late</span>
                                                @elseif($att->status === 'half_day')
                                                    <span class="badge bg-info">Half Day</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $att->status)) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted">No attendance marked yet</td></tr>
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
        
        for (const emp of data.employees) {
            const savedDescriptor = JSON.parse(emp.descriptor);
            const distance = faceapi.euclideanDistance(detection.descriptor, savedDescriptor);
            console.log(`${emp.name}: distance = ${distance.toFixed(3)}`);
            
            if (distance < minDistance) {
                minDistance = distance;
                bestMatch = emp;
            }
        }
        
        console.log('Best match:', bestMatch?.name, 'Distance:', minDistance.toFixed(3));
        
        if (!bestMatch) {
            showStatus('Face not recognized. Distance too high. Please try again or register.', 'danger');
            markAttendanceBtn.disabled = false;
            return;
        }
        
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
                ? (result.status === 'Late' 
                    ? `<span class="badge bg-warning">Late (${result.late_minutes} min)</span>` 
                    : (result.status === 'Half Day' 
                        ? '<span class="badge bg-info">Half Day</span>'
                        : '<span class="badge bg-success">On Time</span>'))
                : '<span class="badge bg-info">Checked Out</span>';
            
            showStatus(`${actionType} successful for ${result.employee_name} at ${result.time}. <a href="/admin/attendance" class="alert-link">View Attendance Page</a>`, 'success');
            addToAttendanceList(bestMatch.employee_id, result.employee_name, result.time, statusBadge, actionType);
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

function addToAttendanceList(empId, name, time, statusBadge, actionType) {
    const tbody = document.getElementById('attendance-tbody');
    if (tbody.querySelector('td[colspan]')) {
        tbody.innerHTML = '';
    }
    
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${empId}</td>
        <td>${name}</td>
        <td>${time} (${actionType})</td>
        <td>${statusBadge}</td>
    `;
    tbody.insertBefore(row, tbody.firstChild);
}

startCameraBtn.addEventListener('click', startCamera);
markAttendanceBtn.addEventListener('click', markAttendance);

loadModels();
</script>
@endsection
