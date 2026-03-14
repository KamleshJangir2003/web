<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    :root{
        --sidebar-width: 260px;
    }

    body{
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

.top-header{
    position: fixed;
    top: 0;
    left: var(--sidebar-width);
    width: calc(100% - var(--sidebar-width));
    height: 60px;
    background: #fff;
    border-bottom: 1px solid #2eacb3;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    z-index: 9999;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

.header-left{
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
    min-width: 0;
}

.header-center {
    display: none;
    align-items: center;
    justify-content: center;
    flex: 1;
}

.header-center .mobile-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 16px;
    color: #2eacb3;
}

.header-center .mobile-logo i {
    font-size: 20px;
    color: #2eacb3;
}

.header-center .mobile-logo img {
    height: 35px;
    width: auto;
}

.header-right{
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: nowrap;
}

.menu-btn, .header-icon{
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    position: relative;
    z-index: 10000;
    pointer-events: auto;
    padding: 8px;
    border-radius: 6px;
    transition: background 0.2s;
    flex-shrink: 0;
}

#fullscreenToggle {
    display: none;
    align-items: center;
    justify-content: center;
}

.header-icon:hover,
.menu-btn:hover{
    background: #f3f4f6;
}

.menu-btn{
    display: none;
}

/* DROPDOWN */
.dropdown{
    position: relative;
    z-index: 10000;
    flex-shrink: 0;
}

.dropdown-btn{
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.dropdown-menu{
    display: none;
    position: absolute;
    top: 45px;
    right: 0;
    background: #fff;
    border: 1px solid #ddd;
    min-width: 180px;
    border-radius: 6px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    z-index: 10001;
    max-width: 90vw;
}

.dropdown-menu li{
    padding: 2px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    pointer-events: auto;
}

.dropdown-menu li:hover{
    background: #f3f4f6;
}

.dropdown.open .dropdown-menu{
    display: block;
}

/* USER */
.user-dropdown img{
    width: 34px;
    height: 34px;
    border-radius: 50%;
}

/* BADGE */
/* ===== NOTIFICATION FIXED STYLE ===== */

#notifBtn{
    position: relative;
    width: 42px;
    height: 42px;
    padding: 0;
}

#notifBtn i {
    font-size: 18px;
}

#notifBtn:hover {
    background: #2eacb3;
    color: #fff;
    padding-left: 10px;
}

#notifBtn:hover i {
    color: #fff;
}

#notifBtn .badge{
    position: absolute;
    top: 17px;          /* thoda andar lao */
    right: 29px;        /* thoda andar lao */
    transform: translate(40%, -40%);  /* perfect corner alignment */
    
    background: #ff3b3b;
    color: #fff;
    font-size: 10px;
    font-weight: 600;

    min-width: 18px;
    height: 18px;
    padding: 0 5px;

    border-radius: 50px;
    display: flex;
    align-items: center;
    justify-content: center;

    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transform: translate(40%, -40%);
}



/* BILLS NOTIFICATION STYLES */
.bills-btn {
    animation: pulse 2s infinite;
}

.bills-badge {
    background: #f39c12 !important;
    top: -6px;
    right: -6px;
    padding: 2px 6px;
    border-radius: 50%;
    font-size: 10px;
}

.bills-menu {
    min-width: 280px;
}

.bills-header {
    background: linear-gradient(135deg, #f39c12, #f5b041);
    color: white;
    font-weight: 600;
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.bills-header .count-badge {
    background: rgba(255,255,255,0.3);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
}

.notif-section-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-weight: 600;
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.notif-section-header .count-badge {
    background: rgba(255,255,255,0.3);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
}

.bills-item {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.bills-item-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.bills-item-info strong {
    font-size: 14px;
}

.bills-item-info small {
    color: #666;
    font-size: 12px;
}

.bills-item button {
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 4px;
}

/* BIRTHDAY NOTIFICATION STYLES */
.birthday-btn {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.birthday-badge {
    background: #ff6b6b !important;
    top: -6px;
    right: -6px;
    padding: 2px 6px;
    border-radius: 50%;
    font-size: 10px;
}

.birthday-menu {
    min-width: 250px;
}

.birthday-header {
    background: linear-gradient(135deg, #ff6b6b, #ff8787);
    color: white;
    font-weight: 600;
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.birthday-header .count-badge {
    background: rgba(255,255,255,0.3);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
}

.birthday-item {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.birthday-item small {
    color: #666;
    margin-left: auto;
}

/* LOGOUT */
.logout{
    color: red;
}

/* MAIN CONTENT FIX */
.main-content{
    margin-left: 131px;
    /* padding-top: 80px; */
    position: relative;
    z-index: 1;
}

/* UPLOAD PROGRESS */
.upload-progress{
    position: fixed;
    top: 70px;
    right: 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 10002;
    display: none;
    min-width: 250px;
}

.progress-bar{
    width: 100%;
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 8px;
}

.progress-fill{
    height: 100%;
    background: #28a745;
    width: 0%;
    transition: width 0.3s;
}

/* Header right list */
.header-right li {
    list-style: none;
    flex-shrink: 0;
}

/* Salary Calculator & PF Form links */
.header-right li a {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
}

/* Salary Calculator style */
.header-right li a .fa-calculator {
    color: #2eacb3;
}
.header-right li a[onclick*="Salary"] {
    background: #eef5ff;
    color: #2eacb3;
    border: 1px solid #d6e4ff;
}
.header-right li a[onclick*="Salary"]:hover {
    background: #2eacb3;
    color: #fff;
    box-shadow: 0 6px 15px rgba(13,110,253,0.3);
}

/* PF Form style */
.header-right li a .fa-file-invoice {
    color: #2eacb3;
}
.header-right li a[href*="pf"] {
    background: #f4fff8;
    color: #2eacb3;
    border: 1px solid #c9f1dc;
}
.header-right li a[href*="pf"]:hover {
    background: #2eacb3;
    color: #fff;
    box-shadow: 0 6px 15px rgba(25,135,84,0.3);
}

/* Hover icon color fix */
.header-right li a:hover i {
    color: #fff;
}

/* Excel upload button */
#excelUploadBtn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 8px;
    background: #e9f7ef;
    border: 1px solid #2eacb3;
    color: #2eacb3;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

/* Excel icon */
#excelUploadBtn .fa-file-excel {
    font-size: 18px;
    color: #2eacb3;
}

/* Dropdown arrow */
#excelUploadBtn .fa-chevron-down {
    font-size: 12px;
    opacity: 0.7;
}

/* Hover effect */
#excelUploadBtn:hover {
    background: #2eacb3;
    color: #fff;
    box-shadow: 0 6px 15px rgba(25,135,84,0.35);
    transform: translateY(-1px);
}

/* Hover icon color */
#excelUploadBtn:hover i {
    color: #fff;
}

/* Active click */
#excelUploadBtn:active {
    transform: scale(0.97);
}

/* ADD EMPLOYEE BUTTON */
.header-icon[href*="employee/create"] {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #e8f5e8;
    border: 1px solid #c3e6c3;
    color: #198754;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-left: 10px;
}

.header-icon[href*="employee/create"]:hover {
    background: #198754;
    color: #fff;
    box-shadow: 0 4px 12px rgba(25,135,84,0.3);
    transform: translateY(-1px);
}

.header-icon[href*="employee/create"] i {
    font-size: 16px;
}


/* =========================
   NOTIFICATION DROPDOWN UI
========================= */

#notifMenu{
    width: 340px !important;
    border-radius: 14px;
    border: none;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

/* Top Header */
#notifMenu > li:first-child{
    padding: 14px 18px !important;
    font-size: 15px;
    font-weight: 600;
    background: #ffffff !important;
    border-bottom: 1px solid #f1f1f1;
}

/* Mark all read button */
#notifMenu button.btn-link{
    font-size: 12px;
    text-decoration: none;
    color: #2eacb3;
    font-weight: 500;
}

#notifMenu button.btn-link:hover{
    text-decoration: underline;
}

/* Section Headers */
.notif-section-header,
.birthday-header,
.bills-header{
    padding: 10px 16px !important;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Birthday Gradient */
.birthday-header{
    background: linear-gradient(135deg, #ff6b6b, #ff8787);
    color: #fff;
}

/* Updates Gradient */
.notif-section-header{
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
}

/* Bills Gradient */
.bills-header{
    background: linear-gradient(135deg, #f39c12, #f5b041);
    color: #fff;
}

/* Count Badge */
.count-badge{
    background: rgba(255,255,255,0.25);
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11px;
}

/* Notification Items */
#notifMenu li{
    padding: 12px 16px !important;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.2s ease;
}

#notifMenu li:last-child{
    border-bottom: none;
}

#notifMenu li:hover{
    background:rgb(115, 137, 158);
}

/* Birthday Item */
.birthday-item{
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.birthday-item small{
    margin-left: auto;
    font-size: 12px;
    color: #999;
}

/* Loading Text */
#notificationsList li[style*="Loading"]{
    text-align: center !important;
    padding: 25px !important;
    color: #999 !important;
    font-style: italic;
}

/* Smooth Scroll */
#notifMenu{
    scrollbar-width: thin;
}

#notifMenu::-webkit-scrollbar{
    width: 6px;
}

#notifMenu::-webkit-scrollbar-thumb{
    background: #ddd;
    border-radius: 10px;
}

</style>
<style>
    /* GLOBAL SEARCH */
.global-search {
    position: relative;
    width: 280px;
    flex-shrink: 1;
    min-width: 0;
}

.global-search input {
    width: 100%;
    padding: 9px 35px 9px 35px;
    border-radius: 20px;
    border: 2px solid #e1e5e9;
    font-size: 13px;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.global-search input:focus {
    outline: none;
    border-color: #2eacb3;
    background: #fff;
    box-shadow: 0 2px 8px rgba(255, 153, 0, 0.2);
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 14px;
}

.global-search-results {
    position: absolute;
    top: 50px;
    left: 0;
    width: 420px;
    max-width: 90vw;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    max-height: 500px;
    overflow-y: auto;
    display: none;
    z-index: 10005;
}

.search-result-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f4;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: background 0.2s ease;
}

.search-result-item:hover {
    background: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-avatar {
    width: 30px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.search-result-info {
    flex: 1;
    min-width: 0;
}

.search-result-name {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 4px;
    font-size: 14px;
}

.search-result-number {
    font-size: 12px;
    color: #666;
    margin-bottom: 6px;
}

.search-result-type {
    font-size: 11px;
    background: #e3f2fd;
    color: #1976d2;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 500;
    display: inline-block;
}

.search-result-page {
    font-size: 11px;
    color: #666;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.search-result-details {
    display: flex;
    gap: 8px;
    margin-top: 6px;
    flex-wrap: wrap;
}

.search-detail-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 8px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.status-badge {
    background: #e8f5e9;
    color: #2e7d32;
}

.status-badge.pending {
    background: #fff3e0;
    color: #e65100;
}

.status-badge.rejected {
    background: #ffebee;
    color: #c62828;
}

.status-badge.interested {
    background: #e3f2fd;
    color: #1565c0;
}

.status-badge.hired {
    background: #e8f5e9;
    color: #2e7d32;
}

.role-badge {
    background: #f3e5f5;
    color: #6a1b9a;
}

.platform-badge {
    background: #e0f2f1;
    color: #00695c;
}

.search-no-results {
    padding: 20px;
    text-align: center;
    color: #666;
    font-style: italic;
}


.header-icon[href*="employee/create"] {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 10px;
    background: linear-gradient(135deg, #198754, #20c997);
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(25,135,84,0.25);
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}

.header-icon[href*="employee/create"] i {
    font-size: 14px;
}

.header-icon[href*="employee/create"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(25,135,84,0.35);
    background: linear-gradient(135deg, #157347, #1aa179);
}

.header-icon[href*="employee/create"]:active {
    transform: scale(0.96);
}

/* ===============================
   GLOBAL STATUS POPUP NOTIFICATION
================================ */
.status-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 20px;
    min-width: 300px;
    z-index: 10000;
    transform: translateX(400px);
    opacity: 0;
    transition: all 0.4s ease;
    border-left: 4px solid #28a745;
}

.status-popup.show {
    transform: translateX(0);
    opacity: 1;
}

.status-popup.success {
    border-left-color: #28a745;
}

.status-popup.error {
    border-left-color: #dc3545;
}

.status-popup.warning {
    border-left-color: #ffc107;
}

.status-popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.status-popup-title {
    font-weight: 600;
    color: #333;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-popup-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.status-popup-close:hover {
    background: #f0f0f0;
    color: #333;
}

.status-popup-message {
    color: #666;
    font-size: 14px;
    line-height: 1.4;
}

.status-popup-details {
    margin-top: 8px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
    font-size: 13px;
    color: #555;
}


/* ADD EMPLOYEE BUTTON - SAME STYLE AS OTHERS */
.header-right li a[href*="employee/create"] {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    background: #e8f5e8;
    color: #198754;
    border: 1px solid #c3e6c3;
}

.header-right li a[href*="employee/create"]:hover {
    background: #198754;
    color: #fff;
    box-shadow: 0 6px 15px rgba(25,135,84,0.3);
}

.header-right li a[href*="employee/create"] i {
    font-size: 15px;
}

.header-right li a[href*="employee"] {
    background: #eef5ff;
    color: #2eacb3;
    border: 1px solid #d6e4ff;
}

.header-right li a[href*="employee"]:hover {
    background: #2eacb3;
    color: #fff;
    box-shadow: 0 6px 15px rgba(13,110,253,0.3);
}


@media (max-width: 768px) {
    #fullscreenToggle {
        display: none !important;
    }
}



</style>

<script>
// Global function to show status popup notification
function showStatusPopup(type, title, message, details = null) {
    // Remove existing popup if any
    const existingPopup = document.querySelector('.status-popup');
    if (existingPopup) {
        existingPopup.remove();
    }
    
    // Create popup element
    const popup = document.createElement('div');
    popup.className = `status-popup ${type}`;
    
    // Get appropriate icon
    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i>';
            break;
        case 'error':
            icon = '<i class="fa-solid fa-exclamation-circle" style="color: #dc3545;"></i>';
            break;
        case 'warning':
            icon = '<i class="fa-solid fa-exclamation-triangle" style="color: #ffc107;"></i>';
            break;
    }
    
    popup.innerHTML = `
        <div class="status-popup-header">
            <div class="status-popup-title">
                ${icon}
                ${title}
            </div>
            <button class="status-popup-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="status-popup-message">${message}</div>
        ${details ? `<div class="status-popup-details">${details}</div>` : ''}
    `;
    
    // Add to body
    document.body.appendChild(popup);
    
    // Show with animation
    setTimeout(() => {
        popup.classList.add('show');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (popup.parentElement) {
            popup.classList.remove('show');
            setTimeout(() => {
                if (popup.parentElement) {
                    popup.remove();
                }
            }, 400);
        }
    }, 5000);
}
</script>

<header class="top-header">
    <div class="header-left">

    <button class="menu-btn" id="mobileMenuToggle">
    <i class="fa-solid fa-bars"></i>
</button>
        <!-- FULLSCREEN TOGGLE BUTTON -->
        <button class="menu-btn" id="fullscreenToggle" title="Toggle Fullscreen">
            <i class="fa-solid fa-expand"></i>
        </button>
        
     
         <!-- GLOBAL SEARCH -->
<div class="global-search">
    <i class="fa-solid fa-search search-icon"></i>
    <input type="text" id="globalSearchInput"
           placeholder="Search In HRMS">
    <div id="globalSearchResults" class="global-search-results"></div>
</div>
    </div>
   
    <!-- MOBILE LOGO CENTER -->
    <div class="header-center">
        <div class="mobile-logo">
            <!-- Company Logo -->
            <img src="{{ asset('Kwikster.jpeg') }}" alt="Kwikster Logo">
        </div>
    </div>

<!-- EXCEL UPLOAD -->
<div class="dropdown">
            <input type="file" id="excelFileInput" accept=".xlsx,.xls,.csv" style="display: none;">
            <button class="header-icon dropdown-btn" id="excelUploadBtn" title="Upload Excel">
    <i class="fa-solid fa-file-excel"></i>
    <i class="fa-solid fa-chevron-down"></i>
</button>

        

            
            <ul class="dropdown-menu" id="excelMenu" style="padding: 10px; width: 260px;">

<!-- Upload Excel -->
<li onclick="selectExcelFile(event)" style="cursor: pointer; padding: 6px 10px;">
    <i class="fa-solid fa-file-excel" style="color: #28a745;"></i>
    Upload Excel File
</li>

<li style="font-size: 12px; color: #666; padding: 5px 10px;">
    Supported: .xlsx, .xls, .csv
</li>

<li><hr class="dropdown-divider"></li>

<!-- Manual Entry -->
<li onclick="toggleManualEntry(event)" style="cursor: pointer; padding: 6px 10px;">
    <i class="fa-solid fa-user-plus" style="color: #2eacb3;"></i>
    Add Manual Entry
</li>

<li id="manualEntryForm" style="display: none; padding: 10px; background: #f8f9fa; border-radius: 4px; margin: 5px;">
<input type="text" 
       id="manualName" 
       placeholder="Enter Name" 
       class="form-control form-control-sm mb-2"
       oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">

    <input type="tel"
       id="manualNumber"
       placeholder="Enter Number"
       maxlength="10"
       pattern="[0-9]{10}"
       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10);"
       class="form-control form-control-sm mb-2">

    <button onclick="saveManualEntry()" class="btn btn-primary btn-sm w-100">Save Lead</button>
</li>

<li><hr class="dropdown-divider"></li>

<!-- Platform Select -->
<li style="padding: 5px 10px;">
    <label style="font-size: 12px; color: #555;">Source / Platform</label>
    <select class="form-select form-select-sm" id="platformSelect">
        <option value="">-- Select Platform --</option>
        <option value="workindia">WorkIndia</option>
        <option value="indeed">Indeed</option>
        <option value="apna_job">Apna Job</option>
        <option value="naukri">Naukri</option>
        <option value="reference">Reference</option>
        <option value="olx">OLX</option>
    </select>
</li>


<!-- Field Select -->
<li style="padding: 5px 10px;">
    <label style="font-size: 12px; color: #555;">Select Field</label>
    <select class="form-select form-select-sm" id="roleSelect">
        <option value="">-- Select Role --</option>
        <option value="python_developer">Python Developer</option>
        <option value="python_intern">Python Intern</option>
        <option value="php_developer">PHP Developer</option>
        <option value="php_intern">PHP Intern</option>
        <option value="frontend_developer">Frontend Developer</option>
        <option value="leads_consultant">lead consultant</option>
        <option value="manager">Manager</option>
        <option value="team_leader">Team Leader</option>
        <option value="hr">HR</option>
        <option value="hr_intern">HR Intern</option>
        <option value="office_boy">Office Boy</option>
        <option value="digital_marketing">Digital Marketing</option>
        <option value="admin">Admin</option>
        <option value="tele_caller">Tele Caller</option>
        <option value="receptionist">Receptionist</option>
        <option value="customer_care_executive">Customer Care Executive</option>
    </select>
</li>

</ul>

        </div>




    <div class="header-right">
        <!-- <li>
    <a href="{{ route('admin.employee.create') }}" class="header-icon" title="Add Employee">
        <i class="fa-solid fa-user-plus"></i>
    </a>
    </li> -->

    <li>
    <a href="{{ route('admin.employee.create') }}">
        <i class="fa-solid fa-user-plus"></i>
        Walk-in
    </a>
</li>

    <!-- SALARY CALCULATOR LINK -->
    <li>
        <a href="#" onclick="openSalaryCalculatorModal(event)">
            <i class="fa-solid fa-calculator"></i>
            Salary Calculator
        </a>
    </li>

    <li>
        <a href="{{ route('admin.pf.forms') }}">
            <i class="fa-solid fa-file-invoice"></i>
            PF Form
        </a>
    </li>


       

        <!-- UNIFIED NOTIFICATION -->
        <div class="dropdown">
            <button class="header-icon dropdown-btn" id="notifBtn">
                <i class="fa-regular fa-bell"></i>
                @php
                    $totalNotifCount = 0;
                    if(isset($todayBirthdays)) $totalNotifCount += $todayBirthdays->count();
                @endphp
                <span class="badge" id="notifBadge" style="{{ $totalNotifCount > 0 ? 'display: inline;' : 'display: none;' }}">{{ $totalNotifCount }}</span>
            </button>

            <ul class="dropdown-menu" id="notifMenu" style="width: 320px; max-height: 500px; overflow-y: auto;">
                <li style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; background: #f8f9fa;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>All Notifications</span>
                        <button onclick="markAllAsRead()" class="btn btn-sm btn-link p-0" style="font-size: 12px;">Mark all read</button>
                    </div>
                </li>
                
                <!-- BIRTHDAYS SECTION -->
                @if(isset($todayBirthdays) && $todayBirthdays->count() > 0)
                <li class="birthday-header">
                    <span>?? Today's Birthdays</span>
                    <span class="count-badge">{{ $todayBirthdays->count() }}</span>
                </li>
                @foreach($todayBirthdays as $employee)
                <li class="birthday-item">
                    <i class="fa-solid fa-gift" style="color: #ff6b6b;"></i>
                    {{ $employee->full_name }}
                    <small>({{ $employee->department }})</small>
                </li>
                @endforeach
                @endif
                
                <!-- BILLS SECTION -->
                <div id="billsContent"></div>
                
                <!-- REGULAR NOTIFICATIONS -->
                <!-- <div id="notificationsList">
                    <li style="padding: 20px; text-align: center; color: #666;">Loading...</li>
                </div> -->
            </ul>
        </div>

        <!-- USER -->
        <div class="dropdown">
            <button class="user-dropdown dropdown-btn">
                <img src="https://i.pravatar.cc/40">
                <span>Admin</span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>

            <ul class="dropdown-menu">
                <li><a href="{{ route('admin.profile') }}"><i class="fa-solid fa-user"></i> Profile</a></li>
                <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-cog"></i> Settings</a></li>
                <li class="logout">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; width: 100%;  padding: 0;">
                            <i class="fa-solid fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</header>


   





<style>
    .top-header{
    overflow: visible !important;
}
/* User dropdown links styling */
.dropdown-menu li a {
    display: block;
    padding: 8px 15px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.dropdown-menu li a:hover {
    background-color: #f8f9fa;
    color: #667eea;
}

.dropdown-menu li a i {
    margin-right: 8px;
    width: 16px;
}

.dropdown-menu li.logout button {
    padding: 8px 15px;
    font-size: 14px;
}

.dropdown-menu li.logout button:hover {
    background-color: #dc3545;
    color: white;
}
    


</style>

<!-- Upload Progress -->
<div class="upload-progress" id="uploadProgress">
    <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-file-excel" style="color: #28a745;"></i>
        <span id="uploadText">Uploading Excel...</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>
</div>
<script>
document.getElementById('excelMenu').addEventListener('click', function (e) {
    e.stopPropagation();   // ?? yahi main fix hai
});
</script>


<script>
/* ---------------- DROPDOWN FIX ---------------- */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Close all other dropdowns
            document.querySelectorAll('.dropdown').forEach(d => {
                if (d !== this.parentElement) d.classList.remove('open');
            });

            // Toggle current dropdown
            this.parentElement.classList.toggle('open');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
    });

    // Prevent dropdown menu from closing when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', e => {
            e.stopPropagation();
        });
    });
});

/* ---------------- AUTO POPUP UNIFIED NOTIFICATIONS ---------------- */
@if(isset($todayBirthdays) && $todayBirthdays->count() > 0)
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const notifDropdown = document.getElementById('notifBtn').parentElement;
        notifDropdown.classList.add('open');
        setTimeout(() => notifDropdown.classList.remove('open'), 5000);
    }, 2000);
});
@endif



/* ---------------- BILLS AUTO POPUP ---------------- */
document.addEventListener('DOMContentLoaded', function() {
    checkDueBillsForHeader();
});



function checkDueBillsForHeader() {
    fetch('/admin/bills/due-today')
        .then(response => response.json())
        .then(data => {
            if (data.bills && data.bills.length > 0) {
                showBillsInHeader(data.bills);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Load notifications
function loadNotifications() {
    fetch('/admin/notifications/unread')
        .then(response => response.json())
        .then(data => {
            let totalCount = data.count;
            @if(isset($todayBirthdays))
            totalCount += {{ $todayBirthdays->count() }};
            @endif
            updateNotificationBadge(totalCount);
            displayNotifications(data.notifications);
        })
        .catch(error => console.error('Error:', error));
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notifBadge');
    badge.textContent = count;
    badge.style.display = count > 0 ? 'inline' : 'none';
}

function displayNotifications(notifications) {
    const list = document.getElementById('notificationsList');
    
    if (notifications.length === 0) {
        list.innerHTML = '';
        return;
    }
    
    list.innerHTML = `<li class="notif-section-header">
        <span>?? Recent Updates</span>
        <span class="count-badge">${notifications.length}</span>
    </li>` + 
        notifications.map(notif => `
        <li style="padding: 10px 12px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s;" 
            onclick="markAsRead(${notif.id})" 
        
            <div style="font-size: 14px; font-weight: 500; color: #333;">${notif.title}</div>
            <div style="font-size: 12px; color: #666; margin-top: 2px;">${notif.message}</div>
            <div style="font-size: 11px; color: #999; margin-top: 4px;">
                <i class="fa-regular fa-clock" style="margin-right: 4px;"></i>${formatTime(notif.created_at)}
            </div>
        </li>
    `).join('');
}

function markAsRead(id) {
    fetch(`/admin/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => loadNotifications());
}

function markAllAsRead() {
    fetch('/admin/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => loadNotifications());
}

function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (minutes < 1440) return `${Math.floor(minutes / 60)}h ago`;
    return date.toLocaleDateString();
}

// Load notifications on page load and refresh every 30 seconds
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    setInterval(loadNotifications, 30000);
});lsInHeader(data.bills);
            }
        })
        .catch(error => console.error('Error:', error));
}

function showBillsInHeader(bills) {
    const content = document.getElementById('billsContent');
    const badge = document.getElementById('notifBadge');
    
    // Generate bills content
    let billsHtml = `<li class="bills-header">
        <span>?? Bills Due Today</span>
        <span class="count-badge">${bills.length}</span>
    </li>`;
    bills.forEach(function(bill) {
        billsHtml += `<li class="bills-item">
            <div class="bills-item-info">
                <strong>${bill.bill_type}</strong>
                <small>?${parseFloat(bill.amount).toFixed(2)} - Due: ${new Date(bill.due_date).toLocaleDateString('en-GB')}</small>
            </div>
            <button class="btn btn-success btn-sm" onclick="markBillAsPaidFromHeader(${bill.id})">
                <i class="fa-solid fa-check"></i>
            </button>
        </li>`;
    });
    
    content.innerHTML = billsHtml;
    
    // Update badge count
    updateNotificationBadge(parseInt(badge.textContent || 0) + bills.length);
}

function markBillAsPaidFromHeader(billId) {
    fetch(`/admin/bills/${billId}/mark-paid`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh bills in header
            checkDueBillsForHeader();
        } else {
            showStatusPopup('error', 'Error', 'Operation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatusPopup('error', 'Error', 'Operation failed');
    });
}

/* ---------------- SELECT EXCEL ---------------- */
function selectExcelFile(e) {
    e.stopPropagation();

    const role = document.getElementById('roleSelect').value;
    if (!role) {
        showStatusPopup('error', 'Error', 'Operation failed');
        return;
    }

    document.getElementById('excelFileInput').click();
}

/* ---------------- PLATFORM FILTERING ---------------- */
document.addEventListener('DOMContentLoaded', function() {
    const platformSelect = document.getElementById('platformSelect');
    const roleSelect = document.getElementById('roleSelect');
    
    function filterEmployees() {
        const selectedPlatform = platformSelect.value;
        const selectedRole = roleSelect.value;
        
        if (selectedPlatform || selectedRole) {
            let url = '/admin/employees?';
            let params = [];
            
            if (selectedRole) params.push(`role=${selectedRole}`);
            if (selectedPlatform) params.push(`platform=${selectedPlatform}`);
            
            url += params.join('&');
            window.location.href = url;
        }
    }
    
    if (platformSelect) {
        platformSelect.addEventListener('change', filterEmployees);
    }
    
    if (roleSelect) {
        roleSelect.addEventListener('change', filterEmployees);
    }
});

/* ---------------- UPLOAD EXCEL ---------------- */
document.getElementById('excelFileInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const role = document.getElementById('roleSelect').value;
    const platform = document.getElementById('platformSelect').value;
    if (!file || !role) return;

    const formData = new FormData();
    formData.append('excel_file', file);
    formData.append('role', role);
    formData.append('platform', platform);
    formData.append('_token', '{{ csrf_token() }}');

    const progress = document.getElementById('uploadProgress');
    const fill = document.getElementById('progressFill');
    const text = document.getElementById('uploadText');

    progress.style.display = 'block';
    fill.style.width = '0%';
    text.innerText = 'Uploading Excel...';

    const xhr = new XMLHttpRequest();

    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            fill.style.width = (e.loaded / e.total) * 100 + '%';
        }
    };

    xhr.onload = () => {
        if (xhr.status === 200) {
            fill.style.width = '100%';
            text.innerText = 'Upload Complete!';
            setTimeout(() => {
                progress.style.display = 'none';
                document.querySelector('.dropdown.open').classList.remove('open');
            }, 2000);
        } else {
            text.innerText = 'Upload Failed!';
            fill.style.background = '#dc3545';
        }
    };

    xhr.send(formData);
});

/* ---------------- MANUAL ENTRY ---------------- */
function toggleManualEntry(e) {
    e.stopPropagation();
    const form = document.getElementById('manualEntryForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function saveManualEntry() {
    const name = document.getElementById('manualName').value.trim();
    const number = document.getElementById('manualNumber').value.trim();
    const role = document.getElementById('roleSelect').value;
    const platform = document.getElementById('platformSelect').value;

    if (!name || !number) {
        showStatusPopup('error', 'Error', 'Operation failed');
        return;
    }
      // ? Only 10 digit number allowed
      if (!/^[0-9]{10}$/.test(number)) {
        showStatusPopup('error', 'Error', 'Operation failed');
        return;
    }
    if (!role) {
        showStatusPopup('error', 'Error', 'Operation failed');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('number', number);
    formData.append('role', role);
    formData.append('platform', platform);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('/save-manual-lead', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatusPopup('success', 'Success', 'Lead saved successfully!');
            document.getElementById('manualName').value = '';
            document.getElementById('manualNumber').value = '';
            document.getElementById('manualEntryForm').style.display = 'none';
        } else {
            showStatusPopup('error', 'Error', 'Operation failed');
        }
    })
    .catch(() => showStatusPopup('error', 'Error', 'Operation failed'));
}
</script>
<script>
const searchInput = document.getElementById('globalSearchInput');
const searchResults = document.getElementById('globalSearchResults');
let searchTimeout;

searchInput.addEventListener('input', function () {
    let query = this.value.trim();

    // Clear previous timeout
    clearTimeout(searchTimeout);

    if (query.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    // Add debounce for better performance
    searchTimeout = setTimeout(() => {
        fetch(`/admin/global-search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                searchResults.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(item => {
                        let div = document.createElement('div');
                        div.classList.add('search-result-item');

                        // Get initials for avatar
                        let initials = item.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);

                        // Status badge with color
                        let statusClass = 'status-badge';
                        if (item.status) {
                            statusClass += ' ' + item.status.toLowerCase().replace(/\s+/g, '-');
                        }

                        // Build details section
                        let detailsHtml = '<div class="search-result-details">';
                        
                        if (item.status && item.status !== 'N/A') {
                            detailsHtml += `<span class="search-detail-badge ${statusClass}">?? ${item.status}</span>`;
                        }
                        
                        if (item.role && item.role !== 'N/A') {
                            detailsHtml += `<span class="search-detail-badge role-badge">?? ${item.role}</span>`;
                        }
                        
                        if (item.platform && item.platform !== 'N/A') {
                            detailsHtml += `<span class="search-detail-badge platform-badge">?? ${item.platform}</span>`;
                        }
                        
                        detailsHtml += '</div>';

                        div.innerHTML = `
                            <div class="search-result-avatar">${initials}</div>
                            <div class="search-result-info">
                                <div class="search-result-name">${item.name}</div>
                                <div class="search-result-number">${item.number || 'No number'}</div>
                                <div class="search-result-page">?? <strong>${item.page}</strong> - <span class="search-result-type">${item.type}</span></div>
                                ${detailsHtml}
                            </div>
                        `;

                        div.onclick = function () {
                            window.location.href = item.url;
                        };

                        searchResults.appendChild(div);
                    });

                    searchResults.style.display = 'block';
                } else {
                    searchResults.innerHTML = `<div class="search-no-results">No employees found for "${query}"</div>`;
                    searchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = `<div class="search-no-results">Search error occurred</div>`;
                searchResults.style.display = 'block';
            });
    }, 300);
});

// Hide results when clicking outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('.global-search')) {
        searchResults.style.display = 'none';
    }
});

// Show results when focusing on input if there's content
searchInput.addEventListener('focus', function() {
    if (this.value.length >= 2 && searchResults.innerHTML) {
        searchResults.style.display = 'block';
    }
});

// Mobile Menu Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('sidebar-mobile-open');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#sidebar') && !e.target.closest('#mobileMenuToggle')) {
                sidebar.classList.remove('sidebar-mobile-open');
            }
        });
    }
});

// Fullscreen Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const fullscreenBtn = document.getElementById('fullscreenToggle');
    const sidebar = document.getElementById('sidebar');
    const header = document.querySelector('.top-header');
    const mainContent = document.querySelector('.main-content');
    let isFullscreen = false;

    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function() {
            isFullscreen = !isFullscreen;
            
            if (isFullscreen) {
                // Fullscreen mode ON
                sidebar.classList.add('sidebar-collapsed');
                header.classList.add('header-fullscreen');
                if (mainContent) mainContent.classList.add('content-fullscreen');
                this.querySelector('i').classList.replace('fa-expand', 'fa-compress');
            } else {
                // Fullscreen mode OFF
                sidebar.classList.remove('sidebar-collapsed');
                header.classList.remove('header-fullscreen');
                if (mainContent) mainContent.classList.remove('content-fullscreen');
                this.querySelector('i').classList.replace('fa-compress', 'fa-expand');
            }
        });
    }
});
</script>

<!-- ================= SALARY CALCULATOR MODAL ================= -->
<div id="salaryCalculatorModal" class="salary-modal" style="display: none;">
    <div class="salary-modal-content">
        <div class="salary-modal-header">
            <h5>Salary Calculator</h5>
            <button class="salary-modal-close" onclick="closeSalaryCalculatorModal()">&times;</button>
        </div>
        <div class="salary-modal-body">
            <form id="salaryCalculatorForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">In-Hand Salary</label>
                    <input type="number" id="modalInHandSalary" class="form-control" placeholder="Enter in-hand salary" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Calculate Gross & CTC</button>
            </form>

            <div id="salaryResults" style="display: none; margin-top: 20px;">
                <hr>
                <table class="table table-bordered table-sm">
                    <tr>
                        <th>Gross Salary</th>
                        <td id="resultGross">? 0.00</td>
                    </tr>
                    <tr>
                        <th>Basic Salary (60%)</th>
                        <td id="resultBasic">? 0.00</td>
                    </tr>
                    <tr>
                        <th>HRA (40%)</th>
                        <td id="resultHra">? 0.00</td>
                    </tr>
                    <tr>
                        <th>Employee PF (12% of Basic)</th>
                        <td id="resultEmpPf">? 0.00</td>
                    </tr>
                    <tr>
                        <th>Employee ESIC (0.75% of Gross)</th>
                        <td id="resultEmpEsic">? 0.00</td>
                    </tr>
                    <tr>
                        <th>Employer PF (13% of Basic)</th>
                        <td id="resultEmprPf">? 0.00</td>
                    </tr>
                    <tr>
                        <th>Employer ESIC (3.25% of Gross)</th>
                        <td id="resultEmprEsic">? 0.00</td>
                    </tr>
                    <tr class="table-success">
                        <th>In-Hand Salary</th>
                        <td id="resultInHand"><strong>? 0.00</strong></td>
                    </tr>
                    <tr class="table-info">
                        <th>Total CTC</th>
                        <td id="resultCtc"><strong>? 0.00</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Salary Calculator Modal */
.salary-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.salary-modal-content {
    background: #fff;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.salary-modal-header {
    background: linear-gradient(135deg, #2eacb3, #084298);
    color: white;
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.salary-modal-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.salary-modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s;
}

.salary-modal-close:hover {
    background: rgba(255,255,255,0.2);
}

.salary-modal-body {
    padding: 20px;
}

.salary-modal-body .form-label {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
}

.salary-modal-body .form-control {
    height: 44px;
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 10px 15px;
}

.salary-modal-body .btn-success {
    height: 45px;
    border-radius: 8px;
    font-weight: 600;
    background: #198754;
    border: none;
}

.salary-modal-body .btn-success:hover {
    background: #157347;
}

.salary-modal-body table {
    font-size: 14px;
}

.salary-modal-body table th {
    background: #f8f9fa;
    width: 60%;
    font-weight: 600;
    padding: 10px;
}

.salary-modal-body table td {
    font-weight: 500;
    padding: 10px;
}

.salary-modal-body .table-success th,
.salary-modal-body .table-success td {
    background: #e6f4ea !important;
    color: #146c43;
}

.salary-modal-body .table-info th,
.salary-modal-body .table-info td {
    background: #e7f1ff !important;
    color: #084298;
}

/* ========================================
   RESPONSIVE BREAKPOINTS
======================================== */

/* Mobile: 320px – 480px */
@media (max-width: 480px) {
    .top-header {
        left: 0;
        width: 100%;
        padding: 0 8px;
        height: 55px;
        justify-content: space-between;
    }
    
    .menu-btn {
        display: flex;
        font-size: 20px;
        padding: 6px;
    }
    
    .header-left {
        gap: 0;
        flex: 0;
    }
    
    .header-center {
        display: flex;
        flex: 1;
    }
    
    .header-center .mobile-logo {
        font-size: 14px;
    }
    
    .header-center .mobile-logo i {
        font-size: 18px;
    }
    
    .header-center .mobile-logo img {
        height: 43px;
        width: 190px;
    }
    
    .header-right {
        gap: 6px;
        flex: 0;
    }
    
    .global-search {
        display: none;
    }
    
    .header-right li {
        display: none;
    }
    
    #fullscreenToggle {
        display: none;
    }
    
    #excelUploadBtn {
        display: flex;
        font-size: 20px;
        padding: 6px;
    }
    
    #excelUploadBtn span {
        display: none;
    }
    
    #excelUploadBtn .fa-chevron-down {
        display: none;
    }
    
    .dropdown-menu {
        min-width: 160px;
        max-width: calc(100vw - 20px);
        right: 0;
    }
    
    .user-dropdown span {
        display: none;
    }
    
    .user-dropdown .fa-chevron-down {
        display: none;
    }
    
    .user-dropdown img {
        width: 32px;
        height: 32px;
    }
    
    .header-icon {
        font-size: 18px;
        padding: 6px;
    }
    
    .sidebar {
        position: fixed;
        left: 0;
        top: 55px;
        height: calc(100vh - 55px);
        width: 260px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 9998;
    }
    
    .sidebar.sidebar-mobile-open {
        transform: translateX(0);
        box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }
    
    .main-content {
        margin-left: 0;
        padding: 10px;
    }
}

/* Large Mobile: 481px – 768px */
@media (min-width: 481px) and (max-width: 768px) {
    .top-header {
        left: 0;
        width: 100%;
        padding: 0 12px;
        height: 60px;
        justify-content: space-between;
    }
    
    .menu-btn {
        display: flex;
        font-size: 20px;
    }
    
    .header-left {
        gap: 0;
        flex: 0;
    }
    
    .header-center {
        display: flex;
        flex: 1;
    }
    
    .header-center .mobile-logo {
        font-size: 16px;
    }
    
    .header-center .mobile-logo i {
        font-size: 20px;
    }
    
    .header-center .mobile-logo img {
        height: 35px;
    }
    
    .header-right {
        gap: 8px;
        flex: 0;
    }
    
    .global-search {
        display: none;
    }
    
    .header-right li {
        display: none;
    }
    
    #fullscreenToggle {
        display: none;
    }
    
    #excelUploadBtn {
        display: flex;
        font-size: 18px;
        padding: 7px;
    }
    
    #excelUploadBtn span {
        display: none;
    }
    
    #excelUploadBtn .fa-chevron-down {
        display: none;
    }
    
    .user-dropdown span {
        display: none;
    }
    
    .user-dropdown .fa-chevron-down {
        display: none;
    }
    
    .dropdown-menu {
        max-width: calc(100vw - 30px);
    }
    
    .sidebar {
        position: fixed;
        left: 0;
        top: 60px;
        height: calc(100vh - 60px);
        width: 260px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 9998;
    }
    
    .sidebar.sidebar-mobile-open {
        transform: translateX(0);
        box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }
    
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
}

/* Tablet: 769px – 1024px */
@media (min-width: 769px) and (max-width: 1024px) {
    .top-header {
        left: 260px;
        width: calc(100% - 260px);
        padding: 0 15px;
    }
    
    .menu-btn {
        display: none;
    }
    
    .header-center {
        display: none;
    }
    
    .header-left {
        gap: 12px;
    }
    
    .header-right {
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .global-search {
        width: 200px;
    }
    
    .header-right li {
        display: flex;
    }
    
    .header-right li a {
        font-size: 12px;
        padding: 7px 10px;
        gap: 5px;
    }
    
    .header-right li a i {
        font-size: 13px;
    }
    
    #excelUploadBtn {
        font-size: 12px;
        padding: 7px 10px;
    }
    
    #fullscreenToggle {
        display: flex;
    }
}

/* Laptop: 1025px – 1440px */
@media (min-width: 1025px) and (max-width: 1440px) {
    .top-header {
        left: 260px;
        width: calc(100% - 260px);
        padding: 0 20px;
    }
    
    .menu-btn {
        display: none;
    }
    
    .header-center {
        display: none;
    }
    
    .header-left {
        gap: 15px;
    }
    
    .header-right {
        gap: 12px;
    }
    
    .global-search {
        width: 250px;
    }
    
    .header-right li {
        display: flex;
    }
    
    #fullscreenToggle {
        display: flex;
    }
}

/* Desktop: 1441px – 1920px */
@media (min-width: 1441px) and (max-width: 1920px) {
    .top-header {
        left: 260px;
        width: calc(100% - 260px);
        padding: 0 30px;
    }
    
    .header-center {
        display: none;
    }
    
    .header-left {
        gap: 18px;
    }
    
    .header-right {
        gap: 15px;
    }
    
    .global-search {
        width: 300px;
    }
    
    .header-right li {
        display: flex;
    }
}

/* Large Screens: 1921px+ */
@media (min-width: 1921px) {
    .top-header {
        left: 260px;
        width: calc(100% - 260px);
        padding: 0 40px;
    }
    
    .header-center {
        display: none;
    }
    
    .header-left {
        gap: 20px;
    }
    
    .header-right {
        gap: 18px;
    }
    
    .global-search {
        width: 350px;
    }
    
    .header-right li {
        display: flex;
    }
    
    .header-right li a {
        font-size: 14px;
        padding: 9px 14px;
    }
}

/* Fullscreen Mode Styles */
.sidebar.sidebar-collapsed {
    width: 70px;
    transition: width 0.3s ease;
}

.sidebar.sidebar-collapsed .company-info,
.sidebar.sidebar-collapsed .sidebar-menu a > span,
.sidebar.sidebar-collapsed .submenu,
.sidebar.sidebar-collapsed .arrow,
.sidebar.sidebar-collapsed .float-end {
    display: none;
}

.sidebar.sidebar-collapsed .sidebar-header {
    padding: 10px;
}

.sidebar.sidebar-collapsed .company-logo1 {
    display: flex;
    justify-content: center;
    align-items: center;
}

.sidebar.sidebar-collapsed .company-logo1 img {
    width: 50px;
    height: auto;
}

.sidebar.sidebar-collapsed .sidebar-menu li a {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px 0;
}

.sidebar.sidebar-collapsed .sidebar-menu i {
    margin-right: 0;
    font-size: 22px;
}

.top-header.header-fullscreen {
    left: 70px;
    width: calc(100% - 70px);
}

.main-content.content-fullscreen {
    margin-left: 70px;
    transition: margin-left 0.3s ease;
}
</style>

<script>
function openSalaryCalculatorModal(e) {
    e.preventDefault();
    document.getElementById('salaryCalculatorModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeSalaryCalculatorModal() {
    document.getElementById('salaryCalculatorModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('salaryResults').style.display = 'none';
    document.getElementById('salaryCalculatorForm').reset();
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('salaryCalculatorModal');
    if (e.target === modal) {
        closeSalaryCalculatorModal();
    }
});

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('salaryCalculatorForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const inHand = parseFloat(document.getElementById('modalInHandSalary').value);
            
            if (!inHand || inHand <= 0) {
                showStatusPopup('error', 'Error', 'Operation failed');
                return;
            }
            
            // Calculate values
            const employeePfRate = 0.12;
            const employeeEsicRate = 0.0075;
            const employerPfRate = 0.13;
            const employerEsicRate = 0.0325;
            
            // Reverse calculation to get gross from in-hand
            const gross = inHand / (1 - employeePfRate * 0.6 - employeeEsicRate);
            const basic = gross * 0.6;
            const hra = gross * 0.4;
            const employeePf = basic * employeePfRate;
            const employeeEsic = gross * employeeEsicRate;
            const employerPf = basic * employerPfRate;
            const employerEsic = gross * employerEsicRate;
            const ctc = gross + employerPf + employerEsic;
            
            // Display results
            document.getElementById('resultGross').textContent = '? ' + gross.toFixed(2);
            document.getElementById('resultBasic').textContent = '? ' + basic.toFixed(2);
            document.getElementById('resultHra').textContent = '? ' + hra.toFixed(2);
            document.getElementById('resultEmpPf').textContent = '? ' + employeePf.toFixed(2);
            document.getElementById('resultEmpEsic').textContent = '? ' + employeeEsic.toFixed(2);
            document.getElementById('resultEmprPf').textContent = '? ' + employerPf.toFixed(2);
            document.getElementById('resultEmprEsic').textContent = '? ' + employerEsic.toFixed(2);
            document.getElementById('resultInHand').innerHTML = '<strong>? ' + inHand.toFixed(2) + '</strong>';
            document.getElementById('resultCtc').innerHTML = '<strong>? ' + ctc.toFixed(2) + '</strong>';
            
            document.getElementById('salaryResults').style.display = 'block';
        });
    }
});
</script>






