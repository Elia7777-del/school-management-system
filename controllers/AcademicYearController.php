<?php
require_once APP_ROOT . '/models/AcademicYear.php';

class AcademicYearController {
    private $academicModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
        $this->academicModel = new AcademicYear();
    }

    public function index() {
        $years = $this->academicModel->getAll();
        $pageTitle = 'Academic Calendar';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/academic/years.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('academic-years');
            }

            $data = [
                'year_name' => sanitize($_POST['year_name']),
                'start_date' => sanitize($_POST['start_date']),
                'end_date' => sanitize($_POST['end_date']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $this->academicModel->create($data);
            setFlash('success', 'Academic Year created.');
            redirect('academic-years');
        }
    }

    public function activate() {
        $id = (int)($_GET['id'] ?? 0);
        $this->academicModel->activate($id);
        setFlash('success', 'Academic Year activated.');
        redirect('academic-years');
    }

    public function terms() {
        $yearId = (int)$_GET['year_id'];
        $year = $this->academicModel->findById($yearId);
        $terms = $this->academicModel->getTerms($yearId);

        $pageTitle = 'Manage Terms - ' . $year['year_name'];
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/academic/terms.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function storeTerm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yearId = (int)$_POST['academic_year_id'];
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('academic-years/terms?year_id=' . $yearId);
            }

            $data = [
                'academic_year_id' => $yearId,
                'term_name' => sanitize($_POST['term_name']),
                'start_date' => sanitize($_POST['start_date']),
                'end_date' => sanitize($_POST['end_date']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $this->academicModel->createTerm($data);
            setFlash('success', 'School Term created.');
            redirect('academic-years/terms?year_id=' . $yearId);
        }
    }

    public function activateTerm() {
        $id = (int)$_GET['id'];
        $yearId = (int)$_GET['year_id'];
        $this->academicModel->activateTerm($id, $yearId);
        setFlash('success', 'Term activated.');
        redirect('academic-years/terms?year_id=' . $yearId);
    }
}
