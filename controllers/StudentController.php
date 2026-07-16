<?php
require_once APP_ROOT . '/models/Student.php';
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/User.php';
require_once APP_ROOT . '/models/Attendance.php';
require_once APP_ROOT . '/models/ExamResult.php';
require_once APP_ROOT . '/models/Fee.php';

class StudentController {
    private $studentModel;
    private $classModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
        $this->studentModel = new Student();
        $this->classModel = new SchoolClass();
    }

    public function index() {
        $search = sanitize($_GET['search'] ?? '');
        $classFilter = sanitize($_GET['class_id'] ?? '');
        $levelFilter = sanitize($_GET['education_level'] ?? '');
        $statusFilter = sanitize($_GET['status'] ?? '');
        $page = (int)($_GET['page'] ?? 1);

        $total = $this->studentModel->getCount($search, $classFilter, $levelFilter, $statusFilter);
        $pagination = paginate($total, ITEMS_PER_PAGE, $page);

        $students = $this->studentModel->getAll($search, $classFilter, $levelFilter, $statusFilter, $pagination['currentPage'], ITEMS_PER_PAGE);
        $classes = $this->classModel->getAll();

        $pageTitle = 'Manage Students';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/students/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function create() {
        $classes = $this->classModel->getAll();
        $pageTitle = 'Add Student';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/students/create.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('students/create');
            }

            $data = [
                'first_name' => sanitize($_POST['first_name'] ?? ''),
                'middle_name' => sanitize($_POST['middle_name'] ?? ''),
                'last_name' => sanitize($_POST['last_name'] ?? ''),
                'gender' => sanitize($_POST['gender'] ?? ''),
                'date_of_birth' => sanitize($_POST['date_of_birth'] ?? ''),
                'class_id' => sanitize($_POST['class_id'] ?? ''),
                'education_level' => sanitize($_POST['education_level'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'admission_date' => date('Y-m-d')
            ];

            // Validate
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['gender']) || empty($data['date_of_birth']) || empty($data['class_id']) || empty($data['education_level'])) {
                setFlash('error', 'Please fill in all required fields.');
                setOldInput();
                redirect('students/create');
            }

            $db = Database::getInstance()->getConnection();
            $data['admission_number'] = generateAdmissionNumber($db);

            // Create User Account if requested
            if (!empty($_POST['create_account'])) {
                $username = sanitize($_POST['username'] ?? '');
                $email = sanitize($_POST['email'] ?? '');
                $password = $_POST['password'] ?? 'Student@123';

                if (empty($username) || empty($email)) {
                    setFlash('error', 'Username and email are required for account creation.');
                    setOldInput();
                    redirect('students/create');
                }

                $userModel = new User();
                $userId = $userModel->create([
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => 4, // Student Role ID
                    'status' => 'active'
                ]);
                $data['user_id'] = $userId;
            }

            $studentId = $this->studentModel->create($data);

            // Link parent if selected
            if (!empty($_POST['parent_id'])) {
                $this->studentModel->linkParent($studentId, $_POST['parent_id']);
            }

            setFlash('success', 'Student added successfully. Admission Number: ' . $data['admission_number']);
            clearOldInput();
            redirect('students');
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->studentModel->findById($id);
        if (!$student) {
            setFlash('error', 'Student not found.');
            redirect('students');
        }
        $classes = $this->classModel->getAll();
        $pageTitle = 'Edit Student';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/students/edit.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function update() {
        $id = (int)($_POST['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('students/edit?id=' . $id);
            }

            $data = [
                'first_name' => sanitize($_POST['first_name'] ?? ''),
                'middle_name' => sanitize($_POST['middle_name'] ?? ''),
                'last_name' => sanitize($_POST['last_name'] ?? ''),
                'gender' => sanitize($_POST['gender'] ?? ''),
                'date_of_birth' => sanitize($_POST['date_of_birth'] ?? ''),
                'class_id' => sanitize($_POST['class_id'] ?? ''),
                'education_level' => sanitize($_POST['education_level'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            $this->studentModel->update($id, $data);
            setFlash('success', 'Student updated successfully.');
            redirect('students');
        }
    }

    public function show() {
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->studentModel->findById($id);
        if (!$student) {
            setFlash('error', 'Student not found.');
            redirect('students');
        }

        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $activeTerm = getActiveSchoolTerm(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;
        $termId = $activeTerm['id'] ?? 0;

        $attendanceModel = new Attendance();
        $examResultModel = new ExamResult();
        $feeModel = new Fee();

        $attendance = $attendanceModel->getStudentAttendanceSummary($id, $termId);
        $results = $examResultModel->getByStudentAll($id);
        $fees = $feeModel->getPayments($id, $yearId);
        $parent = $this->studentModel->getParents($id)[0] ?? null;

        $pageTitle = 'Student Details - ' . $student['first_name'] . ' ' . $student['last_name'];
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/students/show.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $this->studentModel->delete($id);
        setFlash('success', 'Student soft-deleted successfully.');
        redirect('students');
    }
}
