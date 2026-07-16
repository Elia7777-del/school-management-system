<?php
require_once APP_ROOT . '/models/Timetable.php';
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/Subject.php';
require_once APP_ROOT . '/models/Teacher.php';

class TimetableController {
    private $timetableModel;
    private $classModel;

    public function __construct() {
        $this->timetableModel = new Timetable();
        $this->classModel = new SchoolClass();
    }

    public function index() {
        requireRole(['super_admin', 'school_admin', 'teacher', 'student']);
        $classes = $this->classModel->getAll();
        $classId = (int)($_GET['class_id'] ?? ($classes[0]['id'] ?? 0));

        $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());
        $termId = $activeTerm['id'] ?? 0;

        $timetable = $this->timetableModel->getByClass($classId, $termId);

        // Group by day
        $grouped = [];
        foreach ($timetable as $entry) {
            $grouped[$entry['day_of_week']][] = $entry;
        }

        $pageTitle = 'Class Timetable';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/timetable/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function manage() {
        requireRole(['super_admin', 'school_admin']);
        $classes = $this->classModel->getAll();
        $classId = (int)($_GET['class_id'] ?? ($classes[0]['id'] ?? 0));

        $subjects = $this->classModel->getSubjects($classId);
        $teacherModel = new Teacher();
        $teachers = $teacherModel->getAll('', 'active', 1, 100);

        $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());
        $termId = $activeTerm['id'] ?? 0;

        $timetable = $this->timetableModel->getByClass($classId, $termId);

        $pageTitle = 'Manage Timetable';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/timetable/manage.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        requireRole(['super_admin', 'school_admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('timetable/manage');
            }

            $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
            $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());

            $data = [
                'class_id' => (int)$_POST['class_id'],
                'subject_id' => (int)$_POST['subject_id'],
                'teacher_id' => (int)$_POST['teacher_id'],
                'academic_year_id' => $activeYear['id'],
                'term_id' => $activeTerm['id'],
                'day_of_week' => sanitize($_POST['day_of_week']),
                'start_time' => sanitize($_POST['start_time']),
                'end_time' => sanitize($_POST['end_time']),
                'room' => sanitize($_POST['room'] ?? '')
            ];

            $this->timetableModel->create($data);
            setFlash('success', 'Timetable slot added successfully.');
            redirect('timetable/manage?class_id=' . $data['class_id']);
        }
    }

    public function delete() {
        requireRole(['super_admin', 'school_admin']);
        $id = (int)($_GET['id'] ?? 0);
        $classId = (int)($_GET['class_id'] ?? 0);
        $this->timetableModel->delete($id);
        setFlash('success', 'Timetable slot removed successfully.');
        redirect('timetable/manage?class_id=' . $classId);
    }
}
