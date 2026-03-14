<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
.sidebar .submenu {
    display: none !important;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.sidebar .has-sub.open > .submenu {
    display: block !important;
    max-height: 1000px;
}

.sidebar .submenu-toggle {
    display: flex;
    align-items: center;
    gap: 11px;
}

.sidebar .arrow {
    font-size: 12px;
    transition: transform 0.3s ease;
}

.sidebar .has-sub.open .arrow {
    transform: rotate(180deg);
}

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 260px;
    background: white;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
    transition: transform 0.35s ease-in-out;
}

@media (max-width: 768px) {
    .sidebar {
        top: 60px;
        height: calc(100vh - 60px);
        transform: translateX(-100%);
        z-index: 9998;
        width: 100%;
        max-width: 280px;
    }
    
    .sidebar.sidebar-mobile-open {
        transform: translateX(0) !important;
    }
}

/* smooth scrollbar (Chrome / Edge) */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #2eacb3;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

/* Company Info Styles */
.sidebar-header {
    padding: 1.4px 20px;
    border-bottom: 1px solid rgb(79, 124, 212);
}

@media (max-width: 768px) {
    .sidebar-header {
        padding: 10px 15px;
        display: none;
    }
    
    .company-logo1 img {
        width: 150px !important;
    }
}

.company-info {
    display: flex;
    align-items: center;
}

.company-logo {
    width: 40px;
    height: 40px;
    background: #2eacb3;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.company-logo i {
    color: white;
    font-size: 20px;
}

.company-name {
    color: #2eacb3;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* sidebar hide state */
.sidebar.sidebar-hidden {
    transform: translateX(-230px);
}

/* invisible hover strip (trigger area) */
.sidebar-hover-zone {
    position: fixed;
    top: 0;
    left: 0;
    width: 30px;
    height: 100vh;
    z-index: 999;
    background: transparent;
}
</style>
<style>
/* Sidebar Menu Styles */
.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

@media (max-width: 768px) {
    .sidebar-menu {
        padding: 10px 0;
    }
}

.sidebar-menu li {
    margin: 0;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #2eacb3;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    word-break: break-word;
}

@media (max-width: 768px) {
    .sidebar-menu a {
        padding: 10px 15px;
        font-size: 13px;
    }
    
    .sidebar-menu i {
        margin-right: 8px !important;
        width: 18px;
    }
    
    .sidebar .submenu a {
        padding-left: 40px;
        font-size: 12px;
    }
}

.sidebar-menu a > span {
    display: inline;
}

.sidebar-menu a:hover,
.sidebar-menu a:focus,
.sidebar-menu a.active {
    background: #2eacb3;
    color: white;
}

.sidebar-menu a:hover i,
.sidebar-menu a:focus i,
.sidebar-menu a.active i {
    color: white !important;
}

.sidebar-menu i {
    color: #2eacb3 !important;
    margin-right: 10px;
    width: 20px;
}

.sidebar .submenu {
    background:rgb(242, 244, 245);
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

@media (max-width: 768px) {
    .sidebar .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .sidebar .has-sub.open > .submenu {
        max-height: 500px;
        overflow: visible;
    }
}

.sidebar .submenu li {
    margin: 0 !important;
    padding: 0 !important;
    list-style: none !important;
}

.sidebar .submenu a {
    padding-left: 50px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .sidebar .submenu a {
        padding-left: 40px;
        font-size: 12px;
    }
    
    .sidebar .submenu {
        max-height: 0 !important;
        overflow: hidden !important;
        display: none !important;
    }
    
    .sidebar .has-sub.open > .submenu {
        max-height: 500px !important;
        display: block !important;
    }
}

.sidebar .submenu a span {
    display: inline;
}

.sidebar .submenu a:hover,
.sidebar .submenu a:focus,
.sidebar .submenu a.active {
    background: #2eacb3;
    color: white;
}

.sidebar .submenu a:hover i,
.sidebar .submenu a:focus i,
.sidebar .submenu a.active i {
    color: white !important;
}

.sidebar .submenu i {
    color: #2eacb3 !important;
    margin-right: 10px;
}

.company-logo1 img {
    width: 200px;
    object-fit: contain;
    align-items: center
}
</style>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
    <div class="company-info">
    <div class="company-logo1">
    <a href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('Kwikster.jpeg') }}" alt="Kwikster Logo">
    </a>
</div>
    
</div>


        <!-- <div class="user-info">
            <i class="fa-solid fa-user-circle"></i>
            <span class="user-name">HR Admin</span>
        </div> -->
    </div>


    <ul class="sidebar-menu">
    <li>
    <a href="{{ route('admin.dashboard') }}">
        <i class="fa-solid fa-gauge-high"></i>
        <span>Dashboard</span>
    </a>
</li>
<li class="has-sub">
<a href="javascript:void(0)" class="submenu-toggle">
    <i class="fa-solid fa-folder-open"></i>
    <span>Applicant Database</span>
</a>

    <ul class="submenu">
        <li>
            <a href="{{ route('admin.leads.index') }}">
                <i class="fa-solid fa-address-book"></i>
                All Leads
            </a>
        </li>

        <li>
            <a href="{{ route('admin.callbacks.index') }}">
                <i class="fa-solid fa-phone-volume"></i>
                
                Callback
                <span class="callback-badge" id="callbackCount"
                    style="background:#ff6b6b;color:#fff;border-radius:12px;
                    padding:3px 8px;font-size:10px;font-weight:bold;
                    margin-left:8px;display:none;">
                    0
                </span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.interns.index') }}">
                <i class="bi bi-people-fill"></i> Intern Leads
            </a>
        </li>

        <li>
            <a href="{{ route('admin.interns.callbacks') }}">
                <i class="bi bi-telephone-fill"></i> Intern Callbacks
            </a>
        </li>
    </ul>
</li>

<li class="has-sub">
        <a href="javascript:void(0)" class="submenu-toggle">
    <i class="fa-solid fa-clipboard-list"></i>
    <span>Screening Stage</span>
</a>


    <ul class="submenu">
        <li>
            <a href="{{ route('admin.leads.interested') }}">
                <i class="fa-solid fa-star"></i>
                ShortListed
            </a>
        </li>

        <li>
            <a href="{{ route('admin.interviews.index') }}">
                <i class="fa-solid fa-calendar-check"></i>
                Interview In Process
            </a>
        </li>

        <li>
            <a href="{{ route('admin.interviews.selected') }}">
                <i class="fa-solid fa-user-check"></i>
                Offer Released
            </a>
        </li>

        <li>
            <a href="{{ route('admin.employees.documents.index') }}">
                <i class="fa-solid fa-file-lines"></i>
                Document Check
            </a>
        </li>

        <li>
            <a href="{{ route('admin.employees.hired.index') }}">
                <i class="fa-solid fa-user-check"></i>
                Certification Period
            </a>
        </li>
    </ul>
</li>

<li class="has-sub">
<a href="javascript:void(0)" class="submenu-toggle">
        <i class="fa-solid fa-bars-progress"></i>
        <span>Applicant Status</span>
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('admin.leads.rejected') }}">
                <i class="fa-solid fa-xmark"></i>
                <span>Rejected</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.leads.not-interested') }}">
                <i class="fa-solid fa-thumbs-down"></i>
                <span>Not Interested</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.leads.wrong-number') }}">
                <i class="fa-solid fa-phone-slash"></i>
                <span>Wrong Number</span>
            </a>
        </li>

        <li>
            <a href="/admin/employees/not-selected">
                <i class="fa-solid fa-user-xmark"></i>
                <span>Not Selected Employee</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.interviews.rejected') }}">
                <i class="fa-solid fa-ban"></i>
                <span>Rejected Interview</span>
            </a>
        </li>
    </ul>
</li>


        
        
       
        <!-- Employee -->
        <li class="has-sub">
        <a href="javascript:void(0)" class="submenu-toggle">
            <i class="fa-solid fa-users"></i> <span>Employee</span>
        </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('admin.employees.index') }}">
                        <i class="fa-solid fa-user-check"></i>
                        Active Employees
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.employee.credentials') }}">
                        <i class="fa-solid fa-key"></i>
                        Employee Login
                    </a>
                </li>
            </ul>
        </li>

        <!-- Interns Management -->
        <li class="has-sub">
        <a href="javascript:void(0)" class="submenu-toggle">
            <i class="fa-solid fa-graduation-cap"></i> <span>Interns</span>
            
        </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('admin.interns.interested') }}">
                        <i class="fa-solid fa-star"></i>
                        Interested Interns
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.interns.ongoing-list') }}">
                        <i class="fa-solid fa-spinner"></i>
                        Ongoing Interns
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.interns.rejected') }}">
                        <i class="fa-solid fa-xmark"></i>
                        Rejected Interns
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.interns.not-interested') }}">
                        <i class="fa-solid fa-thumbs-down"></i>
                        Not Interested
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.interns.wrong-number') }}">
                        <i class="fa-solid fa-phone-slash"></i>
                        Wrong Number
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.interns.profiles') }}">
                        <i class="fa-solid fa-id-card"></i>
                        Intern Profiles
                    </a>
                </li>
            </ul>
        </li>

      
        <li class="has-sub">
        <a href="javascript:void(0)" class="submenu-toggle">
            <i class="fa-solid fa-file-invoice-dollar"></i> <span>Payroll</span>
           
        </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('admin.salary.index') }}">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        Salary Management
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.attendance.index') }}">
                        <i class="fa-solid fa-calendar-check"></i>
                        Attendance
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.face-attendance.index') }}">
                        <i class="fa-solid fa-face-smile"></i>
                        Face Attendance
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.face-attendance.register') }}">
                        <i class="fa-solid fa-user-plus"></i>
                        Register Face
                    </a>
                </li>
            </ul>
        </li>


       

        <!-- Documents -->
        <!-- <li class="has-sub">
            <a href="#">Documents</a>
            <ul class="submenu">
                <li><a href="#">Employee Documents</a></li>
                <li><a href="#">Company Documents</a></li>
                <li><a href="#">E-Signatures</a></li>
            </ul>
        </li> -->

        <!-- Authentication -->
        <!-- <li class="has-sub">
            <a href="#">Authentication</a>
            <ul class="submenu">
                <li><a href="#">Sign Up</a></li>
                <li><a href="#">Sign In</a></li>
                <li><a href="#">Forget Password</a></li>
            </ul>
        </li> -->

        <!-- <li><a href="#">Maps</a></li> -->
        <li><a href="{{ route('admin.hr-notes.index') }}">
        <i class="fa-solid fa-clipboard"></i> <span>HR Notes</span></a></li>
        <li><a href="{{ route('admin.leaves.index') }}">
        <i class="fa-solid fa-calendar-days"></i> <span>Leave Management</span></a></li>
        <li><a href="{{ route('admin.candidate-journey.index') }}">
        <i class="fa-solid fa-diagram-project"></i> <span>Candidate Journey</span></a></li>
        <li class="has-sub">
        <a href="javascript:void(0)" class="submenu-toggle">
        <i class="fa-solid fa-users"></i> <span>HR Management</span>
        
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('admin.job-openings.index') }}">
                <i class="fa-solid fa-briefcase"></i>
                Job Opening Management
            </a>
        </li>
        <li>
            <a href="{{ route('admin.birthdays.index') }}">
                <i class="fa-solid fa-cake-candles"></i>
                Birthday
            </a>
        </li>
        <li>
            <a href="{{ route('admin.employees.all') }}">
                <i class="fa-solid fa-envelope"></i>
                All Emails
            </a>
        </li>
        <li>
            <a href="{{ route('admin.letters.index') }}">
                <i class="fa-solid fa-file-contract"></i>
                Employee Letters
            </a>
        </li>
        <li>
            <a href="{{ route('admin.bills.index') }}">
                <i class="fa-solid fa-file-invoice"></i>
                Bill Management
            </a>
        </li>
    </ul>
</li>
        <li><a href="{{ route('admin.expenses.index') }}"><i class="fa-solid fa-money-bill-wave"></i> <span>Expenses</span></a></li>
        <li><a href="{{ route('admin.tickets.index') }}"><i class="fa-solid fa-ticket-alt"></i> <span>Employee Tickets</span></a></li>
        <li><a href="{{ route('admin.employee-expenses.index') }}"><i class="fa-solid fa-receipt"></i> <span>Reimbursement</span></a></li>
        <!-- <li class="logout"><a href="#">Logout</a></li> -->

         <!-- Mobile Header Items -->
    <div class="mobile-header-items" style="display: none;">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li>
                <a href="{{ route('admin.employee.create') }}">
                    <i class="fa-solid fa-user-plus"></i>
                    Add Employee
                </a>
            </li>
            <li>
                <a href="{{ route('admin.salary.calculator') }}">
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
            <li>
                <a href="#" onclick="showNotifications()">
                    <i class="fa-regular fa-bell"></i>
                    Notifications
                </a>
            </li>
            <li>
                <a href="{{ route('admin.profile') }}">
                    <i class="fa-solid fa-user"></i>
                    Profile
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings') }}">
                    <i class="fa-solid fa-cog"></i>
                    Settings
                </a>
            </li>
            <li>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #f9fafb; width: 100%; text-align: left; padding: 10px 15px; display: flex; align-items: center; gap: 10px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#374151'" onmouseout="this.style.background='none'">
                        <i class="fa-solid fa-sign-out-alt"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
    </ul>
    
   
</div>

<script>
// Update callback count in sidebar
window.updateCallbackCount = function() {
    fetch('/admin/callbacks/count', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.getElementById('callbackCount');
        if (badge) {
            badge.textContent = data.count;
            badge.style.display = data.count > 0 ? 'inline' : 'none';
        }
    })
    .catch(error => console.log('Error fetching callback count:', error));
};

// Update count on page load
document.addEventListener('DOMContentLoaded', updateCallbackCount);

// Update count every 30 seconds
setInterval(updateCallbackCount, 30000);

// Show employee list for details
function showEmployeeList() {
    fetch('/admin/employees', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(html => {
        // Create modal to show employee list
        const modal = document.createElement('div');
        modal.innerHTML = `
            <div class="modal fade" id="employeeListModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select Employee for Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employeeTableBody">
                                        <!-- Will be populated by AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Load employees via AJAX
        loadEmployeeList();
        
        // Show modal
        const bootstrapModal = new bootstrap.Modal(document.getElementById('employeeListModal'));
        bootstrapModal.show();
        
        // Remove modal when closed
        document.getElementById('employeeListModal').addEventListener('hidden.bs.modal', function() {
            modal.remove();
        });
    })
    .catch(error => console.log('Error:', error));
}

function loadEmployeeList() {
    fetch('/admin/employees/data', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('employeeTableBody');
        tbody.innerHTML = '';
        
        data.employees.forEach(employee => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${employee.first_name} ${employee.last_name}</td>
                <td>${employee.email}</td>
                <td>${employee.department || 'N/A'}</td>
                <td>
                    <a href="/admin/employees/${employee.id}/details" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-eye"></i> View Details
                    </a>
                </td>
            `;
            tbody.appendChild(row);
        });
    })
    .catch(error => console.log('Error loading employees:', error));
}
</script>
<script>
(function() {
    'use strict';
    
    console.log('Sidebar script loading...');
    
    function initSubmenuToggle() {
        const toggles = document.querySelectorAll('.submenu-toggle');
        console.log('Found toggles:', toggles.length);
        
        toggles.forEach(function(toggle, index) {
            // Remove any existing listeners
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                console.log('Toggle clicked!', index);
                
                const parent = this.closest('.has-sub');
                
                if (!parent) {
                    console.log('No parent found');
                    return false;
                }
                
                const isOpen = parent.classList.contains('open');
                console.log('Was open:', isOpen);
                
                // Close all
                document.querySelectorAll('.has-sub').forEach(function(item){
                    item.classList.remove('open');
                });
                
                // Open current if it was closed
                if (!isOpen) {
                    parent.classList.add('open');
                    console.log('Added open class');
                }
                
                setTimeout(function() {
                    console.log('After timeout - Parent has open class:', parent.classList.contains('open'));
                }, 100);
                
                return false;
            }, true);
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSubmenuToggle);
    } else {
        initSubmenuToggle();
    }
})();
</script>
<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');

   
    const hoverZone = document.createElement('div');
    hoverZone.className = 'sidebar-hover-zone';
    document.body.appendChild(hoverZone);

    let hideTimer;

    
    sidebar.addEventListener('mouseleave', () => {
        hideTimer = setTimeout(() => {
            sidebar.classList.add('sidebar-hidden');
        }, 300);
    });

    
    sidebar.addEventListener('mouseenter', () => {
        clearTimeout(hideTimer);
        sidebar.classList.remove('sidebar-hidden');
    });

    
    hoverZone.addEventListener('mouseenter', () => {
        sidebar.classList.remove('sidebar-hidden');
    });
});
</script> -->


