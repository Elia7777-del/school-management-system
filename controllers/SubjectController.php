<?php
require_once APP_ROOT . '/models/Subject.php';

class SubjectController {
    private $subjectModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
        $this->subjectModel = new Subject();
    }

    public function index() {
        $level = sanitize($_GET['education_level'] ?? '');
        $subjects = $this->subjectModel->getAll($level);

        $pageTitle = 'Manage Subjects';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/subjects/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function create() {
        $pageTitle = 'Create Subject';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/subjects/create.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('subjects/create');
            }

            $data = [
                'subject_name' => sanitize($_POST['subject_name'] ?? ''),
                'subject_code' => sanitize($_POST['subject_code'] ?? ''),
                'education_level' => sanitize($_POST['education_level'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            if (empty($data['subject_name']) || empty($data['subject_code']) || empty($data['education_level'])) {
                setFlash('error', 'Please fill in all required fields.');
                redirect('subjects/create');
            }

            $this->subjectModel->create($data);
            setFlash('success', 'Subject created successfully.');
            redirect('subjects');
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $subject = $this->subjectModel->findById($id);
        if (!$subject) {
            setFlash('error', 'Subject not found.');
            redirect('subjects');
        }
        $pageTitle = 'Edit Subject';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/subjects/edit.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function update() {
        $id = (int)$_POST['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('subjects/edit?id=' . $id);
            }

            $data = [
                'subject_name' => sanitize($_POST['subject_name'] ?? ''),
                'subject_code' => sanitize($_POST['subject_code'] ?? ''),
                'education_level' => sanitize($_POST['education_level'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            $this->subjectModel->update($id, $data);
            setFlash('success', 'Subject details updated.');
            redirect('subjects');
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $this->subjectModel->delete($id);
        setFlash('success', 'Subject deleted successfully.');
        redirect('subjects');
    }
}
