@extends('auth.layouts.app')
<style>
    .main-content{
        padding-top: 15px;
    }
    .page-header{
    display: flex;
       /* Horizontal center */
    align-items: center;       /* Vertical center (optional) */
    margin-top: 40px;
}

</style>
@section('title', 'Schedule Interview')

@section('content')
<div class="main-content">
    <div class="page-header">
        <!-- <h1>📅 Schedule Interview</h1> -->
        <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <form action="{{ route('admin.interviews.store') }}" method="POST">
            @csrf
            
            @if(isset($interview))
                <input type="hidden" name="rescheduled_from" value="{{ $interview->id }}">
                <input type="hidden" name="reschedule_reason" id="reschedule_reason_field">
            @endif
            
            <!-- Candidate Info -->
            <div class="section">
                <div class="section-title">Candidate Information</div>
                <div class="row">
                    @if($lead || isset($interview))
                    <input type="hidden" name="lead_id" value="{{ $lead->id ?? $interview->lead_id }}">
                    <input type="hidden" name="candidate_name" value="{{ $lead->name ?? $interview->candidate_name }}">

                        <div class="col">
                            <label>Candidate Name</label>
                            <input type="text" value="{{ $lead->name ?? $interview->candidate_name }}" class="readonly" readonly>
                        </div>
                        <div class="col">
                            <label>Email</label>
                            <input type="email" name="candidate_email" value="{{ $lead->email ?? $interview->candidate_email }}" required>
                        </div>
                        <div class="col">
                            <label>Job Role</label>
                            <input type="text" value="{{ $lead->role ?? $interview->job_role }}" class="readonly" readonly>
                        </div>
                    @else
                        <div class="col">
                            <label>Select Candidate</label>
                            <select name="lead_id" required onchange="updateCandidateInfo(this)">
                                <option value="">Choose a candidate</option>
                                @foreach($leads as $leadOption)
                                    <option value="{{ $leadOption->id }}" 
                                            data-name="{{ $leadOption->name }}" 
                                            data-email="{{ $leadOption->email }}" 
                                            
                                            data-role="{{ $leadOption->role }}">
                                        {{ $leadOption->name }} - {{ $leadOption->role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label>Candidate Name</label>
                            <input type="text" id="candidate_name" class="readonly" readonly>
                        </div>
                        <div class="col">
                            <label>Email</label>
                            <input type="email" name="candidate_email" id="candidate_email" required>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Interview Details -->
            <div class="section">
                <div class="section-title">Interview Details</div>

                <label>Interview Round</label>
                <div class="radio-group">
                    <label><input type="radio" name="interview_round" value="HR" {{ ($nextRound == 'HR' || (isset($interview) && $interview->interview_round == 'HR')) ? 'checked' : '' }} required> HR</label>
                    <label><input type="radio" name="interview_round" value="Technical" {{ ($nextRound == 'Technical' || (isset($interview) && $interview->interview_round == 'Technical')) ? 'checked' : '' }} required> Technical</label>
                    <label><input type="radio" name="interview_round" value="Manager" {{ ($nextRound == 'Manager' || (isset($interview) && $interview->interview_round == 'Manager')) ? 'checked' : '' }} required> Manager</label>
                    <label><input type="radio" name="interview_round" value="Final" {{ ($nextRound == 'Final' || (isset($interview) && $interview->interview_round == 'Final')) ? 'checked' : '' }} required> Final</label>
                </div>

                <div class="row" style="margin-top:15px;">
                    <div class="col">
                        <label>Interview Date</label>
                        <input type="date" name="interview_date" value="{{ isset($interview) ? $interview->interview_date->format('Y-m-d') : '' }}" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col">
                        <label>Start Time</label>
                        <input type="time" name="start_time" value="{{ isset($interview) ? date('H:i', strtotime($interview->start_time)) : '' }}" required>
                    </div>
                    <div class="col">
                        <label>End Time</label>
                        <input type="time" name="end_time" value="{{ isset($interview) ? date('H:i', strtotime($interview->end_time)) : '' }}" required>
                    </div>
                </div>

                <div class="row" style="margin-top:15px;">
                    <div class="col">
                        <label>Interviewer</label>
                        <div style="display: flex; gap: 5px;">
                            <select name="interviewer" id="interviewer_select" required style="flex: 1;" onchange="updateInterviewerInfo(this)">
                                <option value="">Select Interviewer</option>
                                @foreach($interviewers as $interviewer)
                                    <option value="{{ $interviewer['name'] }}" data-email="{{ $interviewer['email'] }}" data-phone="{{ $interviewer['phone'] }}">{{ $interviewer['name'] }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="toggleAddInterviewer()" style="padding: 8px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">+</button>
                        </div>
                        <input type="text" id="new_interviewer" placeholder="Enter new interviewer name" style="margin-top: 5px; display: none;">
                        <input type="email" id="new_interviewer_email" placeholder="Enter email" style="margin-top: 5px; display: none;">
                        <input type="tel" id="new_interviewer_phone" placeholder="Enter phone" style="margin-top: 5px; display: none;">
                        <button type="button" id="save_interviewer_btn" onclick="saveNewInterviewer()" style="margin-top: 5px; display: none; padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Interviewer</button>
                    </div>
                    
                    <div class="col">
                        <label>Interviewer Email</label>
                        <input type="email" name="interviewer_email" required placeholder="interviewer@company.com">
                    </div>
                    <div class="col">
                        <label>Interviewer Phone</label>
                        <input type="tel" name="interviewer_phone" required placeholder="+91 9876543210">
                    </div>
                </div>

                <div class="row" style="margin-top:15px;">
                    <div class="col">
                        <label>Interview Mode</label>
                        <select name="interview_mode" required onchange="toggleMeetingSection(this.value)">
                            <option value="Online" {{ (isset($interview) && $interview->interview_mode == 'Online') ? 'selected' : '' }}>Online</option>
                            <option value="Offline" {{ (isset($interview) && $interview->interview_mode == 'Offline') ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Meeting Link -->
            <div class="section" id="meeting-section">
                <div class="section-title">Meeting Link</div>

                <label>Meeting Platform</label>
                <div class="radio-group">
                    <label><input type="radio" name="meeting_platform" value="Google Meet" {{ (isset($interview) && $interview->meeting_platform == 'Google Meet') ? 'checked' : '' }} onchange="toggleLinkInput()"> Google Meet</label>
                    <label><input type="radio" name="meeting_platform" value="Zoom" {{ (isset($interview) && $interview->meeting_platform == 'Zoom') ? 'checked' : '' }} onchange="toggleLinkInput()"> Zoom</label>
                    <label><input type="radio" name="meeting_platform" value="Teams" {{ (isset($interview) && $interview->meeting_platform == 'Teams') ? 'checked' : '' }} onchange="toggleLinkInput()"> Teams</label>
                </div>

                <div class="meeting-box" style="margin-top:12px;">
                    <input type="text" name="meeting_link" id="meeting_link" value="{{ isset($interview) ? $interview->meeting_link : '' }}" placeholder="Paste your meeting link here or generate one" required>
                    <button type="button" class="generate-btn" onclick="generateMeetingLink()">Generate Link</button>
                </div>
                
                <div class="meeting-note" style="margin-top:10px; padding:10px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:4px; font-size:13px;">
                    <strong>Note:</strong> 
                    <div id="platform-instructions">
                        <div id="meet-note" style="display:none;">For Google Meet: Create meeting at <a href="https://meet.google.com" target="_blank">meet.google.com</a></div>
                        <div id="zoom-note" style="display:none;">For Zoom: Create meeting at <a href="https://zoom.us" target="_blank">zoom.us</a> → Schedule Meeting</div>
                        <div id="teams-note" style="display:none;">For Teams: Create meeting at <a href="https://teams.microsoft.com" target="_blank">teams.microsoft.com</a> → Calendar → New Meeting</div>
                        <div id="default-note">Select a platform to see instructions</div>
                    </div>
                </div>
            </div>
             <!-- Interviewer -->
             <div class="section">
                <div class="section-title">Interviewer / Notes</div>
                <textarea name="interviewer_notes" placeholder="Notes for interviewer only"></textarea>
            </div>

            <!-- Notes -->
            <div class="section">
                <div class="section-title">Instructions / Notes</div>
                <textarea name="instructions" placeholder="Please join 10 minutes early. Keep portfolio ready.">{{ isset($interview) ? $interview->instructions : '' }}</textarea>
            </div>

            <!-- Notifications -->
            <div class="section">
                <div class="section-title">Notifications</div>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="email_candidate" checked> Email to Candidate</label>
                    <label><input type="checkbox" name="email_interviewer" checked> Email to Interviewer</label>
                    <label><input type="checkbox" name="whatsapp_notification"> WhatsApp Notification</label>
                </div>
            </div>

            <!-- Actions -->
            <div class="actions">
                <a href="{{ route('admin.interviews.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary" onclick="return handleFormSubmit()">{{ isset($interview) ? 'Reschedule Interview' : 'Schedule Interview' }}</button>
            </div>
        </form>
    </div>
</div>

<style>
body{
    margin:0;
    font-family: "Segoe UI", sans-serif;
    background:#f4f6f9;
}

.container{
    max-width:1100px;
    margin:10px auto;
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.section{
    margin-bottom:25px;
}

.section-title{
    font-size:16px;
    font-weight:600;
    margin-bottom:10px;
    color:#555;
}

.row{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}

.col{
    flex:1;
    min-width:250px;
}

label{
    font-size:14px;
    display:block;
    margin-bottom:6px;
    color:#444;
}

input, select, textarea{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
    font-size:14px;
    box-sizing: border-box;
}

textarea{
    resize:none;
    height:90px;
}

.radio-group, .checkbox-group{
    display:flex;
    gap:20px;
    margin-top:8px;
    flex-wrap: wrap;
}

.radio-group label,
.checkbox-group label{
    font-size:14px;
    cursor:pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.radio-group input,
.checkbox-group input{
    width: auto;
    margin: 0;
}

.meeting-box{
    display:flex;
    gap:10px;
    align-items:center;
}

.generate-btn{
    background:#2eacb3;
    color:#fff;
    border:none;
    padding:10px 15px;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
    white-space:nowrap;
}

.generate-btn:hover{
    background:#0056b3;
}

.readonly{
    background:#f8f9fa;
    color:#6c757d;
}

.actions{
    display:flex;
    gap:15px;
    justify-content:flex-end;
    margin-top:30px;
}

.btn-primary, .btn-secondary{
    padding:12px 25px;
    border:none;
    border-radius:6px;
    font-size:14px;
    cursor:pointer;
    text-decoration:none;
    display:inline-block;
}

.btn-primary{
    background:#28a745;
    color:#fff;
}

.btn-secondary{
    background:#6c757d;
    color:#fff;
}

.btn-primary:hover{
    background:#218838;
}

.btn-secondary:hover{
    background:#545b62;
}
</style>

<script>
// Load reschedule reason from sessionStorage if available
window.addEventListener('DOMContentLoaded', function() {
    const rescheduleReason = sessionStorage.getItem('rescheduleReason');
    if (rescheduleReason) {
        const reasonField = document.getElementById('reschedule_reason_field');
        if (reasonField) {
            reasonField.value = rescheduleReason;
            sessionStorage.removeItem('rescheduleReason');
        }
    }
});

function updateCandidateInfo(select) {
    const option = select.options[select.selectedIndex];
    if (option.value) {
        document.getElementById('candidate_name').value = option.dataset.name;
        document.getElementById('candidate_email').value = option.dataset.email;
    } else {
        document.getElementById('candidate_name').value = '';
        document.getElementById('candidate_email').value = '';
    }
}

function toggleMeetingSection(mode) {
    const meetingSection = document.getElementById('meeting-section');
    if (mode === 'Online') {
        meetingSection.style.display = 'block';
    } else {
        meetingSection.style.display = 'none';
    }
}

function toggleLinkInput() {
    const platform = document.querySelector('input[name="meeting_platform"]:checked');
    const linkInput = document.getElementById('meeting_link');
    
    // Hide all notes
    document.getElementById('meet-note').style.display = 'none';
    document.getElementById('zoom-note').style.display = 'none';
    document.getElementById('teams-note').style.display = 'none';
    document.getElementById('default-note').style.display = 'none';
    
    if (platform) {
        switch(platform.value) {
            case 'Google Meet':
                linkInput.placeholder = 'Create meeting at meet.google.com and paste link here';
                document.getElementById('meet-note').style.display = 'block';
                break;
            case 'Zoom':
                linkInput.placeholder = 'Create meeting at zoom.us and paste link here';
                document.getElementById('zoom-note').style.display = 'block';
                break;
            case 'Teams':
                linkInput.placeholder = 'Create meeting at teams.microsoft.com and paste link here';
                document.getElementById('teams-note').style.display = 'block';
                break;
        }
    } else {
        linkInput.placeholder = 'Select platform first';
        document.getElementById('default-note').style.display = 'block';
    }
}

function generateMeetingLink() {
    const platform = document.querySelector('input[name="meeting_platform"]:checked');
    if (!platform) {
        alert('Please select a meeting platform first');
        return;
    }
    
    const interviewDate = document.querySelector('input[name="interview_date"]').value;
    const startTime = document.querySelector('input[name="start_time"]').value;
    const endTime = document.querySelector('input[name="end_time"]').value;
    
    switch(platform.value) {
        case 'Google Meet':
            if (!interviewDate || !startTime || !endTime) {
                alert('Please fill interview date and time first');
                return;
            }
            
            // Generate Google Meet style link
            const meetingId = generateGoogleMeetId();
            const link = `https://meet.google.com/${meetingId}`;
            
            // Format the meeting info
            const dateObj = new Date(interviewDate);
            const formattedDate = dateObj.toLocaleDateString('en-US', { 
                month: 'long', 
                day: 'numeric', 
                year: 'numeric' 
            });
            
            const meetingInfo = `Kwikster Interview\n${formattedDate}\nTime: ${startTime} - ${endTime}\nTime zone: Asia/Kolkata\nGoogle Meet joining info\nVideo call link: ${link}`;
            
            document.getElementById('meeting_link').value = link;
            
            // Show formatted meeting info
            alert(`Meeting Created!\n\n${meetingInfo}`);
            break;
            
        case 'Zoom':
            // Set fixed Zoom meeting link
            const fixedZoomLink = 'https://us05web.zoom.us/j/86861179844?pwd=ZOog4VIvSjpfEau5v8ssxyIgYBjhiM.1';
            
            document.getElementById('meeting_link').value = fixedZoomLink;
            
            alert('Zoom Meeting Link Added!\n\nJoin Zoom Meeting:\n' + fixedZoomLink + '\n\nMeeting ID: 868 6117 9844\nPasscode: tj77ms');
            break;
            
        case 'Teams':
            alert('For Microsoft Teams:\n1. Go to teams.microsoft.com\n2. Click Calendar → New Meeting\n3. Copy the meeting link\n4. Paste it here');
            window.open('https://teams.microsoft.com', '_blank');
            break;
    }
}

function generateGoogleMeetId() {
    // Generate Google Meet style ID (xxx-xxxx-xxx)
    const chars = 'abcdefghijklmnopqrstuvwxyz';
    let result = '';
    
    // First part: 3 characters
    for (let i = 0; i < 3; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    result += '-';
    
    // Second part: 4 characters
    for (let i = 0; i < 4; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    result += '-';
    
    // Third part: 3 characters
    for (let i = 0; i < 3; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    return result;
}

function generateZoomMeetingId() {
    // Generate 11-digit Zoom meeting ID
    let id = '';
    for (let i = 0; i < 11; i++) {
        id += Math.floor(Math.random() * 10);
    }
    return id;
}

function generateZoomPassword() {
    // Generate Zoom password (alphanumeric string)
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let password = '';
    for (let i = 0; i < 32; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

function handleFormSubmit() {
    const mode = document.querySelector('select[name="interview_mode"]').value;
    if (mode === 'Online') {
        const meetingLink = document.getElementById('meeting_link').value;
        if (!meetingLink) {
            alert('Please generate a meeting link for online interview');
            return false;
        }
    }
    return true;
}
</script>


<script>
function updateCandidateInfo(select) {
    const option = select.options[select.selectedIndex];
    if (option.value) {
        document.getElementById('candidate_name').value = option.dataset.name;
        document.getElementById('candidate_email').value = option.dataset.email;
    } else {
        document.getElementById('candidate_name').value = '';
        document.getElementById('candidate_email').value = '';
    }
}

function toggleMeetingSection(mode) {
    const meetingSection = document.getElementById('meeting-section');
    if (mode === 'Offline') {
        meetingSection.style.display = 'none';
    } else {
        meetingSection.style.display = 'block';
    }
}

function handleFormSubmit() {
    const interviewMode = document.querySelector('select[name="interview_mode"]').value;
    const meetingLink = document.getElementById('meeting_link').value;
    
    if (interviewMode === 'Online' && !meetingLink.trim()) {
        alert('Please enter a meeting link for online interview');
        return false;
    }
    
    return true;
}

function toggleAddInterviewer() {
    const newInterviewerInput = document.getElementById('new_interviewer');
    const newEmailInput = document.getElementById('new_interviewer_email');
    const newPhoneInput = document.getElementById('new_interviewer_phone');
    const saveBtn = document.getElementById('save_interviewer_btn');
    const select = document.getElementById('interviewer_select');
    
    if (newInterviewerInput.style.display === 'none') {
        newInterviewerInput.style.display = 'block';
        newEmailInput.style.display = 'block';
        newPhoneInput.style.display = 'block';
        saveBtn.style.display = 'block';
        newInterviewerInput.focus();
    } else {
        newInterviewerInput.style.display = 'none';
        newEmailInput.style.display = 'none';
        newPhoneInput.style.display = 'none';
        saveBtn.style.display = 'none';
        newInterviewerInput.value = '';
        newEmailInput.value = '';
        newPhoneInput.value = '';
        select.value = '';
    }
}

function saveNewInterviewer() {
    const input = document.getElementById('new_interviewer');
    const emailInput = document.getElementById('new_interviewer_email');
    const phoneInput = document.getElementById('new_interviewer_phone');
    const select = document.getElementById('interviewer_select');
    const saveBtn = document.getElementById('save_interviewer_btn');
    
    const name = input.value.trim();
    const email = emailInput.value.trim();
    const phone = phoneInput.value.trim();
    
    console.log('Saving - Name:', name, 'Email:', email, 'Phone:', phone);
    
    if (name && email && phone) {
        // Remove any existing duplicate options first
        for (let i = select.options.length - 1; i >= 0; i--) {
            if (select.options[i].value === name) {
                select.remove(i);
            }
        }
        
        // Add the new interviewer with email and phone data
        const newOption = new Option(name, name);
        newOption.setAttribute('data-email', email);
        newOption.setAttribute('data-phone', phone);
        select.add(newOption);
        select.value = name;
        
        console.log('Added interviewer:', name);
        
        // Auto-fill email and phone fields
        updateInterviewerInfo(select);
        
        // Hide inputs and clear them
        input.style.display = 'none';
        emailInput.style.display = 'none';
        phoneInput.style.display = 'none';
        saveBtn.style.display = 'none';
        input.value = '';
        emailInput.value = '';
        phoneInput.value = '';
        
        alert('Interviewer added successfully!');
    } else {
        if (!name) {
            alert('Please enter interviewer name');
            input.focus();
        } else if (!email) {
            alert('Please enter email');
            emailInput.focus();
        } else if (!phone) {
            alert('Please enter phone number');
            phoneInput.focus();
        }
    }
}

function addInterviewerOnComplete(value) {
    const select = document.getElementById('interviewer_select');
    const input = document.getElementById('new_interviewer');
    const emailInput = document.getElementById('new_interviewer_email');
    const phoneInput = document.getElementById('new_interviewer_phone');
    
    console.log('Name:', value.trim());
    console.log('Email:', emailInput.value.trim());
    console.log('Phone:', phoneInput.value.trim());
    
    if (value.trim() && emailInput.value.trim() && phoneInput.value.trim()) {
        // Remove any existing duplicate options first
        for (let i = select.options.length - 1; i >= 0; i--) {
            if (select.options[i].value === value.trim()) {
                select.remove(i);
            }
        }
        
        // Add the new interviewer with email and phone data
        const newOption = new Option(value.trim(), value.trim());
        newOption.setAttribute('data-email', emailInput.value.trim());
        newOption.setAttribute('data-phone', phoneInput.value.trim());
        select.add(newOption);
        select.value = value.trim();
        
        console.log('Added interviewer:', value.trim());
        
        // Auto-fill email and phone fields
        updateInterviewerInfo(select);
        
        // Hide inputs and clear them
        input.style.display = 'none';
        emailInput.style.display = 'none';
        phoneInput.style.display = 'none';
        input.value = '';
        emailInput.value = '';
        phoneInput.value = '';
    } else {
        alert('Please fill all fields: Name, Email, and Phone');
        console.log('Missing fields - Name:', !!value.trim(), 'Email:', !!emailInput.value.trim(), 'Phone:', !!phoneInput.value.trim());
    }
}

function updateInterviewerInfo(select) {
    const selectedOption = select.options[select.selectedIndex];
    const emailField = document.querySelector('input[name="interviewer_email"]');
    const phoneField = document.querySelector('input[name="interviewer_phone"]');
    
    if (selectedOption && selectedOption.value) {
        const email = selectedOption.getAttribute('data-email');
        const phone = selectedOption.getAttribute('data-phone');
        
        if (email) emailField.value = email;
        if (phone) phoneField.value = phone;
    } else {
        emailField.value = '';
        phoneField.value = '';
    }
}

function handleEnterKey(event, value) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const emailInput = document.getElementById('new_interviewer_email');
        const phoneInput = document.getElementById('new_interviewer_phone');
        
        // Check if all fields are filled
        if (!emailInput.value.trim()) {
            emailInput.focus();
            return;
        }
        if (!phoneInput.value.trim()) {
            phoneInput.focus();
            return;
        }
        
        addInterviewerOnComplete(value);
    }
}
</script>
@endsection