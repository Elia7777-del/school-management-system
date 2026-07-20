-- ============================================================================
-- School Management System — Database Schema
-- Engine  : InnoDB
-- Charset : utf8mb4_unicode_ci
-- ============================================================================

CREATE DATABASE IF NOT EXISTS school_ms
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE school_ms;

-- Disable FK checks so tables can be dropped / created in any order
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. ROLES
-- ============================================================================
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `role_name`   VARCHAR(50)  NOT NULL UNIQUE,
    `description` VARCHAR(200) DEFAULT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. USERS
-- ============================================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(50)  NOT NULL UNIQUE,
    `email`      VARCHAR(100) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `role_id`    INT          NOT NULL,
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `last_login` DATETIME     DEFAULT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP    NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_users_role_id` (`role_id`),
    INDEX `idx_users_status`  (`status`),
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. ACADEMIC YEARS
-- ============================================================================
DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
    `id`         INT         NOT NULL AUTO_INCREMENT,
    `year_name`  VARCHAR(20) NOT NULL,
    `start_date` DATE        NOT NULL,
    `end_date`   DATE        NOT NULL,
    `is_active`  TINYINT(1)  NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. SCHOOL TERMS
-- ============================================================================
DROP TABLE IF EXISTS `school_terms`;
CREATE TABLE `school_terms` (
    `id`               INT         NOT NULL AUTO_INCREMENT,
    `academic_year_id` INT         NOT NULL,
    `term_name`        VARCHAR(50) NOT NULL,
    `start_date`       DATE        DEFAULT NULL,
    `end_date`         DATE        DEFAULT NULL,
    `is_active`        TINYINT(1)  NOT NULL DEFAULT 0,
    `created_at`       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_terms_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. CLASSES
-- ============================================================================
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
    `id`              INT         NOT NULL AUTO_INCREMENT,
    `class_name`      VARCHAR(50) NOT NULL,
    `education_level` ENUM('primary','secondary') NOT NULL,
    `section`         VARCHAR(10) DEFAULT NULL,
    `capacity`        INT         NOT NULL DEFAULT 40,
    `created_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_classes_education_level` (`education_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. SUBJECTS
-- ============================================================================
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
    `id`              INT          NOT NULL AUTO_INCREMENT,
    `subject_name`    VARCHAR(100) NOT NULL,
    `subject_code`    VARCHAR(20)  NOT NULL UNIQUE,
    `education_level` ENUM('primary','secondary') NOT NULL,
    `description`     TEXT         DEFAULT NULL,
    `status`          ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_subjects_education_level` (`education_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. CLASS ↔ SUBJECTS  (pivot)
-- ============================================================================
DROP TABLE IF EXISTS `class_subjects`;
CREATE TABLE `class_subjects` (
    `id`         INT       NOT NULL AUTO_INCREMENT,
    `class_id`   INT       NOT NULL,
    `subject_id` INT       NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_class_subject` (`class_id`, `subject_id`),
    CONSTRAINT `fk_cs_class`   FOREIGN KEY (`class_id`)   REFERENCES `classes`  (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cs_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. PARENTS
-- ============================================================================
DROP TABLE IF EXISTS `parents`;
CREATE TABLE `parents` (
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `user_id`      INT          DEFAULT NULL,
    `first_name`   VARCHAR(50)  NOT NULL,
    `last_name`    VARCHAR(50)  NOT NULL,
    `phone`        VARCHAR(20)  DEFAULT NULL,
    `email`        VARCHAR(100) DEFAULT NULL,
    `address`      TEXT         DEFAULT NULL,
    `occupation`   VARCHAR(100) DEFAULT NULL,
    `relationship` ENUM('father','mother','guardian') NOT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   TIMESTAMP    NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_parents_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. STUDENTS
-- ============================================================================
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `user_id`          INT          DEFAULT NULL,
    `admission_number` VARCHAR(30)  NOT NULL UNIQUE,
    `first_name`       VARCHAR(50)  NOT NULL,
    `middle_name`      VARCHAR(50)  DEFAULT NULL,
    `last_name`        VARCHAR(50)  NOT NULL,
    `gender`           ENUM('male','female') NOT NULL,
    `date_of_birth`    DATE         NOT NULL,
    `class_id`         INT          NOT NULL,
    `education_level`  ENUM('primary','secondary') NOT NULL,
    `phone`            VARCHAR(20)  DEFAULT NULL,
    `address`          TEXT         DEFAULT NULL,
    `photo`            VARCHAR(255) DEFAULT NULL,
    `status`           ENUM('active','inactive','graduated','transferred') NOT NULL DEFAULT 'active',
    `admission_date`   DATE         NOT NULL,
    `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       TIMESTAMP    NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_students_class_id`        (`class_id`),
    INDEX `idx_students_education_level` (`education_level`),
    INDEX `idx_students_status`          (`status`),
    CONSTRAINT `fk_students_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`   (`id`),
    CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. STUDENT ↔ PARENTS  (pivot)
-- ============================================================================
DROP TABLE IF EXISTS `student_parents`;
CREATE TABLE `student_parents` (
    `id`         INT NOT NULL AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `parent_id`  INT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_student_parent` (`student_id`, `parent_id`),
    CONSTRAINT `fk_sp_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sp_parent`  FOREIGN KEY (`parent_id`)  REFERENCES `parents`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. TEACHERS
-- ============================================================================
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
    `id`             INT          NOT NULL AUTO_INCREMENT,
    `user_id`        INT          DEFAULT NULL,
    `teacher_number` VARCHAR(30)  NOT NULL UNIQUE,
    `first_name`     VARCHAR(50)  NOT NULL,
    `last_name`      VARCHAR(50)  NOT NULL,
    `gender`         ENUM('male','female') NOT NULL,
    `phone`          VARCHAR(20)  DEFAULT NULL,
    `email`          VARCHAR(100) DEFAULT NULL,
    `qualification`  VARCHAR(200) DEFAULT NULL,
    `specialization` VARCHAR(200) DEFAULT NULL,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `join_date`      DATE         DEFAULT NULL,
    `photo`          VARCHAR(255) DEFAULT NULL,
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     TIMESTAMP    NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_teachers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. TEACHER ↔ SUBJECTS  (pivot)
-- ============================================================================
DROP TABLE IF EXISTS `teacher_subjects`;
CREATE TABLE `teacher_subjects` (
    `id`         INT NOT NULL AUTO_INCREMENT,
    `teacher_id` INT NOT NULL,
    `subject_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_teacher_subject` (`teacher_id`, `subject_id`),
    CONSTRAINT `fk_ts_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ts_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. TEACHER ↔ CLASSES  (pivot)
-- ============================================================================
DROP TABLE IF EXISTS `teacher_classes`;
CREATE TABLE `teacher_classes` (
    `id`               INT        NOT NULL AUTO_INCREMENT,
    `teacher_id`       INT        NOT NULL,
    `class_id`         INT        NOT NULL,
    `academic_year_id` INT        NOT NULL,
    `is_class_teacher` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`       TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_teacher_class_year` (`teacher_id`, `class_id`, `academic_year_id`),
    CONSTRAINT `fk_tc_teacher`       FOREIGN KEY (`teacher_id`)       REFERENCES `teachers`       (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tc_class`         FOREIGN KEY (`class_id`)         REFERENCES `classes`         (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tc_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 14. ATTENDANCE
-- ============================================================================
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
    `id`               INT  NOT NULL AUTO_INCREMENT,
    `student_id`       INT  NOT NULL,
    `class_id`         INT  NOT NULL,
    `academic_year_id` INT  NOT NULL,
    `term_id`          INT  NOT NULL,
    `date`             DATE NOT NULL,
    `status`           ENUM('present','absent','late') NOT NULL DEFAULT 'present',
    `remarks`          TEXT DEFAULT NULL,
    `recorded_by`      INT  NOT NULL,
    `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_attendance_class_date` (`class_id`, `date`),
    UNIQUE KEY `uk_attendance_student_date` (`student_id`, `date`),
    CONSTRAINT `fk_att_student`       FOREIGN KEY (`student_id`)       REFERENCES `students`       (`id`),
    CONSTRAINT `fk_att_class`         FOREIGN KEY (`class_id`)         REFERENCES `classes`         (`id`),
    CONSTRAINT `fk_att_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`  (`id`),
    CONSTRAINT `fk_att_term`          FOREIGN KEY (`term_id`)          REFERENCES `school_terms`    (`id`),
    CONSTRAINT `fk_att_recorded_by`   FOREIGN KEY (`recorded_by`)      REFERENCES `users`           (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 15. EXAMS
-- ============================================================================
DROP TABLE IF EXISTS `exams`;
CREATE TABLE `exams` (
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `exam_name`        VARCHAR(100) NOT NULL,
    `exam_type`        ENUM('midterm','terminal','annual') NOT NULL,
    `class_id`         INT          NOT NULL,
    `academic_year_id` INT          NOT NULL,
    `term_id`          INT          NOT NULL,
    `start_date`       DATE         DEFAULT NULL,
    `end_date`         DATE         DEFAULT NULL,
    `status`           ENUM('pending','ongoing','completed') NOT NULL DEFAULT 'pending',
    `created_by`       INT          NOT NULL,
    `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_exams_class`         FOREIGN KEY (`class_id`)         REFERENCES `classes`        (`id`),
    CONSTRAINT `fk_exams_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_exams_term`          FOREIGN KEY (`term_id`)          REFERENCES `school_terms`   (`id`),
    CONSTRAINT `fk_exams_created_by`    FOREIGN KEY (`created_by`)       REFERENCES `users`          (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 16. EXAM RESULTS
-- ============================================================================
DROP TABLE IF EXISTS `exam_results`;
CREATE TABLE `exam_results` (
    `id`             INT          NOT NULL AUTO_INCREMENT,
    `exam_id`        INT          NOT NULL,
    `student_id`     INT          NOT NULL,
    `subject_id`     INT          NOT NULL,
    `marks_obtained` DECIMAL(5,2) NOT NULL,
    `grade`          VARCHAR(5)   DEFAULT NULL,
    `points`         INT          DEFAULT NULL,
    `remarks`        VARCHAR(50)  DEFAULT NULL,
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_exam_student_subject` (`exam_id`, `student_id`, `subject_id`),
    CONSTRAINT `fk_er_exam`    FOREIGN KEY (`exam_id`)    REFERENCES `exams`    (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_er_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_er_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 17. FEE STRUCTURES
-- ============================================================================
DROP TABLE IF EXISTS `fee_structures`;
CREATE TABLE `fee_structures` (
    `id`               INT           NOT NULL AUTO_INCREMENT,
    `class_id`         INT           NOT NULL,
    `academic_year_id` INT           NOT NULL,
    `term_id`          INT           DEFAULT NULL,
    `amount`           DECIMAL(12,2) NOT NULL,
    `description`      VARCHAR(200)  DEFAULT NULL,
    `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_fs_class`         FOREIGN KEY (`class_id`)         REFERENCES `classes`        (`id`),
    CONSTRAINT `fk_fs_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_fs_term`          FOREIGN KEY (`term_id`)          REFERENCES `school_terms`   (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 18. FEE PAYMENTS
-- ============================================================================
DROP TABLE IF EXISTS `fee_payments`;
CREATE TABLE `fee_payments` (
    `id`               INT           NOT NULL AUTO_INCREMENT,
    `student_id`       INT           NOT NULL,
    `academic_year_id` INT           NOT NULL,
    `term_id`          INT           DEFAULT NULL,
    `amount_paid`      DECIMAL(12,2) NOT NULL,
    `payment_date`     DATE          NOT NULL,
    `payment_method`   ENUM('cash','bank','mobile_money') NOT NULL DEFAULT 'cash',
    `receipt_number`   VARCHAR(50)   NOT NULL UNIQUE,
    `recorded_by`      INT           NOT NULL,
    `remarks`          TEXT          DEFAULT NULL,
    `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_fp_student_id`    (`student_id`),
    INDEX `idx_fp_payment_date`  (`payment_date`),
    CONSTRAINT `fk_fp_student`       FOREIGN KEY (`student_id`)       REFERENCES `students`       (`id`),
    CONSTRAINT `fk_fp_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_fp_term`          FOREIGN KEY (`term_id`)          REFERENCES `school_terms`   (`id`),
    CONSTRAINT `fk_fp_recorded_by`   FOREIGN KEY (`recorded_by`)      REFERENCES `users`          (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 19. TIMETABLES
-- ============================================================================
DROP TABLE IF EXISTS `timetables`;
CREATE TABLE `timetables` (
    `id`               INT  NOT NULL AUTO_INCREMENT,
    `class_id`         INT  NOT NULL,
    `subject_id`       INT  NOT NULL,
    `teacher_id`       INT  NOT NULL,
    `academic_year_id` INT  NOT NULL,
    `term_id`          INT  NOT NULL,
    `day_of_week`      ENUM('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
    `start_time`       TIME NOT NULL,
    `end_time`         TIME NOT NULL,
    `room`             VARCHAR(50) DEFAULT NULL,
    `created_at`       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_timetable_slot` (`class_id`, `day_of_week`, `start_time`),
    CONSTRAINT `fk_tt_class`         FOREIGN KEY (`class_id`)         REFERENCES `classes`        (`id`),
    CONSTRAINT `fk_tt_subject`       FOREIGN KEY (`subject_id`)       REFERENCES `subjects`       (`id`),
    CONSTRAINT `fk_tt_teacher`       FOREIGN KEY (`teacher_id`)       REFERENCES `teachers`       (`id`),
    CONSTRAINT `fk_tt_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_tt_term`          FOREIGN KEY (`term_id`)          REFERENCES `school_terms`   (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 20. ANNOUNCEMENTS
-- ============================================================================
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(200) NOT NULL,
    `body`       TEXT         NOT NULL,
    `audience`   ENUM('all','teachers','students','parents') NOT NULL DEFAULT 'all',
    `created_by` INT          NOT NULL,
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_ann_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 21. TEACHER ACTIVITY LOGS
-- ============================================================================
DROP TABLE IF EXISTS `teacher_activity_logs`;
CREATE TABLE `teacher_activity_logs` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `user_id`     INT          NOT NULL,
    `action_type` VARCHAR(50)  NOT NULL,
    `description` TEXT         NOT NULL,
    `ip_address`  VARCHAR(45)  NOT NULL,
    `request_url` VARCHAR(255) NOT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_tal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
