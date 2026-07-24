<?php
require_once APP_ROOT . '/models/Invoice.php';
require_once APP_ROOT . '/models/Student.php';

class InvoiceController {
    private $invoiceModel;
    private $studentModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin', 'student', 'parent']);
        $this->invoiceModel = new Invoice();
        $this->studentModel = new Student();
    }

    public function index() {
        requireRole(['super_admin', 'school_admin']);
        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;
        
        $status = $_GET['status'] ?? '';
        $invoices = $this->invoiceModel->getAll($yearId, $status);

        $pageTitle = 'Manage Invoices';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/invoices/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function create() {
        requireRole(['super_admin', 'school_admin']);
        $students = $this->studentModel->getAll('', '', '', 'active', 1, 500);

        $pageTitle = 'Create Invoice';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/invoices/create.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        requireRole(['super_admin', 'school_admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('invoices/create');
            }

            $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
            $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());
            
            $data = [
                'student_id' => (int)$_POST['student_id'],
                'academic_year_id' => $activeYear['id'],
                'term_id' => $activeTerm['id'] ?? null,
                'invoice_number' => $this->invoiceModel->generateInvoiceNumber(),
                'amount' => (float)$_POST['amount'],
                'description' => sanitize($_POST['description'] ?? ''),
                'due_date' => sanitize($_POST['due_date'] ?? ''),
                'created_by' => currentUserId()
            ];

            if ($this->invoiceModel->create($data)) {
                setFlash('success', 'Invoice created successfully. Number: ' . $data['invoice_number']);
            } else {
                setFlash('error', 'Failed to create invoice.');
            }
            redirect('invoices');
        }
    }

    public function myFees() {
        requireRole(['student', 'parent']);
        
        $role = currentUserRole();
        $invoices = [];
        
        if ($role === 'student') {
            $student = $this->studentModel->findByUserId(currentUserId());
            if (!$student) {
                setFlash('error', 'Student profile not found.');
                redirect('dashboard');
            }
            $invoices = $this->invoiceModel->getByStudent($student['id']);
        } elseif ($role === 'parent') {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id FROM parents WHERE user_id = ? AND deleted_at IS NULL");
            $stmt->execute([currentUserId()]);
            $parent = $stmt->fetch();
            
            if (!$parent) {
                setFlash('error', 'Parent profile not found.');
                redirect('dashboard');
            }
            $invoices = $this->invoiceModel->getByParent($parent['id']);
        }

        $pageTitle = 'Fees & Invoices';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/fees/my_fees.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
}
