<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_instructor'])) {
        $stmt = $db->prepare("INSERT INTO instructors (first_name, last_name, email, phone, department, hire_date, salary) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['department'], $_POST['hire_date'], $_POST['salary']]);
        header("Location: instructors.php");
        exit();
    }
    
    if (isset($_POST['edit_instructor'])) {
        $stmt = $db->prepare("UPDATE instructors SET first_name = ?, last_name = ?, email = ?, phone = ?, department = ?, hire_date = ?, salary = ?, status = ? WHERE instructor_id = ?");
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['department'], $_POST['hire_date'], $_POST['salary'], $_POST['status'], $_POST['instructor_id']]);
        header("Location: instructors.php");
        exit();
    }
    
    if (isset($_POST['delete_instructor'])) {
        $stmt = $db->prepare("DELETE FROM instructors WHERE instructor_id = ?");
        $stmt->execute([$_POST['instructor_id']]);
        header("Location: instructors.php");
        exit();
    }
}

// Get all instructors
$stmt = $db->query("SELECT * FROM instructors ORDER BY last_name, first_name");
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructors - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .nav-link {
            color: white !important;
            border-radius: 10px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>Course Management System
            </a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="students.php">
                                <i class="fas fa-user-graduate me-2"></i>Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="instructors.php">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Instructors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="courses.php">
                                <i class="fas fa-book me-2"></i>Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="enrollments.php">
                                <i class="fas fa-clipboard-list me-2"></i>Enrollments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="grades.php">
                                <i class="fas fa-chart-line me-2"></i>Grades
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Instructors</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                        <i class="fas fa-plus me-2"></i>Add Instructor
                    </button>
                </div>

                <!-- Instructors Table -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Department</th>
                                        <th>Hire Date</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($instructors as $instructor): ?>
                                    <tr>
                                        <td><?php echo $instructor['instructor_id']; ?></td>
                                        <td><?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                                        <td><?php echo htmlspecialchars($instructor['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($instructor['department']); ?></td>
                                        <td><?php echo htmlspecialchars($instructor['hire_date']); ?></td>
                                        <td>$<?php echo number_format($instructor['salary'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $instructor['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($instructor['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning me-1" 
                                                    data-bs-toggle="modal" data-bs-target="#editInstructorModal"
                                                    onclick="editInstructor(<?php echo htmlspecialchars(json_encode($instructor)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="instructor_id" value="<?php echo $instructor['instructor_id']; ?>">
                                                <button type="submit" name="delete_instructor" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this instructor?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Instructor Modal -->
    <div class="modal fade" id="addInstructorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="department" name="department">
                        </div>
                        <div class="mb-3">
                            <label for="hire_date" class="form-label">Hire Date</label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date">
                        </div>
                        <div class="mb-3">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" class="form-control" id="salary" name="salary">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_instructor" class="btn btn-primary">Add Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Instructor Modal -->
    <div class="modal fade" id="editInstructorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" id="edit_instructor_id" name="instructor_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="edit_department" name="department">
                        </div>
                        <div class="mb-3">
                            <label for="edit_hire_date" class="form-label">Hire Date</label>
                            <input type="date" class="form-control" id="edit_hire_date" name="hire_date">
                        </div>
                        <div class="mb-3">
                            <label for="edit_salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" class="form-control" id="edit_salary" name="salary">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_instructor" class="btn btn-primary">Update Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editInstructor(instructor) {
            document.getElementById('edit_instructor_id').value = instructor.instructor_id;
            document.getElementById('edit_first_name').value = instructor.first_name;
            document.getElementById('edit_last_name').value = instructor.last_name;
            document.getElementById('edit_email').value = instructor.email;
            document.getElementById('edit_phone').value = instructor.phone || '';
            document.getElementById('edit_department').value = instructor.department || '';
            document.getElementById('edit_hire_date').value = instructor.hire_date || '';
            document.getElementById('edit_salary').value = instructor.salary || '';
            document.getElementById('edit_status').value = instructor.status;
        }
    </script>
</body>
</html>
