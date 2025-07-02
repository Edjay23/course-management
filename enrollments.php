<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_enrollment'])) {
        try {
            $stmt = $db->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
            $stmt->execute([$_POST['student_id'], $_POST['course_id']]);
            header("Location: enrollments.php");
            exit();
        } catch(PDOException $e) {
            $error = "Error: Student is already enrolled in this course or enrollment failed.";
        }
    }
    
    if (isset($_POST['edit_enrollment'])) {
        $stmt = $db->prepare("UPDATE enrollments SET status = ? WHERE enrollment_id = ?");
        $stmt->execute([$_POST['status'], $_POST['enrollment_id']]);
        header("Location: enrollments.php");
        exit();
    }
    
    if (isset($_POST['delete_enrollment'])) {
        $stmt = $db->prepare("DELETE FROM enrollments WHERE enrollment_id = ?");
        $stmt->execute([$_POST['enrollment_id']]);
        header("Location: enrollments.php");
        exit();
    }
}

// Get all enrollments with student and course details
$stmt = $db->query("
    SELECT 
        e.*,
        CONCAT(s.first_name, ' ', s.last_name) as student_name,
        s.email as student_email,
        c.course_code,
        c.course_name
    FROM enrollments e
    JOIN students s ON e.student_id = s.student_id
    JOIN courses c ON e.course_id = c.course_id
    ORDER BY e.enrollment_date DESC
");
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all students for dropdown
$stmt = $db->query("SELECT student_id, CONCAT(first_name, ' ', last_name) as student_name FROM students WHERE status = 'active' ORDER BY last_name, first_name");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all courses for dropdown
$stmt = $db->query("SELECT course_id, CONCAT(course_code, ' - ', course_name) as course_info FROM courses WHERE status = 'active' ORDER BY course_code");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments - Course Management System</title>
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
                            <a class="nav-link active" href="enrollments.php">
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
                    <h1 class="h2">Enrollments</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
                        <i class="fas fa-plus me-2"></i>Add Enrollment
                    </button>
                </div>

                <?php if(isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Enrollments Table -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Course</th>
                                        <th>Course Name</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?php echo $enrollment['enrollment_id']; ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['student_email']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['course_code']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['enrollment_date']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $enrollment['status'] == 'enrolled' ? 'success' : 
                                                    ($enrollment['status'] == 'completed' ? 'primary' : 'warning'); 
                                            ?>">
                                                <?php echo ucfirst($enrollment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning me-1" 
                                                    data-bs-toggle="modal" data-bs-target="#editEnrollmentModal"
                                                    onclick="editEnrollment(<?php echo htmlspecialchars(json_encode($enrollment)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                                <button type="submit" name="delete_enrollment" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this enrollment?')">
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

    <!-- Add Enrollment Modal -->
    <div class="modal fade" id="addEnrollmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Enrollment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-control" id="student_id" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php foreach($students as $student): ?>
                                <option value="<?php echo $student['student_id']; ?>">
                                    <?php echo htmlspecialchars($student['student_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-control" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php foreach($courses as $course): ?>
                                <option value="<?php echo $course['course_id']; ?>">
                                    <?php echo htmlspecialchars($course['course_info']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_enrollment" class="btn btn-primary">Add Enrollment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Enrollment Modal -->
    <div class="modal fade" id="editEnrollmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Enrollment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" id="edit_enrollment_id" name="enrollment_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <input type="text" class="form-control" id="edit_student_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" id="edit_course_info" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="enrolled">Enrolled</option>
                                <option value="dropped">Dropped</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_enrollment" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editEnrollment(enrollment) {
            document.getElementById('edit_enrollment_id').value = enrollment.enrollment_id;
            document.getElementById('edit_student_name').value = enrollment.student_name;
            document.getElementById('edit_course_info').value = enrollment.course_code + ' - ' + enrollment.course_name;
            document.getElementById('edit_status').value = enrollment.status;
        }
    </script>
</body>
</html>
