<?php
require_once APP_ROOT . '/models/Exam.php';
require_once APP_ROOT . '/models/ExamResult.php';
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/Student.php';

class ExamController {
    private $examModel;
    private $resultModel;
    private $classModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin', 'teacher']);
        $this->examModel = new Exam();
        $this->resultModel = new ExamResult();
        $this->classModel = new SchoolClass();
    }

    public function index() {
        $classes = $this->classModel->getAll();
        $classId = (int)($_GET['class_id'] ?? ($classes[0]['id'] ?? 0));

        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        $exams = $this->examModel->getAll($classId, $yearId);

        $pageTitle = 'Manage Examinations';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/exams/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function create() {
        $classes = $this->classModel->getAll();
        $pageTitle = 'Create Examination';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/exams/create.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('exams/create');
            }

            $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
            $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());

            $data = [
                'exam_name' => sanitize($_POST['exam_name'] ?? ''),
                'exam_type' => sanitize($_POST['exam_type'] ?? 'midterm'),
                'class_id' => (int)($_POST['class_id'] ?? 0),
                'academic_year_id' => $activeYear['id'] ?? 0,
                'term_id' => $activeTerm['id'] ?? 0,
                'start_date' => sanitize($_POST['start_date'] ?? ''),
                'end_date' => sanitize($_POST['end_date'] ?? ''),
                'status' => 'pending',
                'created_by' => currentUserId()
            ];

            if (empty($data['exam_name']) || empty($data['class_id'])) {
                setFlash('error', 'Exam Name and Class are required.');
                redirect('exams/create');
            }

            $this->examModel->create($data);
            setFlash('success', 'Exam created successfully.');
            redirect('exams');
        }
    }

    public function enterMarks() {
        $id = (int)($_GET['id'] ?? 0);
        $exam = $this->examModel->findById($id);
        if (!$exam) {
            setFlash('error', 'Exam not found.');
            redirect('exams');
        }

        $subjects = $this->classModel->getSubjects($exam['class_id']);
        $subjectId = (int)($_GET['subject_id'] ?? ($subjects[0]['id'] ?? 0));

        $studentModel = new Student();
        $students = $studentModel->getByClass($exam['class_id']);

        // Load existing marks
        $results = $this->resultModel->getByExam($id);
        $existingMarks = [];
        foreach ($results as $res) {
            if ($res['subject_id'] == $subjectId) {
                $existingMarks[$res['student_id']] = $res['marks_obtained'];
            }
        }

        $pageTitle = 'Enter Examination Marks';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/exams/enter_marks.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function storeMarks() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('exams');
            }

            $examId = (int)$_POST['exam_id'];
            $subjectId = (int)$_POST['subject_id'];
            $marks = $_POST['marks'] ?? [];

            $exam = $this->examModel->findById($examId);
            if (!$exam) {
                setFlash('error', 'Exam not found.');
                redirect('exams');
            }

            $this->resultModel->bulkStoreMarks($examId, $subjectId, $marks, $exam['education_level']);
            setFlash('success', 'Marks saved successfully.');
            redirect('exams/results?id=' . $examId);
        }
    }

    public function results() {
        $id = (int)($_GET['id'] ?? 0);
        $exam = $this->examModel->findById($id);
        if (!$exam) {
            setFlash('error', 'Exam not found.');
            redirect('exams');
        }

        $subjects = $this->classModel->getSubjects($exam['class_id']);
        $results = $this->resultModel->getClassResults($id);
        $rawResults = $this->resultModel->getByExam($id);

        // Map marks to student -> subject
        $marksMatrix = [];
        foreach ($rawResults as $r) {
            $marksMatrix[$r['student_id']][$r['subject_id']] = $r['marks_obtained'];
        }

        $pageTitle = 'Examination Results';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/exams/results.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $this->examModel->delete($id);
        setFlash('success', 'Exam deleted successfully.');
        redirect('exams');
    }
}
