# INTERN CERTIFICATE SENDING - COMPLETE TESTING GUIDE

## 📋 COMPLETE WORKFLOW

### STEP 1: SETUP ONGOING INTERNSHIP
1. Go to: `http://127.0.0.1:8000/admin/interns/interested`
2. Find an interested intern
3. Click "Assign Mentor" or "Setup Ongoing"
4. Fill required details:
   - Course/Program (e.g., Web Development)
   - HR for Commission
   - Mentor Teacher
   - Start Date
   - Duration (1-12 months)
   - Stipend Amount
   - Upload Documents (Aadhar, PAN, Education)
5. Submit → Intern moves to "Ongoing" status

### STEP 2: VIEW ONGOING INTERNS
1. Go to: `http://127.0.0.1:8000/admin/interns/ongoing-list`
2. You'll see all ongoing interns with:
   - Name, Course, Mentor, HR
   - Start Date, Duration, Stipend
   - Actions: Edit Profile, Payment

### STEP 3: COMPLETE INTERNSHIP (WITH CERTIFICATE)
1. From Ongoing List, click "Edit Profile"
2. You'll see two new buttons:
   - ✅ "Complete Internship" (Blue button)
   - ❌ "Cancel Internship" (Red button)

3. Click "Complete Internship"
4. Modal opens with form:
   - Completion Date (required)
   - Performance Rating (Excellent/Very Good/Good/Average/Below Average)
   - Remarks (optional)
   - ✅ Send certificate via Email (checked by default)
   - ✅ Send certificate via WhatsApp (checked by default)

5. Fill the form and click "Complete & Generate Certificate"

6. What happens automatically:
   - ✅ Internship status changes to "Completed"
   - ✅ Certificate PDF is generated and saved
   - ✅ Email is sent with PDF attachment (if checked)
   - ✅ WhatsApp opens with pre-filled message (if checked)
   - ✅ Redirects to Profiles page

### STEP 4: VIEW COMPLETED PROFILES
1. Go to: `http://127.0.0.1:8000/admin/interns/profiles`
2. You'll see completed interns with:
   - Name, Course, Mentor, HR, Status
   - Actions:
     - 🔵 View Profile
     - 🟢 Certificate (Download)
     - 🔵 Send (NEW!)

### STEP 5: SEND CERTIFICATE AGAIN (FROM PROFILES)
1. Click the "Send" button next to any completed intern
2. Modal opens showing:
   - Intern Name
   - Email address (or "Not available")
   - Phone number (or "Not available")
   - Two checkboxes:
     - ✅ Send via Email
     - ✅ Send via WhatsApp

3. Select your preferred method(s)
4. Click "Send Certificate"

5. What happens:
   - If Email selected: Email sent with PDF attachment
   - If WhatsApp selected: WhatsApp opens with message
   - Success message shows what was sent

---

## 🔍 TESTING SCENARIOS

### Scenario 1: Complete Internship with Both Email & WhatsApp
**Steps:**
1. Edit ongoing intern profile
2. Click "Complete Internship"
3. Fill completion date and rating
4. Keep both checkboxes checked
5. Submit

**Expected Result:**
- ✅ Email sent to intern's email
- ✅ WhatsApp opens automatically
- ✅ Certificate saved in database
- ✅ Redirects to profiles page
- ✅ Certificate visible in profiles

### Scenario 2: Complete Internship with Only Email
**Steps:**
1. Edit ongoing intern profile
2. Click "Complete Internship"
3. Uncheck WhatsApp checkbox
4. Keep Email checkbox checked
5. Submit

**Expected Result:**
- ✅ Email sent only
- ❌ WhatsApp does not open
- ✅ Certificate saved

### Scenario 3: Complete Internship with Only WhatsApp
**Steps:**
1. Edit ongoing intern profile
2. Click "Complete Internship"
3. Uncheck Email checkbox
4. Keep WhatsApp checkbox checked
5. Submit

**Expected Result:**
- ❌ Email not sent
- ✅ WhatsApp opens with message
- ✅ Certificate saved

### Scenario 4: Send Certificate from Profiles Page
**Steps:**
1. Go to profiles page
2. Find completed intern
3. Click "Send" button
4. Select Email and/or WhatsApp
5. Submit

**Expected Result:**
- ✅ Certificate sent via selected method(s)
- ✅ Success message displayed

### Scenario 5: Intern Without Email
**Steps:**
1. Create intern without email
2. Complete internship
3. Try to send from profiles

**Expected Result:**
- ❌ Email checkbox disabled
- ✅ WhatsApp checkbox enabled (if number exists)
- ✅ Can still send via WhatsApp

### Scenario 6: Intern Without Phone Number
**Steps:**
1. Create intern without phone
2. Complete internship
3. Try to send from profiles

**Expected Result:**
- ✅ Email checkbox enabled (if email exists)
- ❌ WhatsApp checkbox disabled
- ✅ Can still send via Email

---

## 📧 EMAIL DETAILS

**Subject:** Internship Completion Certificate - KWIKSTER

**Content:**
- Congratulations message
- Intern details (Course, Duration, Period, Performance)
- Certificate attached as PDF
- Professional footer

**Attachment:** certificate_[intern_name].pdf

---

## 📱 WHATSAPP MESSAGE FORMAT

```
Congratulations [Intern Name]! Your internship at KWIKSTER has been completed successfully. Download your certificate: [Certificate URL]
```

**Example:**
```
Congratulations Rahul Kumar! Your internship at KWIKSTER has been completed successfully. Download your certificate: http://127.0.0.1:8000/uploads/certificates/certificate_123_1234567890.pdf
```

---

## 🗂️ DATABASE FIELDS USED

**Interns Table:**
- `name` - Intern name
- `email` - Email address (for sending)
- `number` - Phone number (for WhatsApp)
- `course` - Course/Program name
- `mentor_id` - Mentor employee ID
- `hr_id` - HR employee ID
- `start_date` - Internship start date
- `completion_date` - Completion date
- `internship_duration` - Duration in months
- `performance_rating` - Performance rating
- `completion_remarks` - Remarks
- `certificate_path` - PDF filename
- `final_result` - Status (Ongoing/Completed/Cancelled)

---

## 📁 FILES MODIFIED

1. **InternController.php**
   - Added: `sendCertificate()` method
   - Modified: `completeInternship()` method

2. **profiles.blade.php**
   - Added: Send button
   - Added: Send certificate modal
   - Added: JavaScript for sending

3. **edit-profile.blade.php**
   - Added: Complete Internship button & modal
   - Added: Cancel Internship button & modal
   - Added: JavaScript for completion

4. **CertificateMail.php**
   - Created: New Mailable class

5. **web.php**
   - Added: `/admin/interns/{id}/send-certificate` route

6. **certificate.blade.php**
   - Already exists (email template)

---

## ⚙️ MAIL CONFIGURATION

Add to `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="KWIKSTER"
```

**For Gmail:**
1. Enable 2-Factor Authentication
2. Generate App Password
3. Use App Password in MAIL_PASSWORD

---

## 🐛 TROUBLESHOOTING

### Email Not Sending
**Check:**
- Mail configuration in `.env`
- Internet connection
- Gmail App Password (if using Gmail)
- Check `storage/logs/laravel.log` for errors

### WhatsApp Not Opening
**Check:**
- Intern has valid phone number
- Browser allows popups
- WhatsApp Web is accessible

### Certificate Not Generating
**Check:**
- `public/uploads/certificates/` folder exists
- Folder has write permissions (777)
- DomPDF package is installed

### Send Button Not Visible
**Check:**
- Intern status is "Completed"
- `certificate_path` field has value
- Browser cache (Ctrl+F5 to refresh)

---

## ✅ SUCCESS INDICATORS

1. **Completion Success:**
   - Alert shows: "Internship completed successfully!"
   - Certificate URL displayed
   - WhatsApp opens (if selected)
   - Redirects to profiles page

2. **Send Success:**
   - Alert shows: "Email sent to [email] and WhatsApp message prepared for [number]"
   - WhatsApp opens with pre-filled message
   - Modal closes automatically

3. **Database Success:**
   - `final_result` = 'Completed'
   - `certificate_path` has filename
   - `completion_date` is set

---

## 📞 SUPPORT

If any issues:
1. Check browser console (F12) for JavaScript errors
2. Check `storage/logs/laravel.log` for PHP errors
3. Verify database fields are populated
4. Test with different browsers
5. Clear cache: `php artisan cache:clear`

---

**Last Updated:** 2025
**Version:** 1.0
