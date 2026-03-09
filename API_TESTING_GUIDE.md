# API TESTING GUIDE

## 🧪 Test the Face Attendance API

### Base URL
```
http://your-domain.com/admin/face-attendance
```

---

## 1️⃣ GET ALL REGISTERED FACES

**Endpoint**: `GET /admin/face-attendance/all-faces`

**Response**:
```json
{
  "success": true,
  "employees": [
    {
      "id": 1,
      "employee_id": "KIO01",
      "name": "John Doe",
      "descriptor": "[0.123, 0.456, ...]"
    },
    {
      "id": 2,
      "employee_id": "KIO02",
      "name": "Jane Smith",
      "descriptor": "[0.789, 0.012, ...]"
    }
  ]
}
```

---

## 2️⃣ MARK ATTENDANCE (CHECK-IN)

**Endpoint**: `POST /admin/face-attendance/mark`

**Headers**:
```
Content-Type: application/json
X-CSRF-TOKEN: your-csrf-token
```

**Request Body**:
```json
{
  "employee_id": 1,
  "face_descriptor": "[0.123, 0.456, 0.789, ...]"
}
```

**Response (On Time)**:
```json
{
  "success": true,
  "type": "check_in",
  "message": "Check-in successful",
  "employee_name": "John Doe",
  "time": "09:25 AM",
  "status": "Present",
  "late_minutes": 0
}
```

**Response (Late)**:
```json
{
  "success": true,
  "type": "check_in",
  "message": "Check-in successful",
  "employee_name": "John Doe",
  "time": "09:45 AM",
  "status": "Late",
  "late_minutes": 15
}
```

---

## 3️⃣ MARK ATTENDANCE (CHECK-OUT)

**Endpoint**: `POST /admin/face-attendance/mark`

**Request Body**: (Same as check-in)
```json
{
  "employee_id": 1,
  "face_descriptor": "[0.123, 0.456, 0.789, ...]"
}
```

**Response**:
```json
{
  "success": true,
  "type": "check_out",
  "message": "Check-out successful",
  "employee_name": "John Doe",
  "time": "06:30 PM"
}
```

---

## 4️⃣ ALREADY COMPLETED

**Endpoint**: `POST /admin/face-attendance/mark`

**Request Body**: (Same as above)

**Response**:
```json
{
  "success": false,
  "message": "Attendance already completed today"
}
```

---

## 5️⃣ ERROR RESPONSES

### No Shift Assigned
```json
{
  "success": false,
  "message": "No shift assigned to employee."
}
```

### Face Not Registered
```json
{
  "success": false,
  "message": "Face not registered. Please register first."
}
```

### Employee Not Found
```json
{
  "success": false,
  "message": "Employee not found"
}
```

---

## 🧪 POSTMAN COLLECTION

### Collection: Face Attendance System

#### 1. Get All Faces
```
GET {{base_url}}/admin/face-attendance/all-faces
```

#### 2. Check-In (On Time)
```
POST {{base_url}}/admin/face-attendance/mark
Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}
Body:
{
  "employee_id": 1,
  "face_descriptor": "{{face_descriptor}}"
}
```

#### 3. Check-Out
```
POST {{base_url}}/admin/face-attendance/mark
Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}
Body:
{
  "employee_id": 1,
  "face_descriptor": "{{face_descriptor}}"
}
```

#### 4. Already Completed (Third Attempt)
```
POST {{base_url}}/admin/face-attendance/mark
Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}
Body:
{
  "employee_id": 1,
  "face_descriptor": "{{face_descriptor}}"
}
```

---

## 🔧 CURL EXAMPLES

### Get All Faces
```bash
curl -X GET http://your-domain.com/admin/face-attendance/all-faces \
  -H "Accept: application/json"
```

### Mark Attendance (Check-In)
```bash
curl -X POST http://your-domain.com/admin/face-attendance/mark \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{
    "employee_id": 1,
    "face_descriptor": "[0.123, 0.456, ...]"
  }'
```

---

## 🧪 TESTING SCENARIOS

### Scenario 1: Normal Day Shift Employee

**Setup**:
- Employee ID: 1
- Shift: Day Shift (09:30 AM - 06:30 PM)
- Date: March 9, 2024

**Test Steps**:

1. **Check-In (On Time) - 09:25 AM**
   ```json
   POST /mark
   {
     "employee_id": 1,
     "face_descriptor": "..."
   }
   ```
   Expected: `status: "Present", late_minutes: 0`

2. **Check-Out - 06:30 PM**
   ```json
   POST /mark
   {
     "employee_id": 1,
     "face_descriptor": "..."
   }
   ```
   Expected: `type: "check_out"`

3. **Third Attempt - 08:00 PM**
   ```json
   POST /mark
   {
     "employee_id": 1,
     "face_descriptor": "..."
   }
   ```
   Expected: `"Attendance already completed today"`

---

### Scenario 2: Late Employee

**Setup**:
- Employee ID: 2
- Shift: Day Shift (09:30 AM - 06:30 PM)
- Date: March 9, 2024

**Test Steps**:

1. **Check-In (Late) - 09:45 AM**
   ```json
   POST /mark
   {
     "employee_id": 2,
     "face_descriptor": "..."
   }
   ```
   Expected: `status: "Late", late_minutes: 15`

---

### Scenario 3: Night Shift Employee

**Setup**:
- Employee ID: 3
- Shift: Night Shift (07:30 PM - 05:10 AM)
- Date: March 9, 2024

**Test Steps**:

1. **Check-In - March 9, 7:30 PM**
   ```json
   POST /mark
   {
     "employee_id": 3,
     "face_descriptor": "..."
   }
   ```
   Expected: `date: "2024-03-09"`

2. **Check-Out - March 10, 5:10 AM**
   ```json
   POST /mark
   {
     "employee_id": 3,
     "face_descriptor": "..."
   }
   ```
   Expected: `date: "2024-03-09"` (same date)

---

## 📊 DATABASE VERIFICATION QUERIES

### Check Today's Attendance
```sql
SELECT 
    e.employee_id,
    e.full_name,
    a.date,
    a.check_in,
    a.check_out,
    a.status,
    a.late_minutes,
    s.shift_name
FROM attendance a
JOIN employees e ON a.employee_id = e.id
LEFT JOIN shift_types s ON e.shift_id = s.id
WHERE a.date = CURDATE()
ORDER BY a.check_in DESC;
```

### Check Specific Employee Attendance
```sql
SELECT 
    date,
    check_in,
    check_out,
    status,
    late_minutes
FROM attendance
WHERE employee_id = 1
  AND date = CURDATE();
```

### Check Late Employees
```sql
SELECT 
    e.employee_id,
    e.full_name,
    a.check_in,
    a.late_minutes,
    s.shift_name
FROM attendance a
JOIN employees e ON a.employee_id = e.id
JOIN shift_types s ON e.shift_id = s.id
WHERE a.date = CURDATE()
  AND a.late_minutes > 0
ORDER BY a.late_minutes DESC;
```

---

## 🐛 DEBUGGING TIPS

### Issue: "No shift assigned to employee"
**Check**:
```sql
SELECT id, employee_id, full_name, shift_id 
FROM employees 
WHERE id = 1;
```
**Fix**:
```sql
UPDATE employees SET shift_id = 1 WHERE id = 1;
```

### Issue: Late minutes not calculating
**Check**:
```sql
SELECT * FROM shift_types WHERE id = 1;
```
Verify `start_time` and `late_after` values.

### Issue: Night shift date wrong
**Check**:
```sql
SELECT shift_name, start_time, end_time 
FROM shift_types 
WHERE id = 2;
```
Verify `end_time < start_time` for night shifts.

---

## 📝 NOTES

1. **CSRF Token**: Get from meta tag in blade template
   ```html
   <meta name="csrf-token" content="{{ csrf_token() }}">
   ```

2. **Face Descriptor**: 128-dimensional array from face-api.js
   ```javascript
   const descriptor = Array.from(detection.descriptor);
   ```

3. **Time Format**: All times in 24-hour format (HH:mm:ss)

4. **Date Format**: YYYY-MM-DD

---

## ✅ TESTING CHECKLIST

- [ ] Get all faces API works
- [ ] Check-in creates attendance record
- [ ] Late calculation works correctly
- [ ] Check-out updates existing record
- [ ] Third attempt returns error
- [ ] Night shift date handling correct
- [ ] Status badges display correctly
- [ ] Frontend updates in real-time
- [ ] Database records are accurate
- [ ] CSRF protection working

---

Happy Testing! 🚀
