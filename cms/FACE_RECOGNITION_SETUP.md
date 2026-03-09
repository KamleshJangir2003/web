# Face Recognition Attendance System - Setup Guide

## Overview
This face recognition attendance system is integrated into your existing HRMS. It uses face-api.js for client-side face detection and recognition.

## Features
- ✅ Face registration for employees
- ✅ Real-time face detection using webcam
- ✅ Automatic attendance marking with face matching
- ✅ Duplicate attendance prevention (same day)
- ✅ Integrated with existing attendance system
- ✅ No separate dashboard - fully integrated

## Installation Steps

### 1. Run Database Migration
```bash
cd cms
php artisan migrate
```

This adds the `face_data` column to the `employees` table.

### 2. Download Face Recognition Models
Run the batch file to download required models:
```bash
cd cms
download_face_models.bat
```

This will download the following models to `public/models/`:
- tiny_face_detector_model (lightweight face detection)
- face_landmark_68_model (facial landmarks)
- face_recognition_model (face descriptors for matching)

**Alternative Manual Download:**
If the batch file doesn't work, manually download from:
https://github.com/justadudewhohacks/face-api.js/tree/master/weights

Place files in: `cms/public/models/`

### 3. Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Usage

### For Admin - Register Employee Faces

1. Navigate to **Payroll → Register Face** in the sidebar
2. Select an employee from the dropdown
3. Click "Start Camera"
4. Position the employee's face in front of the camera
5. Click "Capture & Register"
6. The system will detect and save the face data

**Note:** Each employee needs to register their face before using face attendance.

### For Employees - Mark Attendance

1. Navigate to **Payroll → Face Attendance** in the sidebar
2. Click "Start Camera"
3. Position your face in front of the camera
4. Click "Mark Attendance"
5. The system will:
   - Detect your face
   - Match it with registered faces
   - Mark attendance if matched
   - Prevent duplicate attendance for the same day

## Technical Details

### Database Schema
- **Table:** `employees`
- **New Column:** `face_data` (TEXT, nullable)
- **Stores:** JSON array of 128 face descriptor values

### Routes Added
```php
GET  /admin/face-attendance              // Mark attendance page
GET  /admin/face-attendance/register     // Register face page
POST /admin/face-attendance/save-face    // Save face data
POST /admin/face-attendance/mark         // Mark attendance
GET  /admin/face-attendance/all-faces    // Get all registered faces
```

### Controller
- **File:** `app/Http/Controllers/Admin/FaceAttendanceController.php`
- **Methods:**
  - `index()` - Show attendance marking page
  - `register()` - Show face registration page
  - `saveFaceData()` - Save employee face descriptor
  - `markAttendance()` - Mark attendance with face matching
  - `getAllFaceData()` - Get all registered faces for matching

### Face Matching Algorithm
- Uses Euclidean distance between face descriptors
- Threshold: 0.6 (lower = stricter matching)
- Finds best match among all registered faces
- Rejects if no match found below threshold

### Security Features
- CSRF token protection on all POST requests
- Employee ID validation
- Duplicate attendance prevention
- Face data stored as encrypted descriptors (not images)

## Browser Requirements
- Modern browser with WebRTC support (Chrome, Firefox, Edge)
- Camera permission required
- HTTPS recommended for production (camera access)

## Troubleshooting

### Models Not Loading
- Check if models exist in `public/models/`
- Verify file permissions
- Check browser console for errors
- Ensure correct MODEL_URL path in JavaScript

### Camera Not Working
- Grant camera permissions in browser
- Check if camera is being used by another app
- Try different browser
- For production, use HTTPS

### Face Not Detected
- Ensure good lighting
- Face should be clearly visible
- Remove glasses/masks if possible
- Try moving closer to camera

### Face Not Recognized
- Re-register the face with better lighting
- Ensure face is registered first
- Check if face_data exists in database
- Adjust threshold in code if needed (line 140 in index.blade.php)

## Performance Optimization
- Uses TinyFaceDetector (lightweight, fast)
- Face descriptors are only 128 numbers (small storage)
- Client-side processing (no server load)
- Models cached in browser after first load

## Integration with Existing System
- Attendance records stored in existing `attendance` table
- Uses same employee records
- Follows same attendance rules (duplicate prevention)
- Appears in regular attendance reports
- Compatible with salary generation system

## Future Enhancements (Optional)
- Add face image preview before registration
- Multiple face angles for better accuracy
- Admin dashboard for face registration status
- Bulk face registration
- Face re-registration option
- Attendance history with face recognition logs

## Support
For issues or questions, check:
1. Browser console for JavaScript errors
2. Laravel logs: `storage/logs/laravel.log`
3. Network tab for API request failures
4. Database for face_data column existence
