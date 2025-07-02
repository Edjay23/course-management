-- Course Management System Database
-- Created for University Course Management

CREATE DATABASE IF NOT EXISTS course_management;
USE course_management;

-- Students Table
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    date_of_birth DATE,
    enrollment_date DATE DEFAULT CURRENT_DATE,
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Instructors Table
CREATE TABLE instructors (
    instructor_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(100),
    hire_date DATE,
    salary DECIMAL(10,2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(10) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT NOT NULL,
    instructor_id INT,
    max_enrollment INT DEFAULT 30,
    semester VARCHAR(20),
    year YEAR,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES instructors(instructor_id) ON DELETE SET NULL
);

-- Enrollments Table (Many-to-Many relationship between Students and Courses)
CREATE TABLE enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date DATE DEFAULT CURRENT_DATE,
    status ENUM('enrolled', 'dropped', 'completed') DEFAULT 'enrolled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, course_id)
);

-- Grades Table
CREATE TABLE grades (
    grade_id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT NOT NULL,
    assignment_name VARCHAR(100),
    grade DECIMAL(5,2),
    max_points DECIMAL(5,2) DEFAULT 100.00,
    grade_date DATE DEFAULT CURRENT_DATE,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE
);

-- Sample Data
INSERT INTO instructors (first_name, last_name, email, department, hire_date, salary) VALUES
('John', 'Smith', 'john.smith@university.edu', 'Computer Science', '2020-01-15', 75000.00),
('Sarah', 'Johnson', 'sarah.johnson@university.edu', 'Mathematics', '2019-08-20', 72000.00),
('Michael', 'Brown', 'michael.brown@university.edu', 'Physics', '2021-02-10', 68000.00);

INSERT INTO students (first_name, last_name, email, phone, date_of_birth) VALUES
('Alice', 'Wilson', 'alice.wilson@student.edu', '555-0101', '2000-05-15'),
('Bob', 'Davis', 'bob.davis@student.edu', '555-0102', '1999-12-03'),
('Carol', 'Miller', 'carol.miller@student.edu', '555-0103', '2001-03-22'),
('David', 'Garcia', 'david.garcia@student.edu', '555-0104', '2000-09-08');

INSERT INTO courses (course_code, course_name, description, credits, instructor_id, semester, year) VALUES
('CS101', 'Introduction to Programming', 'Basic programming concepts using Python', 3, 1, 'Fall', 2024),
('MATH201', 'Calculus II', 'Advanced calculus concepts and applications', 4, 2, 'Fall', 2024),
('PHYS101', 'General Physics I', 'Mechanics and thermodynamics', 4, 3, 'Spring', 2025);

INSERT INTO enrollments (student_id, course_id) VALUES
(1, 1), (1, 2),
(2, 1), (2, 3),
(3, 2), (3, 3),
(4, 1);

INSERT INTO grades (enrollment_id, assignment_name, grade, max_points) VALUES
(1, 'Midterm Exam', 85.5, 100.00),
(1, 'Final Project', 92.0, 100.00),
(2, 'Quiz 1', 78.0, 100.00),
(3, 'Homework 1', 95.0, 100.00);
