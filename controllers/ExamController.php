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
        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        if (isAdmin()) {
            $classes = $this->classModel->getAll();
        } else {
            require_once APP_ROOT . '/models/Teacher.php';
            $teacherModel = new Teacher();
            $teacher = $teacherModel->findByUserId(currentUserId());
            $teacherId = $teacher['id'] ?? 0;
            
            $teacherClasses = $teacherModel->getClasses($teacherId, $yearId);
            $classes = [];
            foreach ($teacherClasses as $tc) {
                $classes[] = [
                    'id' => $tc['class_id'],
                    'class_name' => $tc['class_name'],
                    'section' => $tc['section']
                ];
            }
        }

        $classId = (int)($_GET['class_id'] ?? ($classes[0]['id'] ?? 0));

        if ($classId > 0) {
            $exams = $this->examModel->getAll($classId, $yearId);
        } else {
            $exams = [];
        }

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

        $allClassSubjects = $this->classModel->getSubjects($exam['class_id']);
        
        if (isAdmin()) {
            $subjects = $allClassSubjects;
        } else {
            // Is Teacher
            require_once APP_ROOT . '/models/Teacher.php';
            $teacherModel = new Teacher();
            $teacher = $teacherModel->findByUserId(currentUserId());
            $teacherId = $teacher['id'] ?? 0;
            
            // Check if teacher is assigned to this class
            $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
            $yearId = $activeYear['id'] ?? 0;
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT is_class_teacher FROM teacher_classes WHERE teacher_id = ? AND class_id = ? AND academic_year_id = ?");
            $stmt->execute([$teacherId, $exam['class_id'], $yearId]);
            $classAssignment = $stmt->fetch();
            
            if ($classAssignment) {
                if ($classAssignment['is_class_teacher'] == 1) {
                    $subjects = $allClassSubjects;
                } else {
                    // Teacher can only see subjects they are explicitly assigned to teach
                    $teacherSubjects = $teacherModel->getSubjects($teacherId);
                    $teacherSubjectIds = array_column($teacherSubjects, 'id');
                    
                    $subjects = [];
                    foreach ($allClassSubjects as $sub) {
                        if (in_array($sub['id'], $teacherSubjectIds)) {
                            $subjects[] = $sub;
                        }
                    }
                }
            } else {
                $subjects = []; // Not assigned to this class at all
            }
        }

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
            logActivity('marks_entry', 'Saved marks for Exam #' . $examId . ', Subject #' . $subjectId . ' (' . count(array_filter($marks, fn($m) => $m !== '' && $m !== null)) . ' students)');
            setFlash('success', 'Marks saved successfully.');
            redirect('exams/results?id=' . $examId);
        }
    }

    public function enterStudentMarks() {
        requireRole(['super_admin', 'school_admin']);

        $id = (int)($_GET['id'] ?? 0);
        $exam = $this->examModel->findById($id);
        if (!$exam) {
            setFlash('error', 'Exam not found.');
            redirect('exams');
        }

        $studentModel = new Student();
        $students = $studentModel->getByClass($exam['class_id']);
        $studentId = (int)($_GET['student_id'] ?? ($students[0]['id'] ?? 0));

        $subjects = $this->classModel->getSubjects($exam['class_id']);

        // Load existing marks for this student
        $results = $this->resultModel->getByStudent($studentId, $id);
        $existingMarks = [];
        foreach ($results as $res) {
            $existingMarks[$res['subject_id']] = $res['marks_obtained'];
        }

        $pageTitle = 'Enter Student Marks';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/exams/enter_student_marks.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function storeStudentMarks() {
        requireRole(['super_admin', 'school_admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('exams');
            }

            $examId = (int)$_POST['exam_id'];
            $studentId = (int)$_POST['student_id'];
            $marks = $_POST['marks'] ?? [];

            $exam = $this->examModel->findById($examId);
            if (!$exam) {
                setFlash('error', 'Exam not found.');
                redirect('exams');
            }

            $this->db = Database::getInstance()->getConnection();
            $this->db->beginTransaction();
            try {
                foreach ($marks as $subjectId => $mark) {
                    if ($mark === '' || $mark === null) {
                        $this->resultModel->deleteMark($examId, $studentId, (int)$subjectId);
                    } else {
                        $this->resultModel->storeMarks($examId, $studentId, (int)$subjectId, (float)$mark, $exam['education_level']);
                    }
                }
                $this->db->commit();
                
                logActivity('marks_entry', 'Admin saved marks for Student #' . $studentId . ' in Exam #' . $examId);
                setFlash('success', 'Student marks saved successfully.');
            } catch (Exception $e) {
                $this->db->rollBack();
                setFlash('error', 'Error saving marks: ' . $e->getMessage());
            }

            redirect('exams/student-marks?id=' . $examId . '&student_id=' . $studentId);
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

    public function studentResults() {
        requireRole('student');
        
        $examResultModel = new ExamResult();
        $studentModel = new Student();
        
        $student = $studentModel->findByUserId(currentUserId());
        if (!$student) {
            setFlash('error', 'Student profile not found.');
            redirect('dashboard');
        }
        
        $results = $examResultModel->getByStudentAll($student['id']);
        
        // Group by exam
        $groupedResults = [];
        foreach ($results as $r) {
            $examKey = $r['year_name'] . ' - ' . $r['term_name'] . ' - ' . $r['exam_name'];
            if (!isset($groupedResults[$examKey])) {
                // Fetch rank using the optimized SQL query we added
                $rankData = $examResultModel->getStudentRank($r['exam_id'], $student['id']);
                
                $groupedResults[$examKey] = [
                    'exam_id' => $r['exam_id'],
                    'exam_name' => $r['exam_name'],
                    'term_name' => $r['term_name'],
                    'year_name' => $r['year_name'],
                    'rank' => $rankData['rank'],
                    'total_students' => $rankData['total_students'],
                    'subjects' => []
                ];
            }
            $groupedResults[$examKey]['subjects'][] = $r;
        }

        $pageTitle = 'My Examination Results';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/exams/student_results.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $this->examModel->delete($id);
        setFlash('success', 'Exam deleted successfully.');
        redirect('exams');
    }
}
