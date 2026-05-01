-- Academic GPA Management System
-- MySQL 8+ recommended

CREATE DATABASE IF NOT EXISTS gpa_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gpa_management;

-- Users: admin, professor, student
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'professor', 'student') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE semesters (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(120) NOT NULL,
  academic_year VARCHAR(20) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_semesters_label_year (label, academic_year)
) ENGINE=InnoDB;

CREATE TABLE courses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  semester_id INT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  credits DECIMAL(4,1) UNSIGNED NOT NULL DEFAULT 3.0,
  CONSTRAINT fk_courses_semester
    FOREIGN KEY (semester_id) REFERENCES semesters(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_courses_semester ON courses (semester_id);

-- Student enrolled in a semester (many-to-many)
CREATE TABLE enrollments (
  student_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NOT NULL,
  enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (student_id, semester_id),
  CONSTRAINT fk_enroll_student FOREIGN KEY (student_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_enroll_semester FOREIGN KEY (semester_id) REFERENCES semesters(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Professor assigned to teach a course in a semester
CREATE TABLE assignments (
  professor_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (professor_id, course_id, semester_id),
  CONSTRAINT fk_assign_prof FOREIGN KEY (professor_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_assign_course FOREIGN KEY (course_id) REFERENCES courses(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_assign_sem FOREIGN KEY (semester_id) REFERENCES semesters(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_assign_course_sem ON assignments (course_id, semester_id);

CREATE TABLE grades (
  student_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NOT NULL,
  professor_id INT UNSIGNED NOT NULL,
  grade CHAR(1) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (student_id, course_id, semester_id),
  CONSTRAINT fk_grade_student FOREIGN KEY (student_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_grade_course FOREIGN KEY (course_id) REFERENCES courses(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_grade_sem FOREIGN KEY (semester_id) REFERENCES semesters(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_grade_prof FOREIGN KEY (professor_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_grade_letter CHECK (grade IS NULL OR grade IN ('A','B','C','D','F'))
) ENGINE=InnoDB;

CREATE TABLE gpa_records (
  student_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NOT NULL,
  gpa DECIMAL(5,2) NOT NULL,
  computed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (student_id, semester_id),
  CONSTRAINT fk_gpa_student FOREIGN KEY (student_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_gpa_sem FOREIGN KEY (semester_id) REFERENCES semesters(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Seed: default password for all seeded users is "password" (bcrypt)
INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@gpa.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Dr. Jane Smith', 'professor@gpa.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor'),
('Alex Student', 'student@gpa.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

INSERT INTO semesters (label, academic_year, is_active) VALUES
('Fall', '2025-2026', 1),
('Spring', '2025-2026', 0);

INSERT INTO courses (semester_id, name, credits) VALUES
(1, 'Introduction to Computer Science', 4.0),
(1, 'Database Systems', 3.0);

INSERT INTO enrollments (student_id, semester_id) VALUES
(3, 1);

INSERT INTO assignments (professor_id, course_id, semester_id) VALUES
(2, 1, 1),
(2, 2, 1);
