-- ============================================================================
-- School Management System — Seed Data
-- ============================================================================
-- All passwords are 'password' (bcrypt hash below).
-- Run this AFTER schema.sql.
-- ============================================================================

-- Disable FK checks during seeding to avoid insert-order issues
SET FOREIGN_KEY_CHECKS = 0;

USE school_ms;

-- ============================================================================
-- 1. ROLES
-- ============================================================================
INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'super_admin',  'Super Administrator with full system access'),
(2, 'school_admin', 'School Administrator with management access'),
(3, 'teacher',      'Teacher with class and grade management access'),
(4, 'student',      'Student with limited portal access'),
(5, 'parent',       'Parent / Guardian with child monitoring access');

-- ============================================================================
-- 2. USERS
-- ============================================================================
-- Password for ALL users: 'password'
-- Bcrypt hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `status`) VALUES
(1, 'superadmin',  'superadmin@school.co.tz',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
(2, 'schooladmin', 'schooladmin@school.co.tz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'active'),
(3, 'teacher1',    'teacher1@school.co.tz',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'active'),
(4, 'student1',    'student1@school.co.tz',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 'active'),
(5, 'parent1',     'parent1@school.co.tz',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 'active');

-- ============================================================================
-- 3. ACADEMIC YEARS
-- ============================================================================
INSERT INTO `academic_years` (`id`, `year_name`, `start_date`, `end_date`, `is_active`) VALUES
(1, '2026', '2026-01-06', '2026-11-27', 1);

-- ============================================================================
-- 4. SCHOOL TERMS
-- ============================================================================
INSERT INTO `school_terms` (`id`, `academic_year_id`, `term_name`, `start_date`, `end_date`, `is_active`) VALUES
(1, 1, 'Term I',   '2026-01-06', '2026-04-03', 1),
(2, 1, 'Term II',  '2026-05-04', '2026-08-07', 0),
(3, 1, 'Term III', '2026-09-01', '2026-11-27', 0);

-- ============================================================================
-- 5. CLASSES
-- ============================================================================
-- Primary: Standard I – VII  (IDs 1-7)
-- Secondary: Form I – IV     (IDs 8-11)
INSERT INTO `classes` (`id`, `class_name`, `education_level`) VALUES
(1,  'Standard I',   'primary'),
(2,  'Standard II',  'primary'),
(3,  'Standard III', 'primary'),
(4,  'Standard IV',  'primary'),
(5,  'Standard V',   'primary'),
(6,  'Standard VI',  'primary'),
(7,  'Standard VII', 'primary'),
(8,  'Form I',       'secondary'),
(9,  'Form II',      'secondary'),
(10, 'Form III',     'secondary'),
(11, 'Form IV',      'secondary');

-- ============================================================================
-- 6. SUBJECTS
-- ============================================================================
-- Primary subjects  (IDs 1-6)
-- Secondary subjects (IDs 7-17)
INSERT INTO `subjects` (`id`, `subject_name`, `subject_code`, `education_level`) VALUES
-- Primary
(1,  'Mathematics',    'PM001',  'primary'),
(2,  'English',        'PE002',  'primary'),
(3,  'Kiswahili',      'PK003',  'primary'),
(4,  'Science',        'PS004',  'primary'),
(5,  'Social Studies', 'PSS005', 'primary'),
(6,  'Civics',         'PC006',  'primary'),
-- Secondary
(7,  'Mathematics',    'SM001',  'secondary'),
(8,  'English',        'SE002',  'secondary'),
(9,  'Kiswahili',      'SK003',  'secondary'),
(10, 'Biology',        'SB004',  'secondary'),
(11, 'Chemistry',      'SC005',  'secondary'),
(12, 'Physics',        'SP006',  'secondary'),
(13, 'Geography',      'SG007',  'secondary'),
(14, 'History',        'SH008',  'secondary'),
(15, 'Civics',         'SCV009', 'secondary'),
(16, 'Commerce',       'SCM010', 'secondary'),
(17, 'Book Keeping',   'SBK011', 'secondary');

-- ============================================================================
-- 7. CLASS ↔ SUBJECTS
-- ============================================================================
-- Link each primary class (1-7) to all 6 primary subjects (1-6)
INSERT INTO `class_subjects` (`class_id`, `subject_id`) VALUES
-- Standard I
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
-- Standard II
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6),
-- Standard III
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6),
-- Standard IV
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 6),
-- Standard V
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5), (5, 6),
-- Standard VI
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6),
-- Standard VII
(7, 1), (7, 2), (7, 3), (7, 4), (7, 5), (7, 6);

-- Link each secondary class (8-11) to all 11 secondary subjects (7-17)
INSERT INTO `class_subjects` (`class_id`, `subject_id`) VALUES
-- Form I
(8,  7), (8,  8), (8,  9), (8,  10), (8,  11), (8,  12), (8,  13), (8,  14), (8,  15), (8,  16), (8,  17),
-- Form II
(9,  7), (9,  8), (9,  9), (9,  10), (9,  11), (9,  12), (9,  13), (9,  14), (9,  15), (9,  16), (9,  17),
-- Form III
(10, 7), (10, 8), (10, 9), (10, 10), (10, 11), (10, 12), (10, 13), (10, 14), (10, 15), (10, 16), (10, 17),
-- Form IV
(11, 7), (11, 8), (11, 9), (11, 10), (11, 11), (11, 12), (11, 13), (11, 14), (11, 15), (11, 16), (11, 17);

-- ============================================================================
-- 8. PARENTS
-- ============================================================================
INSERT INTO `parents` (`id`, `user_id`, `first_name`, `last_name`, `phone`, `email`, `address`, `occupation`, `relationship`) VALUES
(1, 5,    'John',  'Mwanga', '0712345678', 'john@example.com', 'Dar es Salaam', 'Business', 'father'),
(2, NULL, 'Amina', 'Hassan', '0713456789', NULL,               'Dodoma',        'Teacher',  'mother');

-- ============================================================================
-- 9. STUDENTS
-- ============================================================================
INSERT INTO `students` (`id`, `user_id`, `admission_number`, `first_name`, `middle_name`, `last_name`, `gender`, `date_of_birth`, `class_id`, `education_level`, `phone`, `address`, `photo`, `status`, `admission_date`) VALUES
(1, 4,    'ADM-2026-001', 'Baraka',   'James', 'Mwanga', 'male',   '2015-03-15', 3,  'primary',   NULL, 'Dar es Salaam', NULL, 'active', '2026-01-06'),
(2, NULL, 'ADM-2026-002', 'Neema',    NULL,    'Joseph', 'female', '2014-07-20', 5,  'primary',   NULL, 'Arusha',        NULL, 'active', '2026-01-06'),
(3, NULL, 'ADM-2026-003', 'Juma',     'Ali',   'Hassan', 'male',   '2013-11-10', 7,  'primary',   NULL, 'Dodoma',        NULL, 'active', '2026-01-06'),
(4, NULL, 'ADM-2026-004', 'Fatma',    NULL,    'Salim',  'female', '2010-02-28', 8,  'secondary', NULL, 'Mwanza',        NULL, 'active', '2026-01-06'),
(5, NULL, 'ADM-2026-005', 'Emmanuel', 'Peter', 'Shayo',  'male',   '2009-06-14', 10, 'secondary', NULL, 'Dar es Salaam', NULL, 'active', '2026-01-06');

-- ============================================================================
-- 10. STUDENT ↔ PARENTS
-- ============================================================================
INSERT INTO `student_parents` (`student_id`, `parent_id`) VALUES
(1, 1),  -- Baraka → John Mwanga (father)
(3, 2),  -- Juma   → Amina Hassan (mother)
(4, 2);  -- Fatma  → Amina Hassan (mother)

-- ============================================================================
-- 11. TEACHERS
-- ============================================================================
INSERT INTO `teachers` (`id`, `user_id`, `teacher_number`, `first_name`, `last_name`, `gender`, `phone`, `email`, `qualification`, `specialization`, `status`, `join_date`) VALUES
(1, 3,    'TSC-001', 'Grace',  'Kimaro', 'female', '0714567890', 'grace@school.co.tz',  'Bachelor of Education', 'Mathematics', 'active', '2020-01-15'),
(2, NULL, 'TSC-002', 'Robert', 'Mushi',  'male',   '0715678901', 'robert@school.co.tz', 'Diploma in Education',  'English',     'active', '2021-03-20');

-- ============================================================================
-- 12. TEACHER ↔ SUBJECTS
-- ============================================================================
INSERT INTO `teacher_subjects` (`teacher_id`, `subject_id`) VALUES
(1, 1),  -- Grace  → Mathematics (primary)
(1, 7),  -- Grace  → Mathematics (secondary)
(2, 2),  -- Robert → English (primary)
(2, 8);  -- Robert → English (secondary)

-- ============================================================================
-- 13. TEACHER ↔ CLASSES
-- ============================================================================
INSERT INTO `teacher_classes` (`teacher_id`, `class_id`, `academic_year_id`, `is_class_teacher`) VALUES
(1, 3, 1, 1),  -- Grace  → Standard III, class teacher
(1, 8, 1, 0),  -- Grace  → Form I
(2, 5, 1, 1);  -- Robert → Standard V, class teacher

-- ============================================================================
-- 14. ATTENDANCE  (2026-01-06)
-- ============================================================================
INSERT INTO `attendance` (`student_id`, `class_id`, `academic_year_id`, `term_id`, `date`, `status`, `remarks`, `recorded_by`) VALUES
(1, 3,  1, 1, '2026-01-06', 'present', NULL,                  3),
(2, 5,  1, 1, '2026-01-06', 'present', NULL,                  3),
(3, 7,  1, 1, '2026-01-06', 'present', NULL,                  3),
(4, 8,  1, 1, '2026-01-06', 'absent',  'Not yet reported',    3),
(5, 10, 1, 1, '2026-01-06', 'late',    'Arrived at 08:15 AM', 3);

-- ============================================================================
-- 15. EXAMS
-- ============================================================================
INSERT INTO `exams` (`id`, `exam_name`, `exam_type`, `class_id`, `academic_year_id`, `term_id`, `start_date`, `end_date`, `status`, `created_by`) VALUES
(1, 'Midterm Examination', 'midterm', 3, 1, 1, '2026-02-15', '2026-02-20', 'completed', 3);

-- ============================================================================
-- 16. EXAM RESULTS  (Student 1 — Exam 1)
-- ============================================================================
INSERT INTO `exam_results` (`exam_id`, `student_id`, `subject_id`, `marks_obtained`, `grade`, `points`, `remarks`) VALUES
(1, 1, 1, 85.00, 'A', NULL, 'Excellent'),
(1, 1, 2, 72.00, 'B', NULL, 'Very Good'),
(1, 1, 3, 65.00, 'B', NULL, 'Very Good'),
(1, 1, 4, 58.00, 'C', NULL, 'Good');

-- ============================================================================
-- 17. FEE STRUCTURES
-- ============================================================================
INSERT INTO `fee_structures` (`id`, `class_id`, `academic_year_id`, `term_id`, `amount`, `description`) VALUES
(1, 3, 1, 1, 150000.00, 'Tuition Fee - Standard III Term I'),
(2, 8, 1, 1, 250000.00, 'Tuition Fee - Form I Term I');

-- ============================================================================
-- 18. FEE PAYMENTS
-- ============================================================================
INSERT INTO `fee_payments` (`id`, `student_id`, `academic_year_id`, `term_id`, `amount_paid`, `payment_date`, `payment_method`, `receipt_number`, `recorded_by`, `remarks`) VALUES
(1, 1, 1, 1, 100000.00, '2026-01-10', 'cash',         'RCP-20260110-001', 2, 'Partial payment'),
(2, 4, 1, 1, 250000.00, '2026-01-08', 'mobile_money', 'RCP-20260108-001', 2, 'Full payment');

-- ============================================================================
-- 19. TIMETABLE  (Form I — Monday)
-- ============================================================================
INSERT INTO `timetables` (`id`, `class_id`, `subject_id`, `teacher_id`, `academic_year_id`, `term_id`, `day_of_week`, `start_time`, `end_time`, `room`) VALUES
(1, 8, 7, 1, 1, 1, 'Monday', '07:30:00', '08:30:00', 'Room 1'),  -- Mathematics
(2, 8, 8, 2, 1, 1, 'Monday', '08:30:00', '09:30:00', 'Room 1'),  -- English
(3, 8, 9, 2, 1, 1, 'Monday', '10:00:00', '11:00:00', 'Room 1');  -- Kiswahili

-- ============================================================================
-- 20. ANNOUNCEMENTS
-- ============================================================================
INSERT INTO `announcements` (`id`, `title`, `body`, `audience`, `created_by`, `is_active`) VALUES
(1, 'Welcome to Term I 2026', 'Welcome back students to the new academic year 2026. We wish you all the best in your studies. Please ensure you have all the required learning materials and report to your respective classes on time.', 'all', 1, 1);

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- END OF SEED DATA
-- ============================================================================
