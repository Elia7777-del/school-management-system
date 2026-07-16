<?php
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/Subject.php';

class ClassController {
    private $classModel;
    private $subjectModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
        $this->classModel = new SchoolClass();
        $this->subjectModel = new Subject();
    }

    public function index() {
        $classes = $this->classModel->getAll();
        // Get student counts for each class
        foreach ($classes as &$class) {
            $class['student_count'] = $this->classModel->getStudentCount($class['id']);
            $class['subjects_count'] = count($this->classModel->getSubjects($class['id']));
        }

        $pageTitle = 'Manage Classes';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/classes/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function create() {
        $subjects = $this->subjectModel->getAll();
        $pageTitle = 'Create Class';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/classes/create.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('classes/create');
            }

            $data = [
                'class_name' => sanitize($_POST['class_name'] ?? ''),
                'education_level' => sanitize($_POST['education_level'] ?? ''),
                'section' => sanitize($_POST['section'] ?? ''),
                'capacity' => (int)($_POST['capacity'] ?? 40)
            ];

            if (empty($data['class_name']) || empty($data['education_level'])) {
                setFlash('error', 'Class name and Education Level are required.');
                redirect('classes/create');
            }

            $classId = $this->classModel->create($data);

            if (!empty($_POST['subjects'])) {
                foreach ($_POST['subjects'] as $subjectId) {
                    $this->classModel->assignSubject($classId, $subjectId);
                }
            }

            setFlash('success', 'Class created successfully.');
            redirect('classes');
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $class = $this->classModel->findById($id);
        if (!$class) {
            setFlash('error', 'Class not found.');
            redirect('classes');
        }
        $subjects = $this->subjectModel->getAll();
        $assignedSubjects = array_column($this->classModel->getSubjects($id), 'id');

        $pageTitle = 'Edit Class';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/classes/edit.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function update() {
        $id = (int)$_POST['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('classes/edit?id=' . $id);
            }

            $data = [
                'class_name' => sanitize($_POST['class_name'] ?? ''),
                'education_level' => sanitize($_POST['education_level'] ?? ''),
                'section' => sanitize($_POST['section'] ?? ''),
                'capacity' => (int)($_POST['capacity'] ?? 40)
            ];

            $this->classModel->update($id, $data);

            // Re-assign subjects
            // Simple delete all then insert new
            $this->db = Database::getInstance()->getConnection();
            $stmt = $this->db->prepare("DELETE FROM class_subjects WHERE class_id = ?");
            $stmt->execute([$id]);

            if (!empty($_POST['subjects'])) {
                foreach ($_POST['subjects'] as $subjectId) {
                    $this->classModel->assignSubject($id, $subjectId);
                }
            }

            setFlash('success', 'Class details updated.');
            redirect('classes');
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $this->classModel->delete($id);
        setFlash('success', 'Class deleted successfully.');
        redirect('classes');
    }
}
