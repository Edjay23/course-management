<?php
header('Content-Type: application/json');
require_once 'config/database.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

try {
    switch ($endpoint) {
        case 'courses':
            handleCoursesAPI($method);
            break;
        case 'students':
            handleStudentsAPI($method);
            break;
        case 'instructors':
            handleInstructorsAPI($method);
            break;
        case 'enrollments':
            handleEnrollmentsAPI($method);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}

function handleCoursesAPI($method) {
    global $pdo;
    
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM courses ORDER BY course_name");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $courses]);
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['course_name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Course name is required']);
                return;
            }
            
            $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, description, credits, instructor_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['course_code'] ?? '',
                $input['course_name'],
                $input['description'] ?? '',
                $input['credits'] ?? 3,
                $input['instructor_id'] ?? null
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

function handleStudentsAPI($method) {
    global $pdo;
    
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM students ORDER BY last_name, first_name");
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $students]);
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['first_name']) || !isset($input['last_name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'First name and last name are required']);
                return;
            }
            
            $stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, email, phone, date_of_birth) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['student_id'] ?? null,
                $input['first_name'],
                $input['last_name'],
                $input['email'] ?? '',
                $input['phone'] ?? '',
                $input['date_of_birth'] ?? null
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

function handleInstructorsAPI($method) {
    global $pdo;
    
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM instructors ORDER BY last_name, first_name");
            $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $instructors]);
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['first_name']) || !isset($input['last_name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'First name and last name are required']);
                return;
            }
            
            $stmt = $pdo->prepare("INSERT INTO instructors (first_name, last_name, email, phone, department) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['first_name'],
                $input['last_name'],
                $input['email'] ?? '',
                $input['phone'] ?? '',
                $input['department'] ?? ''
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

function handleEnrollmentsAPI($method) {
    global $pdo;
    
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("
                SELECT e.*, s.first_name as student_first_name, s.last_name as student_last_name, 
                       c.course_name, c.course_code
                FROM enrollments e
                JOIN students s ON e.student_id = s.id
                JOIN courses c ON e.course_id = c.id
                ORDER BY e.enrollment_date DESC
            ");
            $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $enrollments]);
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['student_id']) || !isset($input['course_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Student ID and Course ID are required']);
                return;
            }
            
            $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, enrollment_date, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $input['student_id'],
                $input['course_id'],
                $input['enrollment_date'] ?? date('Y-m-d'),
                $input['status'] ?? 'enrolled'
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}
?>
