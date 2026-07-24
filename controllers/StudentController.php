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
        $db = Database::getInstance()->getConnection();
        
        // Fetch parents list for dropdown
        $stmt = $db->query("
            SELECT p.id, p.first_name, p.last_name, p.relationship, p.phone,
                   u.username
            FROM parents p
            JOIN users u ON p.user_id = u.id
            WHERE p.deleted_at IS NULL AND u.deleted_at IS NULL
            ORDER BY p.first_name, p.last_name
        ");
        $parents = $stmt->fetchAll();

        $suggestedAdmNumber = generateAdmissionNumber($db);
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
                'admission_date' => sanitize($_POST['admission_date'] ?? date('Y-m-d'))
            ];

            // Validate
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['gender']) || empty($data['date_of_birth']) || empty($data['class_id']) || empty($data['education_level'])) {
                setFlash('error', 'Please fill in all required fields.');
                setOldInput();
                redirect('students/create');
            }

            $db = Database::getInstance()->getConnection();
            
            // Use admin-provided admission number or auto-generate
            $adminAdmNumber = trim(sanitize($_POST['admission_number'] ?? ''));
            if (!empty($adminAdmNumber)) {
                $data['admission_number'] = $adminAdmNumber;
            } else {
                $data['admission_number'] = generateAdmissionNumber($db);
            }

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
        
        $db = Database::getInstance()->getConnection();
        // Fetch student role users for linking
        $stmt = $db->query("SELECT u.id, u.username, u.email FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name = 'student' AND u.status = 'active' AND u.deleted_at IS NULL ORDER BY u.username");
        $studentUsers = $stmt->fetchAll();

        // Fetch parents list — only from the parents table (these have proper parent IDs for linking)
        $stmt = $db->query("
            SELECT p.id, p.first_name, p.last_name, p.relationship, p.phone,
                   u.username
            FROM parents p
            JOIN users u ON p.user_id = u.id
            WHERE p.deleted_at IS NULL AND u.deleted_at IS NULL
            ORDER BY p.first_name, p.last_name
        ");
        $parents = $stmt->fetchAll();

        // Get currently linked parent ID
        $linkedParents = $this->studentModel->getParents($id);
        $currentParentId = !empty($linkedParents) ? $linkedParents[0]['id'] : null;

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
                'admission_number' => sanitize($_POST['admission_number'] ?? ''),
                'first_name' => sanitize($_POST['first_name'] ?? ''),
                'middle_name' => sanitize($_POST['middle_name'] ?? ''),
                'last_name' => sanitize($_POST['last_name'] ?? ''),
                'gender' => sanitize($_POST['gender'] ?? ''),
                'date_of_birth' => sanitize($_POST['date_of_birth'] ?? ''),
                'class_id' => sanitize($_POST['class_id'] ?? ''),
                'education_level' => sanitize($_POST['education_level'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'active'),
                'user_id' => !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null
            ];

            $this->studentModel->update($id, $data);

            // Update linked parent if selected
            if (isset($_POST['parent_id'])) {
                $parentId = (int)$_POST['parent_id'];
                $db = Database::getInstance()->getConnection();
                
                // Remove existing link
                $stmt = $db->prepare("DELETE FROM student_parents WHERE student_id = ?");
                $stmt->execute([$id]);
                
                if ($parentId > 0) {
                    // Check if parentId exists in parents table
                    $stmt = $db->prepare("SELECT id FROM parents WHERE id = ?");
                    $stmt->execute([$parentId]);
                    $existingParent = $stmt->fetch();

                    if (!$existingParent) {
                        // It might be a user_id of a Parent role user, let's check users table
                        $stmt = $db->prepare("SELECT u.* FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ? AND r.role_name = 'parent'");
                        $stmt->execute([$parentId]);
                        $parentUser = $stmt->fetch();

                        if ($parentUser) {
                            // Auto create parent profile
                            $stmt = $db->prepare("INSERT INTO parents (user_id, first_name, last_name, email, relationship) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([
                                $parentUser['id'],
                                $parentUser['username'],
                                'Parent',
                                $parentUser['email'],
                                'guardian'
                            ]);
                            $parentId = $db->lastInsertId();
                        }
                    }

                    if ($parentId > 0) {
                        $this->studentModel->linkParent($id, $parentId);
                    }
                }
            }

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
