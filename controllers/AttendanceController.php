<?php
require_once APP_ROOT . '/models/Attendance.php';
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/Student.php';

class AttendanceController {
    private $attendanceModel;
    private $classModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin', 'teacher']);
        $this->attendanceModel = new Attendance();
        $this->classModel = new SchoolClass();
    }

    public function index() {
        $classes = $this->classModel->getAll();
        $classId = (int)($_GET['class_id'] ?? ($classes[0]['id'] ?? 0));
        $date = sanitize($_GET['date'] ?? date('Y-m-d'));

        $students = $this->attendanceModel->getByClassAndDate($classId, $date);

        $pageTitle = 'Attendance Overview';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/attendance/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function record() {
        $classes = $this->classModel->getAll();
        $classId = (int)($_GET['class_id'] ?? ($classes[0]['id'] ?? 0));
        $date = sanitize($_GET['date'] ?? date('Y-m-d'));

        $students = $this->attendanceModel->getByClassAndDate($classId, $date);

        $pageTitle = 'Record Attendance';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/attendance/record.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('attendance/record');
            }

            $classId = (int)($_POST['class_id'] ?? 0);
            $date = sanitize($_POST['date'] ?? date('Y-m-d'));

            $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
            $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());

            if (!$activeYear || !$activeTerm) {
                setFlash('error', 'No active academic year or term found.');
                redirect('attendance/record');
            }

            $attendanceData = $_POST['attendance'] ?? [];
            $remarksData = $_POST['remarks'] ?? [];

            $records = [];
            foreach ($attendanceData as $studentId => $status) {
                $records[] = [
                    'student_id' => (int)$studentId,
                    'class_id' => $classId,
                    'academic_year_id' => $activeYear['id'],
                    'term_id' => $activeTerm['id'],
                    'date' => $date,
                    'status' => sanitize($status),
                    'remarks' => sanitize($remarksData[$studentId] ?? ''),
                    'recorded_by' => currentUserId()
                ];
            }

            $this->attendanceModel->bulkRecord($records);
            setFlash('success', 'Attendance recorded successfully for class on ' . formatDate($date));
            redirect('attendance?class_id=' . $classId . '&date=' . $date);
        }
    }

    public function report() {
        $classes = $this->classModel->getAll();
        $classId = (int)($_GET['class_id'] ?? ($classes[0]['id'] ?? 0));
        $month = (int)($_GET['month'] ?? date('m'));
        $year = (int)($_GET['year'] ?? date('Y'));

        $studentModel = new Student();
        $students = $studentModel->getByClass($classId);
        $attendance = $this->attendanceModel->getByClass($classId, $month, $year);

        // Map attendance to student -> day
        $matrix = [];
        foreach ($attendance as $record) {
            $day = (int)date('d', strtotime($record['date']));
            $matrix[$record['student_id']][$day] = $record['status'];
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $pageTitle = 'Attendance Report';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/attendance/report.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
}
