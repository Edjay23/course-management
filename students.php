<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_student'])) {
        $stmt = $db->prepare("INSERT INTO students (first_name, last_name, email, phone, date_of_birth) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['date_of_birth']]);
        header("Location: students.php");
        exit();
    }
    
    if (isset($_POST['edit_student'])) {
        $stmt = $db->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, phone = ?, date_of_birth = ?, status = ? WHERE student_id = ?");
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['date_of_birth'], $_POST['status'], $_POST['student_id']]);
        header("Location: students.php");
        exit();
    }
    
    if (isset($_POST['delete_student'])) {
        $stmt = $db->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->execute([$_POST['student_id']]);
        header("Location: students.php");
        exit();
    }
}

// Get all students
$stmt = $db->query("SELECT * FROM students ORDER BY last_name, first_name");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Course Management System</title>
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
                            <a class="nav-link active" href="students.php">
                                <i class="fas fa-user-graduate me-2"></i>Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="instructors.php">
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
                    <h1 class="h2">Students</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-plus me-2"></i>Add Student
                    </button>
                </div>

                <!-- Students Table -->
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
                                        <th>Date of Birth</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($students as $student): ?>
                                    <tr>
                                        <td><?php echo $student['student_id']; ?></td>
                                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($student['date_of_birth']); ?></td>
                                        <td><?php echo htmlspecialchars($student['enrollment_date']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $student['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($student['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning me-1" 
                                                    data-bs-toggle="modal" data-bs-target="#editStudentModal"
                                                    onclick="editStudent(<?php echo htmlspecialchars(json_encode($student)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                                <button type="submit" name="delete_student" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this student?')">
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

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
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
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" id="edit_student_id" name="student_id">
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
                            <label for="edit_date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="edit_date_of_birth" name="date_of_birth">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="graduated">Graduated</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_student" class="btn btn-primary">Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editStudent(student) {
            document.getElementById('edit_student_id').value = student.student_id;
            document.getElementById('edit_first_name').value = student.first_name;
            document.getElementById('edit_last_name').value = student.last_name;
            document.getElementById('edit_email').value = student.email;
            document.getElementById('edit_phone').value = student.phone || '';
            document.getElementById('edit_date_of_birth').value = student.date_of_birth || '';
            document.getElementById('edit_status').value = student.status;
        }
    </script>
</body>
</html>
