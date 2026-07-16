<?php
require_once APP_ROOT . '/models/Student.php';
require_once APP_ROOT . '/models/Teacher.php';
require_once APP_ROOT . '/models/Attendance.php';
require_once APP_ROOT . '/models/Exam.php';
require_once APP_ROOT . '/models/Fee.php';
require_once APP_ROOT . '/models/SchoolClass.php';

class ReportController {
    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
    }

    public function index() {
        $pageTitle = 'Academic & Administrative Reports';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/reports/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function students() {
        $studentModel = new Student();
        $classModel = new SchoolClass();

        $classId = (int)($_GET['class_id'] ?? 0);
        $level = sanitize($_GET['education_level'] ?? '');

        $students = $studentModel->getAll('', $classId, $level, 'active', 1, 100);
        $classes = $classModel->getAll();

        $pageTitle = 'Student Roster Report';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/reports/students.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function teachers() {
        $teacherModel = new Teacher();
        $teachers = $teacherModel->getAll('', 'active', 1, 100);

        $pageTitle = 'Teacher Staff Directory';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/reports/teachers.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function fees() {
        $feeModel = new Fee();
        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        $payments = $feeModel->getAllPayments($yearId, '', 1, 100);

        $pageTitle = 'Fee Collection Statement';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/reports/fees.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
}
