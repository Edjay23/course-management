<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_course'])) {
        $stmt = $db->prepare("INSERT INTO courses (course_code, course_name, description, credits, instructor_id, max_enrollment, semester, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['course_code'], $_POST['course_name'], $_POST['description'], $_POST['credits'], $_POST['instructor_id'], $_POST['max_enrollment'], $_POST['semester'], $_POST['year']]);
        header("Location: courses.php");
        exit();
    }
    
    if (isset($_POST['edit_course'])) {
        $stmt = $db->prepare("UPDATE courses SET course_code = ?, course_name = ?, description = ?, credits = ?, instructor_id = ?, max_enrollment = ?, semester = ?, year = ?, status = ? WHERE course_id = ?");
        $stmt->execute([$_POST['course_code'], $_POST['course_name'], $_POST['description'], $_POST['credits'], $_POST['instructor_id'], $_POST['max_enrollment'], $_POST['semester'], $_POST['year'], $_POST['status'], $_POST['course_id']]);
        header("Location: courses.php");
        exit();
    }
    
    if (isset($_POST['delete_course'])) {
        $stmt = $db->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->execute([$_POST['course_id']]);
        header("Location: courses.php");
        exit();
    }
}

// Get all courses with instructor names
$stmt = $db->query("
    SELECT c.*, CONCAT(i.first_name, ' ', i.last_name) as instructor_name
    FROM courses c
    LEFT JOIN instructors i ON c.instructor_id = i.instructor_id
    ORDER BY c.course_code
");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all instructors for dropdown
$stmt = $db->query("SELECT instructor_id, CONCAT(first_name, ' ', last_name) as instructor_name FROM instructors WHERE status = 'active' ORDER BY last_name, first_name");
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - Course Management System</title>
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
                            <a class="nav-link active" href="courses.php">
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
                    <h1 class="h2">Courses</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="fas fa-plus me-2"></i>Add Course
                    </button>
                </div>

                <!-- Courses Table -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Credits</th>
                                        <th>Instructor</th>
                                        <th>Max Enrollment</th>
                                        <th>Semester</th>
                                        <th>Year</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($courses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                        <td><?php echo $course['credits']; ?></td>
                                        <td><?php echo htmlspecialchars($course['instructor_name'] ?? 'Not Assigned'); ?></td>
                                        <td><?php echo $course['max_enrollment']; ?></td>
                                        <td><?php echo htmlspecialchars($course['semester']); ?></td>
                                        <td><?php echo $course['year']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $course['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($course['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning me-1" 
                                                    data-bs-toggle="modal" data-bs-target="#editCourseModal"
                                                    onclick="editCourse(<?php echo htmlspecialchars(json_encode($course)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                                <button type="submit" name="delete_course" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this course?')">
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

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_code" class="form-label">Course Code</label>
                                    <input type="text" class="form-control" id="course_code" name="course_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="credits" class="form-label">Credits</label>
                                    <input type="number" class="form-control" id="credits" name="credits" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="course_name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">Instructor</label>
                                    <select class="form-control" id="instructor_id" name="instructor_id">
                                        <option value="">Select Instructor</option>
                                        <?php foreach($instructors as $instructor): ?>
                                        <option value="<?php echo $instructor['instructor_id']; ?>">
                                            <?php echo htmlspecialchars($instructor['instructor_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_enrollment" class="form-label">Max Enrollment</label>
                                    <input type="number" class="form-control" id="max_enrollment" name="max_enrollment" value="30">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select class="form-control" id="semester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="Spring">Spring</option>
                                        <option value="Summer">Summer</option>
                                        <option value="Fall">Fall</option>
                                        <option value="Winter">Winter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="year" name="year" value="<?php echo date('Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" id="edit_course_id" name="course_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_course_code" class="form-label">Course Code</label>
                                    <input type="text" class="form-control" id="edit_course_code" name="course_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_credits" class="form-label">Credits</label>
                                    <input type="number" class="form-control" id="edit_credits" name="credits" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_course_name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="edit_course_name" name="course_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_instructor_id" class="form-label">Instructor</label>
                                    <select class="form-control" id="edit_instructor_id" name="instructor_id">
                                        <option value="">Select Instructor</option>
                                        <?php foreach($instructors as $instructor): ?>
                                        <option value="<?php echo $instructor['instructor_id']; ?>">
                                            <?php echo htmlspecialchars($instructor['instructor_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_max_enrollment" class="form-label">Max Enrollment</label>
                                    <input type="number" class="form-control" id="edit_max_enrollment" name="max_enrollment">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_semester" class="form-label">Semester</label>
                                    <select class="form-control" id="edit_semester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="Spring">Spring</option>
                                        <option value="Summer">Summer</option>
                                        <option value="Fall">Fall</option>
                                        <option value="Winter">Winter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="edit_year" name="year" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-control" id="edit_status" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_course" class="btn btn-primary">Update Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editCourse(course) {
            document.getElementById('edit_course_id').value = course.course_id;
            document.getElementById('edit_course_code').value = course.course_code;
            document.getElementById('edit_course_name').value = course.course_name;
            document.getElementById('edit_description').value = course.description || '';
            document.getElementById('edit_credits').value = course.credits;
            document.getElementById('edit_instructor_id').value = course.instructor_id || '';
            document.getElementById('edit_max_enrollment').value = course.max_enrollment;
            document.getElementById('edit_semester').value = course.semester;
            document.getElementById('edit_year').value = course.year;
            document.getElementById('edit_status').value = course.status;
        }
    </script>
</body>
</html>
