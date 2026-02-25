@extends('auth.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
.dashboard-wrapper{
   padding: 0%;
    margin-left: 65px;
    margin-top: 60px;
}

/* Mobile Dashboard Responsive */
@media (max-width: 768px) {
    .dashboard-wrapper {
        margin-left: 0 !important;
        margin-top: 70px;
        padding: 15px;
    }
    
    .stat-card {
        min-height: 100px;
    }
    
    .stat-number {
        font-size: 24px !important;
    }
    
    .stat-title {
        font-size: 12px;
    }
    
    .alert {
        padding: 12px !important;
        margin-bottom: 15px !important;
    }
    
    .alert h5 {
        font-size: 16px;
    }
    
    .table-responsive {
        font-size: 12px;
    }
    
    .btn-sm {
        font-size: 10px;
        padding: 4px 8px;
    }
}
.stat-card{
    border: none;
    border-radius: 14px;
    color: #fff;
    position: relative;
    overflow: hidden;
    min-height: 130px;
    
}
.stat-card i{
    position: absolute;
    right: 20px;
    bottom: 20px;
    font-size: 45px;
    opacity: 0.3;
}
.stat-title{
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.stat-number{
    font-size: 32px;
    font-weight: 700;
}
.table-card{
    border-radius: 14px;
    border: none;
}
.table thead{
    background: #f5f6fa;
}
.badge-role{
    padding: 6px 10px;
    font-size: 12px;
}
.welcome-card{
    background: linear-gradient(135deg, #4f46e5, #3b82f6);
    color: #fff;
    border-radius: 16px;
}

.birthday-alert {
    animation: slideInDown 0.8s ease-out;
}

.hiring-alert {
    animation: slideInDown 0.8s ease-out;
}

.bill-alert {
    animation: slideInDown 0.8s ease-out;
}

@keyframes slideInDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.dashboard-wrapper {
    transform: scale(0.85);
    transform-origin: top center;
    width: 100%;
}

body {
    overflow-x: hidden;
}
</style>
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        body {
    background: linear-gradient(145deg, #f0f2f5 0%, #e6e9f0 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}


        /* Main Card */
        .employee-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            width: 100%;
            max-width: 400px;
            border-radius: 32px;
            box-shadow: 
                0 20px 35px -8px rgba(0, 0, 0, 0.2),
                0 8px 18px -6px rgba(0, 0, 0, 0.1),
                inset 0 1px 1px rgba(255, 255, 255, 0.7);
            padding: 28px 24px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: transform 0.2s ease;
        }

        .employee-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 40px -10px rgba(0, 0, 0, 0.3);
        }

        /* Header */
        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #eaeef5;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e2b3c;
            letter-spacing: -0.3px;
        }

        .icon-badge {
            background: #1e3a5f;
            color: white;
            font-size: 1.2rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 10px rgba(0, 50, 100, 0.2);
        }

        /* Row styling */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 10px 0;
            border-bottom: 1px dashed #d0d9e8;
        }

        .info-row:last-of-type {
            border-bottom: none;
        }

        .label {
            font-size: 1.1rem;
            font-weight: 500;
            color: #3d4e66;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .label span {
            font-size: 1.2rem;
        }

        .value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0b1f33;
        }

        .value small {
            font-size: 0.9rem;
            font-weight: 400;
            color: #5e6f88;
            margin-left: 5px;
        }

        /* Total row special */
        .total-row {
            background: #f4f7fd;
            margin: 4px 0 12px 0;
            padding: 14px 16px;
            border-radius: 24px;
            border: 1px solid #cfdcec;
        }

        .total-row .label {
            font-weight: 600;
            color: #022b49;
        }

        .total-row .value {
            font-size: 1.8rem;
            color: #003057;
        }

        /* Progress bar mini */
        .progress-section {
            margin: 18px 0 8px 0;
            background: #ecf1f7;
            border-radius: 30px;
            padding: 12px 15px;
        }

        .progress-item {
            margin-bottom: 12px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .progress-bar-bg {
            width: 100%;
            height: 8px;
            background: #dbe1ec;
            border-radius: 30px;
            overflow: hidden;
        }

        .progress-fill-male {
            height: 8px;
            width: 73%;
            background: linear-gradient(90deg, #2563eb, #3898ff);
            border-radius: 30px;
        }

        .progress-fill-female {
            height: 8px;
            width: 23%;
            background: linear-gradient(90deg, #d43f8d, #f472b6);
            border-radius: 30px;
        }

        /* Footer summary */
        .footer-note {
            background: #e9edf4;
            text-align: center;
            padding: 12px;
            border-radius: 40px;
            margin-top: 20px;
            font-size: 0.95rem;
            color: #1f3a5f;
            font-weight: 500;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }

        .footer-note strong {
            font-weight: 700;
            color: #0e2b4b;
        }

        /* Responsive */
        @media (max-width: 450px) {
            .employee-card {
                padding: 20px 16px;
            }
            .card-header h2 {
                font-size: 1.3rem;
            }
        }
    </style>
    <style>
        /* ===============================
   TABLE CARD DESIGN
================================ */

.table-card {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    border: none;
    overflow: hidden;
    max-width: 700px;
}

/* Header */
.table-card .card-header {
   
    border-bottom: 1px solid #f1f1f1;
    background: #ffffff;
}

.table-card .card-header h5 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

/* Table */
.table-card table {
    margin-bottom: 0;
}

.table-card thead {
    background: #f8f9fc;
}

.table-card thead th {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #666;
    font-weight: 600;
    
    border-bottom: 1px solid #eaeaea;
}

/* Table Body */
.table-card tbody td {
    
    font-size: 14px;
    color: #444;
    border-bottom: 1px solid #f5f5f5;
}

.table-card tbody tr:last-child td {
    border-bottom: none;
}

/* Row Hover */
.table-card tbody tr {
    transition: 0.2s ease;
}

.table-card tbody tr:hover {
    background: #f4f7ff;
    transform: scale(1.002);
}

/* ===============================
   CONTACT BUTTON STYLE
================================ */

.table-card .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.table-card .btn-success {
    background: #1cc88a;
    border: none;
}

.table-card .btn-success:hover {
    background: #17a673;
}

.table-card .btn-primary {
    background: #4e73df;
    border: none;
}

.table-card .btn-primary:hover {
    background: #2e59d9;
}

/* ===============================
   RESPONSIVE IMPROVEMENT
================================ */

@media (max-width: 768px) {
    .table-card thead {
        display: none;
    }

    .table-card table,
    .table-card tbody,
    .table-card tr,
    .table-card td {
        display: block;
        width: 100%;
    }

    .table-card tr {
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .table-card td {
       
        text-align: right;
        position: relative;
    }

    .table-card td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        font-weight: 600;
        text-align: left;
        color: #666;
    }
}


.male-all-employee{
    display: flex;
    gap: 30px;
    align-items: flex-start;
}


.table-card{
    margin-left: 50px;
}
    </style>
    <style>
        /* ============================= */
/* 📱 MOBILE RESPONSIVE FIX */
/* ============================= */

@media (max-width: 768px) {

/* Employee Structure Card */
.employee-card {
    padding: 15px;
}

.card-header h2 {
    font-size: 18px;
}

.info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

.info-row .value {
    font-size: 14px;
}

.progress-header {
    font-size: 13px;
}

.footer-note {
    font-size: 13px;
    text-align: center;
}

/* Table Cards */
.table-card {
    margin: 10px;
    border-radius: 12px;
}

.card-header {
    /* flex-direction: column !important; */
    align-items: flex-start !important;
    gap: 10px;
}

.card-header h5 {
    font-size: 15px;
}

/* Search box full width */
.employee-search {
    max-width: 100%;
    width: 100%;
}

/* Table font smaller */
.table th,
.table td {
    font-size: 13px;
    padding: 8px;
}

/* Contact buttons smaller */
.btn-sm {
    padding: 4px 6px;
    font-size: 12px;
}

/* Make table scroll smooth */
.table-responsive {
    overflow-x: auto;
}

/* Sticky header mobile fix */
.employee-table-wrapper {
    max-height: 300px;
}

/* Activity log badges smaller */
.badge {
    font-size: 11px;
    padding: 5px 6px;
}

/* Pending approval buttons full width */
.table .text-end {
    text-align: left !important;
}

.table .text-end .btn {
    width: 100%;
    margin-bottom: 5px;
}
}


/* ============================= */
/* 📱 Extra Small Devices (≤480px) */
/* ============================= */

@media (max-width: 480px) {

.employee-card {
    padding: 12px;
}

.icon-badge {
    font-size: 18px;
}

.card-header h2 {
    font-size: 16px;
}

.info-row .label {
    font-size: 13px;
}

.progress-fill-male,
.progress-fill-female {
    height: 6px;
}

.footer-note {
    font-size: 12px;
}

/* Tables more compact */
.table th,
.table td {
    font-size: 12px;
}
}


@media (max-width: 992px){
    .male-all-employee{
        flex-direction: column;
        height: auto;
    }

    .table-card{
        margin-left: 0;
        max-width: 100%;
    }

    .employee-card{
        max-width: 100%;
    }
}


    </style>

<div class="dashboard-wrapper">

    <!-- 🎉 Birthday Alert -->
    @if(isset($todayBirthdays) && $todayBirthdays->count() > 0)
    <div class="alert alert-info birthday-alert mb-4" style="background: linear-gradient(135deg, #ff6b6b, #ffa500); color: white; border: none; border-radius: 12px;">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-birthday-cake fa-2x me-3"></i>
            <div>
                <h5 class="mb-1">🎉 Today's Birthdays!</h5>
                <p class="mb-0">
                    @foreach($todayBirthdays as $employee)
                        <strong>{{ $employee->full_name }}</strong> ({{ $employee->department }})@if(!$loop->last), @endif
                    @endforeach
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- 🔥 Hiring Required Alert -->
    @if(isset($activeJobOpenings) && $activeJobOpenings->count() > 0)
    <div class="alert alert-warning hiring-alert mb-4" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; border-radius: 12px;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-briefcase fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">🔥 Hiring Required</h5>
                    <p class="mb-0">{{ $activeJobOpenings->count() }} active job opening(s) need attention</p>
                </div>
            </div>
            <button class="btn btn-light btn-sm" onclick="showHiringModal()">
                <i class="fa-solid fa-eye"></i> View Details
            </button>
        </div>
    </div>
    @endif

    <!-- 💰 Due Bills Alert -->
    <div id="dueBillsAlert" class="alert alert-warning bill-alert mb-4" style="background: linear-gradient(135deg, #f39c12, #e74c3c); color: white; border: none; border-radius: 12px; display: none;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-file-invoice fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">💰 Plaese Paid Your billing !</h5>
                    <p class="mb-0" id="dueBillsList">Loading bills...</p>
                </div>
            </div>
            <!-- <button class="btn btn-light btn-sm" onclick="showBillsModal()">
                <i class="fa-solid fa-eye"></i> View Details
            </button> -->
        </div>
    </div>

    <!-- 💸 Auto-Generated Salary Alert -->
    <div id="autoSalaryAlert" class="alert alert-success bill-alert mb-4" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; border-radius: 12px; display: none;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-money-bill-wave fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">💸 Monthly Salary Generated!</h5>
                    <p class="mb-0" id="autoSalaryText">Checking for auto-generated salaries...</p>
                </div>
            </div>
            <button class="btn btn-light btn-sm" onclick="viewSalaryRecords()">
                <i class="fa-solid fa-eye"></i> View Salary
            </button>
        </div>
    </div>

    <!-- 📞 Today's Callbacks Alert -->
    @if(isset($todayCallbacks) && $todayCallbacks->count() > 0)
    <div class="alert alert-warning bill-alert mb-4" style="background: linear-gradient(135deg, #ff9500, #ff6b35); color: white; border: none; border-radius: 12px;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-phone fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">📞 आज Callback करना है!</h5>
                    <p class="mb-0">
                        @foreach($todayCallbacks as $callback)
                            <strong>{{ $callback->name }}</strong>@if(!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
            </div>
            <a href="{{ url('/admin/callbacks') }}" class="btn btn-light btn-sm">
                <i class="fa-solid fa-eye"></i> View Callbacks
            </a>
        </div>
    </div>
    @endif
    

    <!-- 🔹 Stats Cards -->
    <div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-info">
                <div class="card-body">
                    <div class="stat-title">Employee Leads</div>
                    <div class="stat-number">{{ $stats['totalLeads'] ?? 0 }}</div>
                    <i class="bi bi-person-plus-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-primary">
                <div class="card-body">
                    <div class="stat-title">Total Employees</div>
                    <div class="stat-number">{{ $stats['totalHiredEmployees'] ?? 0 }}</div>
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning">
                <div class="card-body">
                    <div class="stat-title">Pending Approvals</div>
                    <div class="stat-number">{{ $stats['pendingApprovals'] ?? 0 }}</div>
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>

        <!-- <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-success">
                <div class="card-body">
                    <div class="stat-title">Total Admins</div>
                    <div class="stat-number">{{ $stats['totalAdmins'] ?? 0 }}</div>
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
        </div> -->

        <!-- <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-info">
                <div class="card-body">
                    <div class="stat-title">Total Clients</div>
                    <div class="stat-number">{{ $stats['totalClients'] ?? 0 }}</div>
                    <i class="bi bi-briefcase-fill"></i>
                </div>
            </div>
        </div> -->
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning">
                <div class="card-body">
                    <div class="stat-title">New Callbacks</div>
                    <div class="stat-number">{{ $stats['totalCallbacks'] ?? 0 }}</div>
                    <i class="bi bi-telephone-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-success">
                <div class="card-body">
                    <div class="stat-title">Total Employee Interviews</div>
                    <div class="stat-number">{{ $stats['totalInterviews'] ?? 0 }}</div>
                    <i class="bi bi-chat-dots-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-danger">
                <div class="card-body">
                    <div class="stat-title">Total Employee rejected Interviews</div>
                    <div class="stat-number">{{ $stats['rejectedInterviews'] ?? 0 }}</div>
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning">
                <div class="card-body">
                    <div class="stat-title">New Tickets</div>
                    <div class="stat-number">{{ $stats['newTickets'] ?? 0 }}</div>
                    <i class="bi bi-ticket-perforated"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-secondary">
                <div class="card-body">
                    <div class="stat-title">Active Interns</div>
                    <div class="stat-number">{{ $stats['activeInterns'] ?? 0 }}</div>
                    <i class="bi bi-ticket-detailed"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ url('/admin/interns/ongoing-list') }}" class="text-decoration-none">
                <div class="card stat-card bg-success">
                    <div class="card-body">
                        <div class="stat-title">Intern Payment</div>
                        <div class="stat-number" style="font-size: 16px; line-height: 1.4;">
                            Total: ₹{{ number_format($stats['totalInternPayment'] ?? 0) }}<br>
                            <small style="font-size: 13px; opacity: 0.9;">Received: ₹{{ number_format($stats['receivedInternPayment'] ?? 0) }}</small><br>
                            <small style="font-size: 13px; opacity: 0.85;">Pending: ₹{{ number_format(($stats['totalInternPayment'] ?? 0) - ($stats['receivedInternPayment'] ?? 0)) }}</small>
                        </div>
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-danger">
                <div class="card-body">
                    <div class="stat-title"> total employee salary</div>
                    <div class="stat-number">#</div>
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning">
                <div class="card-body">
                    <div class="stat-title">Employee Hired</div>
                    <div class="stat-number">{{ $stats['employeeHired'] ?? 0 }}</div>
                    <i class="bi bi-ticket-perforated"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-secondary">
                <div class="card-body">
                    <div class="stat-title">Selected Employee</div>
                    <div class="stat-number">{{ $stats['selectedEmployee'] ?? 0 }}</div>
                    <i class="bi bi-ticket-detailed"></i>
                </div>
            </div>
        </div>
    </div>
  
    

<style>
 .filter-container {
    background: #ffffff;
    padding: 18px 20px;
    border-radius: 14px;
    display: flex;
    gap: 25px;
    align-items: end;
    flex-wrap: wrap;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
    border: 1px solid #f1f1f1;
    width: 50%;
    margin-top: 30px;
}

.filter-item {
    display: flex;
    flex-direction: column;
    
}
.filter-item {
    width: 230px;
}

.search-item {
    flex: 1;
    
}

.filter-label {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 6px;
    color: #555;
}

.filter-control {
    height: 44px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding-left: 12px;
    transition: 0.2s ease;
}

.filter-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13,110,253,0.1);
}

.search-wrapper {
    position: relative;
}

.search-wrapper i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 14px;
}

.search-wrapper input {
    padding-left: 35px;
}
.search-item {
    width: 350px;   /* 👈 Yaha control karo */
    flex: unset;    /* important */
}

/* Responsive */
@media (max-width: 768px) {
    .filter-item,
    .search-item {
        width: 100%;
    }
}
</style>

<!-- Journey Filter Bar -->
<!-- Journey Filter Bar -->
<div class="journey-filter-bar">

    <div class="filter-group">
        <label>Date</label>
        <select id="journeyDateFilter" onchange="filterJourneyData()">
            <option value="">All Time</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
        </select>
    </div>

    <div class="filter-group search-group">
        <label>Search</label>
        <div class="search-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" id="journeySearchBar"
                   placeholder="Search by name, phone, email..."
                   oninput="filterJourneyData()">
        </div>
    </div>
<!-- 
    <div class="filter-group">
        <label>&nbsp;</label>
        <button class="reset-btn" onclick="resetJourneyFilter()">
            Reset
        </button>
    </div> -->

</div>

<style>
.journey-filter-container {
    display: flex;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 20px;
}
.filter-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s ease;
}
.filter-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}
.filter-left {
    width: 280px;
}
.filter-right {
    flex: 1;
    max-width: 450px;
}
.filter-icon {
    font-size: 32px;
    line-height: 1;
}
.filter-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
}
.filter-control {
    height: 42px;
    border-radius: 10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 0 14px;
    font-size: 14px;
    background: rgba(255, 255, 255, 0.95);
    color: #333;
    transition: all 0.2s ease;
}
.filter-control:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.8);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
}
@media (max-width: 768px) {
    .journey-filter-container {
        flex-direction: column;
    }
    .filter-left,
    .filter-right {
        width: 100%;
        max-width: 100%;
    }
}
.journey-card {
    background: white;
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.journey-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}
.journey-header {
    /* display: flex; */
    justify-content: space-between;
    align-items: center;
    padding-bottom: 8px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
}
.candidate-info h5 {
    margin: 0;
    color: #2d3748;
    font-size: 15px;
    font-weight: 600;
}
.candidate-meta {
    color: #718096;
    font-size: 13px;
    margin-top: 3px;
    line-height: 1.4;
}
.status-dropdown {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 6px;
}
.collapse {
    display: none;
}
.collapse.show {
    display: block;
}
.journey-wrapper {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    margin-bottom: 20px;
    margin-top: 20px;
}
.journey-column {
    min-width: 0;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 12px;
}
.journey-column h4 {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid rgba(0,0,0,0.1);
}
.btn.w-100 {
    font-size: 11px;
    padding: 6px;
    border-radius: 6px;
    font-weight: 600;
}
/* @media (max-width: 1400px) {
    .journey-wrapper {
        grid-template-columns: repeat(4, 1fr);
    }
} */
@media (max-width: 992px) {
    .journey-wrapper {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .journey-wrapper {
        grid-template-columns: 1fr;
    }
}
</style>
<style>
    .journey-filter-bar {
    display: flex;
    align-items: end;
    gap: 20px;              /* normal spacing */
    padding: 20px;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    margin-bottom: 25px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filter-group label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.filter-group select,
.filter-group input {
    height: 42px;
    border-radius: 8px;
    border: 1px solid #e0e6ed;
    padding: 0 12px;
    font-size: 14px;
    transition: 0.2s ease;
}

.filter-group select:focus,
.filter-group input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Search Input */
.search-wrapper {
    position: relative;
}

.search-wrapper input {
    padding-left: 38px;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    opacity: 0.6;
}

/* Reset Button */
.reset-btn {
    height: 42px;
    padding: 0 18px;
    border-radius: 8px;
    border: none;
    background: #ef4444;
    color: white;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}

.reset-btn:hover {
    background: #dc2626;
}

/* Responsive */
@media (max-width: 768px) {
    .journey-filter-bar {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-group {
        width: 100%;
    }
}
/* Left filter fixed width */
.filter-group:first-child {
    width: 220px;
}

/* Search group expand karega */
.search-group {
    flex: 1;              /* ye important hai */
}

/* Search input full width */
.search-group input {
    width: 100%;
}
</style>

<div class="journey-wrapper">
<!-- Leads Column -->
<div class="journey-column">
    <h4 class="text-primary">📋 Leads ({{ isset($leads) ? $leads->count() : 0 }})</h4>
    @if(isset($leads) && $leads->count() > 0)
    @foreach($leads as $lead)
    <div class="journey-card" data-date="{{ $lead->created_at }}" data-name="{{ strtolower($lead->name) }}" data-meta="{{ strtolower(($lead->phone ?? $lead->number) . ' ' . $lead->role) }}">
        <button class="journey-header w-100 border-0 bg-transparent text-start" type="button" data-target="#lead-{{ $lead->id }}">
            <div class="candidate-info">
                <h5>{{ $lead->name }}</h5>
                <div class="candidate-meta">{{ $lead->phone ?? $lead->number }} | {{ $lead->role }}</div>
            </div>
        </button>
        <div id="lead-{{ $lead->id }}" class="collapse">
            <div class="mt-2">
                <select class="form-select form-select-sm status-dropdown" onchange="showStatusModal({{ $lead->id }}, this.value)">
                    <option value="">Select Status</option>
                    <option value="Interested">Interested</option>
                    <option value="Call Back">Call Back</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Not Interested">Not Interested</option>
                    <option value="Wrong Number">Wrong Number</option>
                </select>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    <a href="{{ url('/admin/leads') }}" class="btn btn-sm btn-primary w-100">View All</a>
</div>

<!-- Callbacks Column -->
<div class="journey-column">
    <h4 class="text-warning">📞 Callbacks ({{ isset($callbacks) ? $callbacks->count() : 0 }})</h4>
    @if(isset($callbacks) && $callbacks->count() > 0)
    @foreach($callbacks as $callback)
    <div class="journey-card" data-date="{{ $callback->callback_date }}" data-name="{{ strtolower($callback->name) }}" data-meta="{{ strtolower($callback->number) }}">
        <button class="journey-header w-100 border-0 bg-transparent text-start" type="button" data-target="#callback-{{ $callback->id }}">
            <div class="candidate-info">
                <h5>{{ $callback->name }}</h5>
                <div class="candidate-meta">{{ $callback->number }} | {{ \Carbon\Carbon::parse($callback->callback_date)->format('d M Y') }}</div>
            </div>
        </button>
        <div id="callback-{{ $callback->id }}" class="collapse">
            <div class="mt-2">
                <select class="form-select form-select-sm status-dropdown" onchange="showCallbackStatusModal({{ $callback->id }}, this.value)">
                    <option value="">Select Status</option>
                    <option value="interested">Interested</option>
                    <option value="rejected">Rejected</option>
                    <option value="not_interested">Not Interested</option>
                    <option value="wrong_number">Wrong Number</option>
                </select>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    <a href="{{ url('/admin/callbacks') }}" class="btn btn-sm btn-warning w-100">View All</a>
</div>

<!-- Interested Column -->
<div class="journey-column">
    <h4 class="text-success">✅ Interested ({{ isset($interestedCandidates) ? $interestedCandidates->count() : 0 }})</h4>
    @if(isset($interestedCandidates) && $interestedCandidates->count() > 0)
    @foreach($interestedCandidates as $candidate)
    <div class="journey-card" data-date="{{ $candidate->created_at }}" data-name="{{ strtolower($candidate->name) }}" data-meta="{{ strtolower(($candidate->phone ?? $candidate->number) . ' ' . ($candidate->role ?? '')) }}">
        <button class="journey-header w-100 border-0 bg-transparent text-start" type="button" data-target="#interested-{{ $candidate->id }}">
            <div class="candidate-info">
                <h5>{{ $candidate->name }}</h5>
                <div class="candidate-meta">{{ $candidate->phone ?? $candidate->number }} | {{ $candidate->role ?? 'N/A' }}</div>
            </div>
        </button>
        <div id="interested-{{ $candidate->id }}" class="collapse">
            <div class="mt-2">
                <a href="{{ url('/admin/interviews/create?lead_id=' . $candidate->id) }}" class="btn btn-sm btn-primary w-100">Schedule Interview</a>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    <a href="{{ url('/admin/leads/interested') }}" class="btn btn-sm btn-success w-100">View All</a>
</div>

<!-- Interviews Column -->
<div class="journey-column">
    <h4 class="text-info">🎤 Interviews ({{ isset($interviews) ? $interviews->count() : 0 }})</h4>
    @if(isset($interviews) && $interviews->count() > 0)
    @foreach($interviews as $interview)
    <div class="journey-card" data-date="{{ $interview->interview_date }}" data-name="{{ strtolower($interview->lead->name ?? '') }}" data-meta="{{ strtolower($interview->interview_round ?? '') }}">
        <button class="journey-header w-100 border-0 bg-transparent text-start" type="button" data-target="#interview-{{ $interview->id }}">
            <div class="candidate-info">
                <h5>{{ $interview->lead->name ?? 'N/A' }}</h5>
                <div class="candidate-meta">{{ \Carbon\Carbon::parse($interview->interview_date)->format('d M Y') }} | {{ $interview->interview_round }}</div>
            </div>
        </button>
        <div id="interview-{{ $interview->id }}" class="collapse">
            <div class="mt-2">
                <select class="form-select form-select-sm status-dropdown" onchange="showInterviewResultModal({{ $interview->id }}, this.value)">
                    <option value="">Select Result</option>
                    <option value="Selected">Selected</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    <a href="{{ url('/admin/interviews') }}" class="btn btn-sm btn-info w-100">View All</a>
</div>

<!-- Selected Column -->
<div class="journey-column">
    <h4 class="text-dark">🎯 Selected ({{ isset($selectedInterviews) ? $selectedInterviews->count() : 0 }})</h4>
    @if(isset($selectedInterviews) && $selectedInterviews->count() > 0)
    @foreach($selectedInterviews as $interview)
    <div class="journey-card" data-date="{{ $interview->updated_at }}" data-name="{{ strtolower($interview->lead->name ?? '') }}" data-meta="{{ strtolower(($interview->candidate_email ?? '') . ' ' . ($interview->job_role ?? '')) }}">
        <button class="journey-header w-100 border-0 bg-transparent text-start" type="button" data-target="#selected-{{ $interview->id }}">
            <div class="candidate-info">
                <h5>{{ $interview->lead->name ?? 'N/A' }}</h5>
                <div class="candidate-meta">{{ $interview->candidate_email }} | {{ $interview->job_role }}</div>
            </div>
        </button>
        <div id="selected-{{ $interview->id }}" class="collapse">
            <div class="mt-2">
                <button class="btn btn-sm btn-primary w-100" onclick="showWelcomeLetterModal({{ $interview->id }})">Send Welcome Letter</button>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    <a href="{{ url('/admin/interviews/selected') }}" class="btn btn-sm btn-dark w-100">View All</a>
</div>

<!-- Documents Column -->
<div class="journey-column">
    <h4 class="text-secondary">📄 Documents ({{ isset($employeesWithDocuments) ? $employeesWithDocuments->count() : 0 }})</h4>
    @if(isset($employeesWithDocuments) && $employeesWithDocuments->count() > 0)
    @foreach($employeesWithDocuments as $employee)
    <div class="journey-card" data-date="{{ $employee->created_at }}" data-name="{{ strtolower($employee->first_name . ' ' . $employee->last_name) }}" data-meta="{{ strtolower(($employee->email ?? '') . ' ' . ($employee->department ?? '')) }}">
        <button class="journey-header w-100 border-0 bg-transparent text-start" type="button" data-target="#doc-{{ $employee->id }}">
            <div class="candidate-info">
                <h5>{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                <div class="candidate-meta">{{ $employee->email }} | {{ $employee->department }}</div>
            </div>
        </button>
        <div id="doc-{{ $employee->id }}" class="collapse">
            <div class="mt-2">
                <a href="{{ url('/admin/employees/documents') }}" class="btn btn-sm btn-secondary w-100">View Documents</a>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    <a href="{{ url('/admin/employees/documents') }}" class="btn btn-sm btn-secondary w-100">View All</a>
</div>

<!-- Hired Column -->
<div class="journey-column">
    <h4 style="color: #667eea;">👔 Hired ({{ isset($hiredEmployees) ? $hiredEmployees->count() : 0 }})</h4>
    @if(isset($hiredEmployees) && $hiredEmployees->count() > 0)
    @foreach($hiredEmployees as $employee)
    <div class="journey-card" data-date="{{ $employee->created_at }}" data-name="{{ strtolower($employee->first_name . ' ' . $employee->last_name) }}" data-meta="{{ strtolower(($employee->email ?? '') . ' ' . ($employee->department ?? '')) }}">
        <div class="journey-header">
            <div class="candidate-info">
                <h5>{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                <div class="candidate-meta">{{ $employee->email }} | {{ $employee->department }}</div>
            </div>
            <span class="badge bg-success">{{ $employee->employee_status }}</span>
        </div>
    </div>
    @endforeach
    @endif
    <a href="{{ url('/admin/employees/hired') }}" class="btn btn-sm w-100" style="background: #667eea; color: white;">View All</a>
</div>
</div>

<script>
function filterJourneyData() {
    const dateFilter = document.getElementById('journeyDateFilter').value;
    const searchTerm = document.getElementById('journeySearchBar').value.toLowerCase();
    const allCards = document.querySelectorAll('.journey-card');
    
    allCards.forEach(card => {
        const cardDate = new Date(card.getAttribute('data-date'));
        const cardName = card.getAttribute('data-name') || '';
        const cardMeta = card.getAttribute('data-meta') || '';
        
        let dateMatch = true;
        let searchMatch = true;
        
        // Date filter logic
        if (dateFilter) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (dateFilter === 'today') {
                dateMatch = cardDate.toDateString() === today.toDateString();
            } else if (dateFilter === 'week') {
                const weekAgo = new Date(today);
                weekAgo.setDate(today.getDate() - 7);
                dateMatch = cardDate >= weekAgo;
            } else if (dateFilter === 'month') {
                const monthAgo = new Date(today);
                monthAgo.setMonth(today.getMonth() - 1);
                dateMatch = cardDate >= monthAgo;
            }
        }
        
        // Search filter logic
        if (searchTerm) {
            searchMatch = cardName.includes(searchTerm) || cardMeta.includes(searchTerm);
        }
        
        // Show/hide card
        if (dateMatch && searchMatch) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

document.querySelectorAll('.journey-header').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        if (targetId) {
            const targetElement = document.querySelector(targetId);
            targetElement.classList.toggle('show');
        }
    });
});
</script>

<!-- Status Modal for Leads -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Lead Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="leadId">
                <input type="hidden" id="leadStatus">
                <div class="mb-3">
                    <label class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="leadReason" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitLeadStatus()">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal for Callbacks -->
<div class="modal fade" id="callbackStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Callback Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="callbackId">
                <input type="hidden" id="callbackStatus">
                <div class="mb-3">
                    <label class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="callbackReason" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitCallbackStatus()">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Interview Result Modal -->
<div class="modal fade" id="interviewResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Interview Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="interviewId">
                <input type="hidden" id="interviewResult">
                <div class="mb-3" id="rejectionReasonDiv" style="display:none;">
                    <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectionReason" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitInterviewResult()">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Letter Modal -->
<div class="modal fade" id="welcomeLetterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Welcome Letter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="welcomeInterviewId">
                <div class="mb-3">
                    <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="joiningDate" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Current CTC <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="currentCtc" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">In-Hand Salary <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="inHandSalary" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitWelcomeLetter()">Send Letter</button>
            </div>
        </div>
    </div>
</div>

<script>
function showStatusModal(leadId, status) {
    if (!status) return;
    document.getElementById('leadId').value = leadId;
    document.getElementById('leadStatus').value = status;
    document.getElementById('leadReason').value = '';
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function submitLeadStatus() {
    const leadId = document.getElementById('leadId').value;
    const status = document.getElementById('leadStatus').value;
    const reason = document.getElementById('leadReason').value;
    
    if (!reason) {
        alert('Please enter a reason');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found');
        return;
    }
    
    fetch(`/admin/leads/${leadId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ condition_status: status, reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });
}

function showCallbackStatusModal(callbackId, status) {
    if (!status) return;
    document.getElementById('callbackId').value = callbackId;
    document.getElementById('callbackStatus').value = status;
    document.getElementById('callbackReason').value = '';
    new bootstrap.Modal(document.getElementById('callbackStatusModal')).show();
}

function submitCallbackStatus() {
    const callbackId = document.getElementById('callbackId').value;
    const status = document.getElementById('callbackStatus').value;
    const reason = document.getElementById('callbackReason').value;
    
    if (!reason) {
        alert('Please enter a reason');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found');
        return;
    }
    
    fetch(`/admin/callbacks/${callbackId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status, reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('callbackStatusModal')).hide();
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });
}

function showInterviewResultModal(interviewId, result) {
    if (!result) return;
    document.getElementById('interviewId').value = interviewId;
    document.getElementById('interviewResult').value = result;
    document.getElementById('rejectionReason').value = '';
    
    if (result === 'Rejected') {
        document.getElementById('rejectionReasonDiv').style.display = 'block';
    } else {
        document.getElementById('rejectionReasonDiv').style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('interviewResultModal')).show();
}

function submitInterviewResult() {
    const interviewId = document.getElementById('interviewId').value;
    const result = document.getElementById('interviewResult').value;
    const reason = document.getElementById('rejectionReason').value;
    
    if (result === 'Rejected' && !reason) {
        alert('Please enter rejection reason');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found');
        return;
    }
    
    fetch(`/admin/interviews/${interviewId}/update-result`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ result: result, rejection_reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('interviewResultModal')).hide();
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating result');
    });
}

function showWelcomeLetterModal(interviewId) {
    document.getElementById('welcomeInterviewId').value = interviewId;
    document.getElementById('joiningDate').value = '';
    document.getElementById('currentCtc').value = '';
    document.getElementById('inHandSalary').value = '';
    new bootstrap.Modal(document.getElementById('welcomeLetterModal')).show();
}

function submitWelcomeLetter() {
    const interviewId = document.getElementById('welcomeInterviewId').value;
    const joiningDate = document.getElementById('joiningDate').value;
    const ctc = document.getElementById('currentCtc').value;
    const inHand = document.getElementById('inHandSalary').value;
    
    if (!joiningDate || !ctc || !inHand) {
        alert('Please fill all fields');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found');
        return;
    }
    
    fetch(`/admin/interviews/${interviewId}/send-welcome`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            joining_date: joiningDate, 
            current_ctc: ctc, 
            in_hand_salary: inHand 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('welcomeLetterModal')).hide();
            alert(data.message);
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                location.reload();
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending welcome letter');
    });
}
</script>

  <!---employee male - female--->
  <div class="male-all-employee">
    <div class="employee-card">
        
        <!-- Header -->
        <div class="card-header">
            <div class="icon-badge">👥</div>
            <h2>Employee Structure</h2>
        </div>

        <!-- Total row (highlighted) -->
        <div class="info-row total-row">
            <span class="label">📋 Total</span>
            <span class="value">{{ $stats['totalHiredEmployees'] ?? 0 }}</span>
        </div>

        <!-- Male -->
        <div class="info-row">
            <span class="label"><span>♂️</span> Male</span>
            <span class="value">{{ $stats['malePercentage'] ?? 0 }}% <small>({{ $stats['maleEmployees'] ?? 0 }})</small></span>
        </div>

        <!-- Female -->
        <div class="info-row">
            <span class="label"><span>♀️</span> Female</span>
            <span class="value">{{ $stats['femalePercentage'] ?? 0 }}% <small>({{ $stats['femaleEmployees'] ?? 0 }})</small></span>
        </div>

        <!-- Visual progress bars (extra, but keeps UI rich) -->
        <div class="progress-section">
            <div class="progress-item">
                <div class="progress-header">
                    <span>👨 Male</span>
                    <span>{{ $stats['malePercentage'] ?? 0 }}%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill-male" style="width: {{ $stats['malePercentage'] ?? 0 }}%;"></div>
                </div>
            </div>
            <div class="progress-item">
                <div class="progress-header">
                    <span>👩 Female</span>
                    <span>{{ $stats['femalePercentage'] ?? 0 }}%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill-female" style="width: {{ $stats['femalePercentage'] ?? 0 }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Summary footer -->
        <div class="footer-note">
            👤 <strong>{{ $stats['maleEmployees'] ?? 0 }} Male</strong> · <strong>{{ $stats['femaleEmployees'] ?? 0 }} Female</strong> · Total {{ $stats['totalHiredEmployees'] ?? 0 }}
        </div>

    </div>
    
    <!-- 🔹 All Employees Table -->
    @if(isset($allEmployees) && $allEmployees->count() > 0)

<style>
/* Scroll Wrapper */
.employee-table-wrapper {
    max-height: 400px; /* Height control yaha se */
    overflow-y: auto;
}

/* Sticky Header */
.employee-table-wrapper thead th {
    position: sticky;
    top: 0;
    background: #ffffff;
    z-index: 2;
    box-shadow: 0 2px 2px rgba(0,0,0,0.05);
}

/* Name wrap fix */
.table td {
    white-space: nowrap;
}

/* Search box styling */
.employee-search {
    max-width: 250px;
}

/* Smooth scrollbar */
.employee-table-wrapper::-webkit-scrollbar {
    width: 6px;
}

.employee-table-wrapper::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}
</style>


<div class="card table-card mb-4 shadow-sm">
    
    <!-- Header -->
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        
        <h5 class="mb-0 fw-semibold">
            👥 All Employees ({{ $allEmployees->count() }})
        </h5>

        <!-- Search -->
        <input type="text"
               id="employeeSearch"
               class="form-control form-control-sm employee-search"
               placeholder="🔍 Search employee...">
    </div>


    <!-- Table -->
    <div class="card-body p-0">
        <div class="table-responsive employee-table-wrapper">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Platform</th>
                        <th class="text-center">Contact</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody">
                    
                    @foreach($allEmployees as $employee)
                    <tr>
                        <td>
                            <strong>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </strong>
                        </td>

                        <td>
                            {{ $employee->department ?? 'N/A' }}
                        </td>

                       <td>
    @if($employee->platform)
        <span class="badge bg-info">
            {{ ucfirst(str_replace('_', ' ', $employee->platform)) }}
        </span>
    @else
        <span class="text-muted">N/A</span>
    @endif
</td>


                        <td class="text-center">
                            
                            @if($employee->phone)
                            <a href="tel:{{ $employee->phone }}"
                               class="btn btn-sm btn-success me-1"
                               title="Call">
                                <i class="bi bi-telephone-fill"></i>
                            </a>
                            @endif

                            @if($employee->email)
                            <a href="mailto:{{ $employee->email }}"
                               class="btn btn-sm btn-primary me-1"
                               title="Email">
                                <i class="bi bi-envelope-fill"></i>
                            </a>
                            @endif

                            @if($employee->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $employee->phone) }}"
                               target="_blank"
                               class="btn btn-sm btn-success"
                               title="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            @endif

                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Live Search Script -->
<script>
document.getElementById("employeeSearch").addEventListener("keyup", function () {

    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#employeeTableBody tr");

    rows.forEach(function (row) {

        let name = row.cells[0].innerText.toLowerCase();
        let department = row.cells[1].innerText.toLowerCase();
        let platform = row.cells[2].innerText.toLowerCase();

        if (name.includes(value) || department.includes(value) || platform.includes(value)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

});
</script>

@endif

    </div>

   <!-- 🔹 Activity Logs Section -->
<div class="activity-card">
    <div class="activity-header">
        <h3>📋 Recent Activity Logs</h3>
    </div>

    <div class="activity-table">

        <div class="activity-row activity-head">
            <div>User</div>
            <div>Action</div>
            <div>Module</div>
            <div>Time</div>
        </div>

        @if(isset($recentLogs) && $recentLogs->count() > 0)
            @foreach($recentLogs as $log)
            <div class="activity-row">
                <div class="user-name">
                    {{ $log->user_name ?? 'System' }}
                </div>

                <div>
                    <span class="status-badge
                        @if(str_contains(strtolower($log->action), 'create')) badge-success
                        @elseif(str_contains(strtolower($log->action), 'update')) badge-warning
                        @elseif(str_contains(strtolower($log->action), 'delete')) badge-danger
                        @else badge-info
                        @endif">
                        {{ $log->action }}
                    </span>
                </div>

                <div class="module-name">
                    {{ $log->module ?? 'General' }}
                </div>

                <div class="time-text">
                    {{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}
                </div>
            </div>
            @endforeach
        @else
            <div class="no-data">
                No recent activity logs found
            </div>
        @endif

    </div>
</div>
<style>
    /* Card */
.activity-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    overflow: hidden;
    margin-bottom: 25px;
    margin-top: 30px;
}

/* Header */
.activity-header {
    padding: 20px 25px;
    border-bottom: 1px solid #f1f1f1;
}

.activity-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

/* Table Wrapper */
.activity-table {
    width: 100%;
}

/* Row Layout */
.activity-row {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr 1fr;
    padding: 18px 25px;
    align-items: center;
    border-bottom: 1px solid #f3f3f3;
    transition: all 0.2s ease;
}

/* Header Row */
.activity-head {
    background: #f8fafc;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    color: #6c757d;
}

/* Hover */
.activity-row:hover {
    background: #f9fbff;
}

/* User */
.user-name {
    font-weight: 600;
    color: #2c3e50;
}

/* Module */
.module-name {
    color: #555;
}

/* Time */
.time-text {
    font-size: 13px;
    color: #888;
}

/* Badge */
.status-badge {
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 500;
}

/* Badge Colors */
.badge-success {
    background: #e6f9f0;
    color: #0f9d58;
}

.badge-warning {
    background: #fff4e5;
    color: #f57c00;
}

.badge-danger {
    background: #fdecea;
    color: #d93025;
}

.badge-info {
    background: #e8f0fe;
    color: #1a73e8;
}

/* No Data */
.no-data {
    padding: 30px;
    text-align: center;
    color: #999;
}
</style>
    <style>
        /* 🔹 Activity Log Card Styling */
.table-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

/* Header spacing */
.table-card .card-header {
    padding: 20px 24px !important;
    border-bottom: 1px solid #f1f1f1;
}

/* Table header */
.table-card .table thead th {
    padding: 18px 24px !important;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    background: #f8fafc;
    border-bottom: 1px solid #e9ecef;
}

/* Table body cells */
.table-card .table tbody td {
    padding: 18px 24px !important;
    border-bottom: 1px solid #f3f3f3;
}

/* Row hover effect */
.table-card .table tbody tr:hover {
    background-color: #f9fbfd !important;
}

/* Badge modern look */
.table-card .badge {
    padding: 6px 14px;
    border-radius: 50px;
    font-weight: 500;
    font-size: 12px;
}
    </style>
    <!-- 🔹 Pending Approvals Table -->
    @if(isset($pendingUsers) && $pendingUsers->count() > 0)
    <div class="card table-card mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Pending User Approvals</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingUsers as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department }}</td>
                            <td>
                                <span class="badge bg-secondary badge-role">
                                    {{ ucfirst($user->user_type) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.employees.details', $user->id) }}" class="btn btn-sm btn-info me-2">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                <form action="{{ route('admin.approve', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- 🔹 Welcome Card -->
    <!-- <div class="card welcome-card">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-1">Welcome, {{ Auth::user()->first_name }} 👋</h4>
                <p class="mb-0">You are logged in as <strong>Administrator</strong></p>
            </div>
            <a href="{{ route('admin.users') }}" class="btn btn-light mt-3 mt-md-0">
                <i class="bi bi-gear"></i> Manage Users
            </a>
        </div>
    </div> -->

</div>

<!-- Hiring Required Modal -->
<div class="modal fade" id="hiringModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                <h5 class="modal-title">
                    <i class="fa-solid fa-briefcase me-2"></i>Active Job Openings - Hiring Required
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(isset($activeJobOpenings) && $activeJobOpenings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Shift</th>
                                    <th>Salary</th>
                                    <th>Days Since Posted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeJobOpenings as $job)
                                <tr>
                                    <td><strong>{{ $job->job_title }}</strong></td>
                                    <td><span class="badge bg-info">{{ $job->shift }}</span></td>
                                    <td>₹{{ number_format($job->salary, 2) }}</td>
                                    <td>{{ $job->created_at->diffInDays() }} days</td>
                                    <td>
                                        <a href="{{ route('admin.job-openings.show', $job) }}" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                        <form action="{{ route('admin.job-openings.close', $job) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Mark this job as hired/closed?')">
                                                <i class="fa-solid fa-check"></i> Close
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.job-openings.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-cog"></i> Manage Job Openings
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Due Bills Modal -->
<div class="modal fade" id="dueBillsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #f39c12, #e74c3c); color: white;">
                <h5 class="modal-title">
                    <i class="fa-solid fa-file-invoice me-2"></i>Bills Due Today
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalBillsContent">
                    <!-- Bills will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.bills.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-cog"></i> Manage Bills
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showHiringModal() {
    const modal = new bootstrap.Modal(document.getElementById('hiringModal'));
    modal.show();
}

// Check for due bills on dashboard load
document.addEventListener('DOMContentLoaded', function() {
    checkDueBillsForDashboard();
    checkAutoGeneratedSalary();
});

function checkAutoGeneratedSalary() {
    fetch('/admin/salary/check-auto-generated')
        .then(response => response.json())
        .then(data => {
            if (data.hasAutoGenerated && data.salaryData) {
                showAutoSalaryAlert(data.salaryData);
            }
        })
        .catch(error => console.error('Error checking auto-generated salary:', error));
}

function showAutoSalaryAlert(salaryData) {
    const alert = document.getElementById('autoSalaryAlert');
    const salaryText = document.getElementById('autoSalaryText');
    
    salaryText.textContent = `Salary for ${salaryData.count} employees has been automatically generated for ${salaryData.month_name} ${salaryData.year}.`;
    alert.style.display = 'block';
    
    // Store salary data for navigation
    window.autoSalaryData = salaryData;
}

function viewSalaryRecords() {
    if (window.autoSalaryData) {
        window.location.href = `/admin/salary?month=${window.autoSalaryData.month}&year=${window.autoSalaryData.year}`;
    } else {
        window.location.href = '/admin/salary';
    }
}

function checkDueBillsForDashboard() {
    fetch('/admin/bills/due-today')
        .then(response => response.json())
        .then(data => {
            if (data.bills && data.bills.length > 0) {
                showDueBillsAlert(data.bills);
            }
        })
        .catch(error => console.error('Error:', error));
}

function showDueBillsAlert(bills) {
    const alert = document.getElementById('dueBillsAlert');
    const billsList = document.getElementById('dueBillsList');
    
    let billsText = '';
    bills.forEach((bill, index) => {
        billsText += `${bill.bill_type} (₹${parseFloat(bill.amount).toFixed(2)})`;
        if (index < bills.length - 1) billsText += ', ';
    });
    
    billsList.textContent = billsText;
    alert.style.display = 'block';
    
    // Store bills data for modal
    window.dueBillsData = bills;
}

function showBillsModal() {
    if (window.dueBillsData) {
        let content = '<div class="table-responsive">';
        content += '<table class="table table-hover">';
        content += '<thead><tr><th>Bill Type</th><th>Amount</th><th>Due Date</th><th>Action</th></tr></thead><tbody>';
        
        window.dueBillsData.forEach(function(bill) {
            content += `<tr>
                <td><strong>${bill.bill_type}</strong></td>
                <td>₹${parseFloat(bill.amount).toFixed(2)}</td>
                <td>${new Date(bill.due_date).toLocaleDateString('en-GB')}</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="markBillAsPaidFromDashboard(${bill.id})">
                        <i class="fa-solid fa-check"></i> Mark Paid
                    </button>
                </td>
            </tr>`;
        });
        
        content += '</tbody></table></div>';
        document.getElementById('modalBillsContent').innerHTML = content;
        
        const modal = new bootstrap.Modal(document.getElementById('dueBillsModal'));
        modal.show();
    }
}

function markBillAsPaidFromDashboard(billId) {
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
            // Remove bill from data
            window.dueBillsData = window.dueBillsData.filter(bill => bill.id !== billId);
            
            // If no bills left, hide alert
            if (window.dueBillsData.length === 0) {
                document.getElementById('dueBillsAlert').style.display = 'none';
                bootstrap.Modal.getInstance(document.getElementById('dueBillsModal')).hide();
            } else {
                // Update alert and modal
                showDueBillsAlert(window.dueBillsData);
                showBillsModal();
            }
        } else {
            alert('Error marking bill as paid');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking bill as paid');
    });
}
</script>

<!-- 🎉 Birthday Popup Modal -->
<div class="modal fade" id="birthdayPopupModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 20px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #ff6b6b, #ffa500); color: white; border: none;">
                <h5 class="modal-title">
                    <i class="fa-solid fa-birthday-cake me-2"></i>🎉 Today's Birthdays!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                @if(isset($todayBirthdays) && $todayBirthdays->count() > 0)
                    @foreach($todayBirthdays as $employee)
                    <div class="birthday-employee-card" style="background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 40px;">🎂</div>
                        <div>
                            <h6 style="margin: 0; font-weight: 600; color: #333;">{{ $employee->full_name }}</h6>
                            <p style="margin: 0; color: #666; font-size: 14px;">{{ $employee->department }}</p>
                            @if($employee->phone)
                            <a href="tel:{{ $employee->phone }}" class="btn btn-sm btn-success mt-2">
                                <i class="fa-solid fa-phone"></i> Call & Wish
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 60px; margin-bottom: 15px;">🎂</div>
                        <h6>No birthdays today</h6>
                        <p style="color: #666;">Check back tomorrow!</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer" style="border: none; background: #f8f9fa;">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-check"></i> Got it!
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($todayBirthdays) && $todayBirthdays->count() > 0)
    // Check if popup is enabled
    fetch('/admin/birthdays/popup-status')
        .then(response => response.json())
        .then(data => {
            if (data.enabled) {
                const modalElement = document.getElementById('birthdayPopupModal');
                if (modalElement) {
                    setTimeout(function() {
                        const birthdayModal = new bootstrap.Modal(modalElement);
                        birthdayModal.show();
                    }, 1000);
                }
            }
        })
        .catch(error => console.error('Error:', error));
    @endif
});
</script>

<script>
function filterData() {
    const dateFilter = document.getElementById('dateFilter').value;
    const searchTerm = document.getElementById('searchBar').value.toLowerCase();
    
    const url = new URL(window.location.href);
    if (dateFilter) url.searchParams.set('date_filter', dateFilter);
    else url.searchParams.delete('date_filter');
    
    if (searchTerm) url.searchParams.set('search', searchTerm);
    else url.searchParams.delete('search');
    
    window.location.href = url.toString();
}
</script>

@endsection
