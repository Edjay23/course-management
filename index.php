<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Course Management System</title>
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
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
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
                            <a class="nav-link active" href="index.php">
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
                    <h1 class="h2">Dashboard</h1>
                </div>

                <?php
                require_once 'config/database.php';
                
                $database = new Database();
                $db = $database->getConnection();

                // Get statistics
                $stmt = $db->query("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
                $total_students = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $db->query("SELECT COUNT(*) as count FROM instructors WHERE status = 'active'");
                $total_instructors = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $db->query("SELECT COUNT(*) as count FROM courses WHERE status = 'active'");
                $total_courses = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $db->query("SELECT COUNT(*) as count FROM enrollments WHERE status = 'enrolled'");
                $total_enrollments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Students</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_students; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Instructors</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_instructors; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Courses</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_courses; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-book fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total Enrollments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_enrollments; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Enrollments -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Enrollments</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $db->query("
                            SELECT 
                                CONCAT(s.first_name, ' ', s.last_name) as student_name,
                                c.course_name,
                                c.course_code,
                                e.enrollment_date
                            FROM enrollments e
                            JOIN students s ON e.student_id = s.student_id
                            JOIN courses c ON e.course_id = c.course_id
                            ORDER BY e.enrollment_date DESC
                            LIMIT 5
                        ");
                        $recent_enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Course</th>
                                        <th>Course Code</th>
                                        <th>Enrollment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['course_code']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['enrollment_date']); ?></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
