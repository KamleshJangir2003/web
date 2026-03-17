<?php
// Load environment variables from Laravel .env
$envPath = __DIR__ . '/cms/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Strip surrounding quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
        }
    }
}

// Database Configuration from .env (fallbacks preserved)
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_DATABASE'] ?? 'attendance_db';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_POST) {
    $date = $_POST['date'];
    
    foreach ($_POST['employees'] as $employee_id => $data) {
        $shift_status = $data['shift_status'];
        $entry_time = !empty($data['entry_time']) ? $data['entry_time'] : null;
        $exit_time = !empty($data['exit_time']) ? $data['exit_time'] : null;
        $reason = $data['reason'];
        
        $sql = "INSERT INTO attendance_logs 
                (employee_id, date, shift_shift_status, entry_time, exit_time, reason) 
                VALUES (?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                shift_shift_status = VALUES(shift_shift_status),
                entry_time = VALUES(entry_time),
                exit_time = VALUES(exit_time),
                reason = VALUES(reason)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employee_id, $date, $shift_status, $entry_time, $exit_time, $reason]);
    }
    
    $success_message = "Attendance saved successfully!";
}

// Get filter values
$selected_date = $_GET['date'] ?? date('Y-m-d');
$department_filter = $_GET['department'] ?? '';
$search_employee = $_GET['search'] ?? '';

// Get departments for filter
$dept_sql = "SELECT DISTINCT department FROM employees WHERE shift_status = 'active' ORDER BY department";
$departments = $pdo->query($dept_sql)->fetchAll(PDO::FETCH_COLUMN);

// Build employee query with filters
$employee_sql = "SELECT * FROM employees WHERE shift_status = 'active'";
$params = [];

if ($department_filter) {
    $employee_sql .= " AND department = ?";
    $params[] = $department_filter;
}

if ($search_employee) {
    $employee_sql .= " AND (name LIKE ? OR employee_code LIKE ?)";
    $params[] = "%$search_employee%";
    $params[] = "%$search_employee%";
}

$employee_sql .= " ORDER BY employee_code";
$stmt = $pdo->prepare($employee_sql);
$stmt->execute($params);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get existing attendance for selected date
$attendance_data = [];
$employee_lookup = [];
if ($employees) {
    foreach ($employees as $emp) {
        $employee_lookup[$emp['id']] = $emp;
    }
    
    $employee_ids = array_column($employees, 'id');
    $placeholders = str_repeat('?,', count($employee_ids) - 1) . '?';
    
    $att_sql = "SELECT * FROM attendance_logs 
            WHERE employee_id IN ($placeholders) 
            AND date = ?";
    $stmt = $pdo->prepare($att_sql);
    $stmt->execute(array_merge($employee_ids, [$selected_date]));
    
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $att) {
        $attendance_data[$att['employee_id']] = $att;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .table th { background-color: #f8f9fa; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .form-control:focus, .form-select:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance Management System</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Filters -->
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" value="<?= $selected_date ?>" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Department</label>
                                <select name="department" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept ?>" <?= $department_filter === $dept ? 'selected' : '' ?>><?= $dept ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search Employee</label>
                                <input type="text" name="search" class="form-control" placeholder="Name or Code" value="<?= $search_employee ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100"><i class="fas fa-search me-1"></i>Search</button>
                            </div>
                        </form>

                        <!-- Attendance Form -->
                        <form method="POST">
                            <input type="hidden" name="date" value="<?= $selected_date ?>">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Employee Code</th>
                                            <th>Employee Name</th>
                                            <th>Department</th>
                                            <th>Job Title</th>
                                            <th>shift_status</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($employees)): ?>
                                            <tr><td colspan="8" class="text-center text-muted">No employees found</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($employees as $emp): ?>
                                                <?php $att = $attendance_data[$emp['id']] ?? null; ?>
                                                <tr>
                                                    <td><?= $emp['employee_code'] ?></td>
                                                    <td><?= $emp['name'] ?></td>
                                                    <td><?= $emp['department'] ?></td>
                                                    <td><?= $emp['job_title'] ?></td>
                                                    <td>
                                                        <select name="employees[<?= $emp['id'] ?>][shift_status]" class="form-select form-select-sm">
                                                            <option value="Present" <?= ($att['shift_shift_status'] ?? 'Present') === 'Present' ? 'selected' : '' ?>>Present</option>
                                                            <option value="Absent" <?= ($att['shift_shift_status'] ?? '') === 'Absent' ? 'selected' : '' ?>>Absent</option>
                                                            <option value="Leave" <?= ($att['shift_shift_status'] ?? '') === 'Leave' ? 'selected' : '' ?>>Leave</option>
                                                            <option value="Half Day" <?= ($att['shift_shift_status'] ?? '') === 'Half Day' ? 'selected' : '' ?>>Half Day</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="time" name="employees[<?= $emp['id'] ?>][entry_time]" class="form-control form-control-sm" value="<?= $att['entry_time'] ?? '' ?>">
                                                    </td>
                                                    <td>
                                                        <input type="time" name="employees[<?= $emp['id'] ?>][exit_time]" class="form-control form-control-sm" value="<?= $att['exit_time'] ?? '' ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="employees[<?= $emp['id'] ?>][reason]" class="form-control form-control-sm" placeholder="Reason" value="<?= $att['reason'] ?? '' ?>">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (!empty($employees)): ?>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save Attendance
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>

                        <!-- Saved Attendance Display -->
                        <?php if (!empty($attendance_data)): ?>
                            <hr class="my-4">
                            <h5 class="mb-3"><i class="fas fa-list me-2"></i>Saved Attendance for <?= date('d M Y', strtotime($selected_date)) ?></h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee Code</th>
                                            <th>Employee Name</th>
                                            <th>Department</th>
                                            <th>shift_status</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attendance_data as $att): ?>
                                            <?php $emp = $employee_lookup[$att['employee_id']] ?? null; ?>
                                            <?php if ($emp): ?>
                                                <tr>
                                                    <td><?= $emp['employee_code'] ?></td>
                                                    <td><?= $emp['name'] ?></td>
                                                    <td><?= $emp['department'] ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $att['shift_status'] === 'Present' ? 'success' : ($att['shift_status'] === 'Absent' ? 'danger' : 'warning') ?>">
                                                            <?= $att['shift_status'] ?>
                                                        </span>
                                                    </td>
                                                    <td><?= $att['entry_time'] ? date('h:i A', strtotime($att['entry_time'])) : '-' ?></td>
                                                    <td><?= $att['exit_time'] ? date('h:i A', strtotime($att['exit_time'])) : '-' ?></td>
                                                    <td><?= $att['reason'] ?: '-' ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>