<?php
require_once APP_ROOT . '/models/Fee.php';
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/Student.php';

class FeeController {
    private $feeModel;
    private $classModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
        $this->feeModel = new Fee();
        $this->classModel = new SchoolClass();
    }

    public function index() {
        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        $totalCollected = $this->feeModel->getTotalCollected($yearId);
        $totalPending = $this->feeModel->getTotalPending($yearId);
        $recentPayments = $this->feeModel->getAllPayments($yearId, '', 1, 10);

        $pageTitle = 'Fee Overview';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/fees/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function structure() {
        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        $structures = $this->feeModel->getStructures('', $yearId);
        $classes = $this->classModel->getAll();

        $pageTitle = 'Manage Fee Structures';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/fees/structure.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function storeStructure() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('fees/structure');
            }

            $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
            $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());

            $data = [
                'class_id' => (int)$_POST['class_id'],
                'academic_year_id' => $activeYear['id'] ?? 0,
                'term_id' => $activeTerm['id'] ?? null,
                'amount' => (float)$_POST['amount'],
                'description' => sanitize($_POST['description'] ?? '')
            ];

            $this->feeModel->createStructure($data);
            setFlash('success', 'Fee structure created successfully.');
            redirect('fees/structure');
        }
    }

    public function recordPayment() {
        $studentModel = new Student();
        $students = $studentModel->getAll('', '', '', 'active', 1, 100);

        $pageTitle = 'Record Fee Payment';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/fees/record_payment.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function storePayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('fees/record');
            }

            $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
            $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());

            $db = Database::getInstance()->getConnection();
            $receipt = generateReceiptNumber($db);

            $data = [
                'student_id' => (int)$_POST['student_id'],
                'academic_year_id' => $activeYear['id'],
                'term_id' => $activeTerm['id'] ?? null,
                'amount_paid' => (float)$_POST['amount_paid'],
                'payment_date' => sanitize($_POST['payment_date'] ?? date('Y-m-d')),
                'payment_method' => sanitize($_POST['payment_method'] ?? 'cash'),
                'receipt_number' => $receipt,
                'recorded_by' => currentUserId(),
                'remarks' => sanitize($_POST['remarks'] ?? '')
            ];

            $this->feeModel->recordPayment($data);
            setFlash('success', 'Fee payment recorded successfully. Receipt No: ' . $receipt);
            redirect('fees');
        }
    }

    public function report() {
        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        $payments = $this->feeModel->getAllPayments($yearId, '', 1, 100);

        $pageTitle = 'Fee Collection Report';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/fees/report.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
}
