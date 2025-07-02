# Course Management System

## Overview
This is a comprehensive course management system designed for universities to handle course registration, student enrollment, instructor assignment, and grade recording. The system provides a clean, modern interface built with Bootstrap and uses PHP with MySQL for backend operations.

## Entity-Relationship Diagram (ERD)

### Entities and Attributes

#### 1. Students
- **Primary Key**: student_id (INT, AUTO_INCREMENT)
- **Attributes**:
  - first_name (VARCHAR(50), NOT NULL)
  - last_name (VARCHAR(50), NOT NULL)
  - email (VARCHAR(100), UNIQUE, NOT NULL)
  - phone (VARCHAR(15))
  - date_of_birth (DATE)
  - enrollment_date (DATE, DEFAULT CURRENT_DATE)
  - status (ENUM: 'active', 'inactive', 'graduated', DEFAULT 'active')
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

#### 2. Instructors
- **Primary Key**: instructor_id (INT, AUTO_INCREMENT)
- **Attributes**:
  - first_name (VARCHAR(50), NOT NULL)
  - last_name (VARCHAR(50), NOT NULL)
  - email (VARCHAR(100), UNIQUE, NOT NULL)
  - phone (VARCHAR(15))
  - department (VARCHAR(100))
  - hire_date (DATE)
  - salary (DECIMAL(10,2))
  - status (ENUM: 'active', 'inactive', DEFAULT 'active')
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

#### 3. Courses
- **Primary Key**: course_id (INT, AUTO_INCREMENT)
- **Attributes**:
  - course_code (VARCHAR(10), UNIQUE, NOT NULL)
  - course_name (VARCHAR(100), NOT NULL)
  - description (TEXT)
  - credits (INT, NOT NULL)
  - instructor_id (INT, FOREIGN KEY)
  - max_enrollment (INT, DEFAULT 30)
  - semester (VARCHAR(20))
  - year (YEAR)
  - status (ENUM: 'active', 'inactive', DEFAULT 'active')
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

#### 4. Enrollments
- **Primary Key**: enrollment_id (INT, AUTO_INCREMENT)
- **Attributes**:
  - student_id (INT, FOREIGN KEY, NOT NULL)
  - course_id (INT, FOREIGN KEY, NOT NULL)
  - enrollment_date (DATE, DEFAULT CURRENT_DATE)
  - status (ENUM: 'enrolled', 'dropped', 'completed', DEFAULT 'enrolled')
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- **Composite Unique Key**: (student_id, course_id)

#### 5. Grades
- **Primary Key**: grade_id (INT, AUTO_INCREMENT)
- **Attributes**:
  - enrollment_id (INT, FOREIGN KEY, NOT NULL)
  - assignment_name (VARCHAR(100))
  - grade (DECIMAL(5,2))
  - max_points (DECIMAL(5,2), DEFAULT 100.00)
  - grade_date (DATE, DEFAULT CURRENT_DATE)
  - comments (TEXT)
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

### Relationships

#### 1. Instructors → Courses (One-to-Many)
- **Relationship**: One instructor can teach multiple courses, but each course is taught by one instructor
- **Foreign Key**: courses.instructor_id references instructors.instructor_id
- **Cardinality**: 1:M
- **Constraints**: ON DELETE SET NULL (if instructor is deleted, course instructor becomes NULL)

#### 2. Students ↔ Courses (Many-to-Many through Enrollments)
- **Relationship**: Students can enroll in multiple courses, and courses can have multiple students
- **Bridge Entity**: Enrollments
- **Foreign Keys**: 
  - enrollments.student_id references students.student_id
  - enrollments.course_id references courses.course_id
- **Cardinality**: M:N
- **Constraints**: 
  - ON DELETE CASCADE for both foreign keys
  - UNIQUE constraint on (student_id, course_id) to prevent duplicate enrollments

#### 3. Enrollments → Grades (One-to-Many)
- **Relationship**: One enrollment can have multiple grades (for different assignments)
- **Foreign Key**: grades.enrollment_id references enrollments.enrollment_id
- **Cardinality**: 1:M
- **Constraints**: ON DELETE CASCADE (if enrollment is deleted, all associated grades are deleted)

### Database Constraints and Business Rules

#### Primary Constraints:
1. **Unique Email Addresses**: Both students and instructors must have unique email addresses
2. **Unique Course Codes**: Each course must have a unique course code
3. **Enrollment Uniqueness**: A student cannot enroll in the same course multiple times
4. **Non-negative Values**: Credits, max_enrollment, grade, and max_points must be positive values

#### Business Rules:
1. **Active Status Management**: Only active students can enroll in courses
2. **Course Capacity**: Enrollments should not exceed max_enrollment (enforced at application level)
3. **Grade Validation**: Grades should not exceed max_points (enforced at application level)
4. **Semester Validation**: Valid semesters are Spring, Summer, Fall, Winter

### Normalization

The database design follows **Third Normal Form (3NF)**:

#### First Normal Form (1NF):
- All attributes contain atomic values
- No repeating groups or arrays
- Each row is unique with primary keys

#### Second Normal Form (2NF):
- Meets 1NF requirements
- All non-key attributes are fully functionally dependent on primary keys
- No partial dependencies exist

#### Third Normal Form (3NF):
- Meets 2NF requirements
- No transitive dependencies
- Non-key attributes depend only on primary keys

### Implementation Considerations

#### Scalability:
- **Indexing**: Primary keys are automatically indexed. Consider adding indexes on:
  - students.email
  - instructors.email
  - courses.course_code
  - enrollments(student_id, course_id)
  
#### Performance:
- **Connection Pooling**: Implement database connection pooling for high-traffic scenarios
- **Query Optimization**: Use prepared statements and appropriate JOINs
- **Caching**: Consider implementing query result caching for frequently accessed data

#### Security:
- **SQL Injection Prevention**: All queries use prepared statements
- **Data Validation**: Input validation on both client and server side
- **Access Control**: Implement role-based access control for different user types

### Database Setup Instructions

1. **Prerequisites**:
   - XAMPP with Apache and MySQL running
   - Access to phpMyAdmin

2. **Database Creation**:
   ```sql
   -- Import the course_management.sql file in phpMyAdmin
   -- Or run the SQL commands directly in MySQL console
   ```

3. **Configuration**:
   - Update database credentials in `config/database.php`
   - Ensure proper file permissions for web server access

4. **Testing**:
   - Access the application via `http://localhost/app/`
   - Verify all CRUD operations work correctly
   - Test relationships and constraints

This design ensures data integrity, reduces redundancy, and provides a solid foundation for a university course management system while maintaining scalability and performance.
