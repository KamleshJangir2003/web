@extends('auth.layouts.app')

@section('content')
<style>

.container-fluid{
    padding-left:130px;
    padding-right:20px;
}

/* MOBILE FIX */

@media (max-width:768px){

.container-fluid{
    padding-left:10px !important;
    padding-right:10px !important;
    width:100% !important;
}

.row{
    margin-left:0 !important;
    margin-right:0 !important;
}

.card{
    width:100%;
}

#employeeSelect{
    width:100%;
}

}

/* video box */
#video{
    width:100%;
    max-width:640px;
    border-radius:10px;
}

#canvas{
    display:none;
}

/* -------- MOBILE RESPONSIVE -------- */

@media (max-width:768px){

.container-fluid{
    padding-left:15px !important;
    padding-right:15px !important;
}

.card-body{
    padding:15px;
}

#video{
    max-width:100%;
}

.btn-lg{
    width:100%;
    margin-bottom:10px;
}

.table{
    font-size:12px;
}

}

/* Fix dropdown overflow */
.card{
overflow:visible;
}

#employeeSelect{
width:100%;
max-width:100%;
}

.form-select{
width:100%;
}
#employeeSelect{
width:100%;
max-width:100%;
font-size:14px;
}

.form-select{
width:100%;
max-width:100%;
}

</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Register Employee Face</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Select an employee, start the camera, and capture their face for registration
                    </div>

                    <div id="status-message"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Employee</label>
                                <select id="employeeSelect" class="form-select">
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->employee_id }} - {{ $emp->full_name }} 
                                            @if($emp->face_data) ✓ @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="text-center mb-3">
                                <div style="position: relative; display: inline-block;">
                                    <video id="video" autoplay muted></video>
                                    <canvas id="canvas"></canvas>
                                </div>
                            </div>

                            <div class="text-center">
                                <button id="startCamera" class="btn btn-primary btn-lg" disabled>
                                    <i class="fas fa-camera me-2"></i>Start Camera
                                </button>
                                <button id="captureFace" class="btn btn-success btn-lg" disabled>
                                    <i class="fas fa-camera-retro me-2"></i>Capture & Register
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5>Registered Employees</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
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
                                                    @if($emp->face_data)
                                                        <span class="badge bg-success">Registered</span>
                                                    @else
                                                        <span class="badge bg-warning">Not Registered</span>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const employeeSelect = document.getElementById('employeeSelect');
const startCameraBtn = document.getElementById('startCamera');
const captureFaceBtn = document.getElementById('captureFace');
const statusMessage = document.getElementById('status-message');

let modelsLoaded = false;
let stream = null;

async function loadModels() {
    const MODEL_URL = '/models';
    try {
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        modelsLoaded = true;
        showStatus('Models loaded successfully', 'success');
    } catch (error) {
        showStatus('Error loading models: ' + error.message, 'danger');
    }
}

employeeSelect.addEventListener('change', function() {
    startCameraBtn.disabled = !this.value;
});

async function startCamera() {
    if (!modelsLoaded) {
        await loadModels();
    }
    
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: {} });
        video.srcObject = stream;
        startCameraBtn.disabled = true;
        captureFaceBtn.disabled = false;
        showStatus('Camera started. Position face and click Capture', 'info');
    } catch (error) {
        showStatus('Error accessing camera: ' + error.message, 'danger');
    }
}

async function captureFace() {
    const employeeId = employeeSelect.value;
    
    if (!employeeId) {
        showStatus('Please select an employee', 'warning');
        return;
    }
    
    captureFaceBtn.disabled = true;
    showStatus('Detecting face...', 'info');
    
    try {
        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        if (!detection) {
            showStatus('No face detected. Please try again.', 'warning');
            captureFaceBtn.disabled = false;
            return;
        }
        
        showStatus('Face detected. Saving...', 'info');
        
        const descriptor = Array.from(detection.descriptor);
        
        const response = await fetch('/admin/face-attendance/save-face', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                face_descriptor: descriptor
            })
        });
        
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        
        const result = await response.json();
        
        if (result.success) {
            showStatus('Face registered successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatus(result.message || 'Registration failed', 'danger');
            captureFaceBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        showStatus('Error: ' + error.message, 'danger');
        captureFaceBtn.disabled = false;
    }
}

function showStatus(message, type) {
    statusMessage.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
}

startCameraBtn.addEventListener('click', startCamera);
captureFaceBtn.addEventListener('click', captureFace);

loadModels();
</script>
@endsection
