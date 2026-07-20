<?php
require_once APP_ROOT . '/models/Teacher.php';
require_once APP_ROOT . '/models/Subject.php';
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/User.php';

class TeacherController {
    private $teacherModel;
    private $subjectModel;
    private $classModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
        $this->teacherModel = new Teacher();
        $this->subjectModel = new Subject();
        $this->classModel = new SchoolClass();
    }

    public function index() {
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $page = (int)($_GET['page'] ?? 1);

        $total = $this->teacherModel->getCount($search, $status);
        $pagination = paginate($total, ITEMS_PER_PAGE, $page);

        $teachers = $this->teacherModel->getAll($search, $status, $pagination['currentPage'], ITEMS_PER_PAGE);

        $pageTitle = 'Manage Teachers';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/teachers/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function create() {
        $subjects = $this->subjectModel->getAll();
        $classes = $this->classModel->getAll();
        
        $userModel = new User();
        $allUsers = $userModel->getAll('', 1, 100);

        $pageTitle = 'Add Teacher';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/teachers/create.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('teachers/create');
            }

            $data = [
                'first_name' => sanitize($_POST['first_name'] ?? ''),
                'last_name' => sanitize($_POST['last_name'] ?? ''),
                'gender' => sanitize($_POST['gender'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'qualification' => sanitize($_POST['qualification'] ?? ''),
                'specialization' => sanitize($_POST['specialization'] ?? ''),
                'join_date' => sanitize($_POST['join_date'] ?? date('Y-m-d')),
                'user_id' => !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null
            ];

            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['phone'])) {
                setFlash('error', 'Please fill in all required fields.');
                setOldInput();
                redirect('teachers/create');
            }

            $db = Database::getInstance()->getConnection();
            $data['teacher_number'] = generateTeacherNumber($db);

            // User Account creation if requested
            if (!empty($_POST['create_account'])) {
                $username = sanitize($_POST['username'] ?? '');
                $email = sanitize($_POST['email'] ?? '');
                $password = $_POST['password'] ?? 'Teacher@123';

                if (empty($username) || empty($email)) {
                    setFlash('error', 'Username and email are required for account creation.');
                    setOldInput();
                    redirect('teachers/create');
                }

                $userModel = new User();
                $userId = $userModel->create([
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => 3, // Teacher Role ID
                    'status' => 'active'
                ]);
                $data['user_id'] = $userId;
            }

            $teacherId = $this->teacherModel->create($data);

            // Assign subjects
            if (!empty($_POST['subjects'])) {
                foreach ($_POST['subjects'] as $subjectId) {
                    $this->teacherModel->assignSubject($teacherId, $subjectId);
                }
            }

            // Assign classes
            $activeYear = getActiveAcademicYear($db);
            $yearId = $activeYear['id'] ?? 0;
            if ($yearId > 0 && !empty($_POST['classes'])) {
                foreach ($_POST['classes'] as $classId) {
                    $isClassTeacher = !empty($_POST['class_teacher'][$classId]) ? 1 : 0;
                    $this->teacherModel->assignClass($teacherId, $classId, $yearId, $isClassTeacher);
                }
            }

            setFlash('success', 'Teacher added successfully. TSC Number: ' . $data['teacher_number']);
            clearOldInput();
            redirect('teachers');
        }
    }

    public function show() {
        $id = (int)($_GET['id'] ?? 0);
        $teacher = $this->teacherModel->findById($id);
        if (!$teacher) {
            setFlash('error', 'Teacher not found.');
            redirect('teachers');
        }

        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        $subjects = $this->teacherModel->getSubjects($id);
        $classes = $this->teacherModel->getClasses($id, $yearId);

        $pageTitle = 'Teacher Profile - ' . $teacher['first_name'] . ' ' . $teacher['last_name'];
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/teachers/show.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $teacher = $this->teacherModel->findById($id);
        if (!$teacher) {
            setFlash('error', 'Teacher not found.');
            redirect('teachers');
        }
        
        $activeYear = getActiveAcademicYear(Database::getInstance()->getConnection());
        $yearId = $activeYear['id'] ?? 0;

        $allSubjects = $this->subjectModel->getAll();
        $allClasses = $this->classModel->getAll();
        
        $teacherSubjects = $this->teacherModel->getSubjects($id);
        $assignedSubjectIds = array_column($teacherSubjects, 'id');
        
        $teacherClasses = $this->teacherModel->getClasses($id, $yearId);
        // Create an associative array for easy checking: class_id => is_class_teacher
        $assignedClasses = [];
        foreach ($teacherClasses as $tc) {
            $assignedClasses[$tc['class_id']] = $tc['is_class_teacher'];
        }
        
        $userModel = new User();
        $allUsers = $userModel->getAll('', 1, 100);
        
        $pageTitle = 'Edit Teacher';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/teachers/edit.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function update() {
        $id = (int)$_POST['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('teachers/edit?id=' . $id);
            }

            $data = [
                'first_name' => sanitize($_POST['first_name'] ?? ''),
                'last_name' => sanitize($_POST['last_name'] ?? ''),
                'gender' => sanitize($_POST['gender'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'qualification' => sanitize($_POST['qualification'] ?? ''),
                'specialization' => sanitize($_POST['specialization'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'active'),
                'user_id' => !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null
            ];

            $this->teacherModel->update($id, $data);
            
            // Update subjects
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM teacher_subjects WHERE teacher_id = ?");
            $stmt->execute([$id]);
            if (!empty($_POST['subjects'])) {
                foreach ($_POST['subjects'] as $subjectId) {
                    $this->teacherModel->assignSubject($id, $subjectId);
                }
            }

            // Update classes
            $activeYear = getActiveAcademicYear($db);
            $yearId = $activeYear['id'] ?? 0;
            if ($yearId > 0) {
                // Remove existing class assignments for this academic year
                $stmt = $db->prepare("DELETE FROM teacher_classes WHERE teacher_id = ? AND academic_year_id = ?");
                $stmt->execute([$id, $yearId]);
                
                // Assign new ones
                if (!empty($_POST['classes'])) {
                    foreach ($_POST['classes'] as $classId) {
                        // Check if this class is selected as class teacher
                        $isClassTeacher = !empty($_POST['class_teacher'][$classId]) ? 1 : 0;
                        $this->teacherModel->assignClass($id, $classId, $yearId, $isClassTeacher);
                    }
                }
            }
            
            setFlash('success', 'Teacher details updated.');
            redirect('teachers');
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $this->teacherModel->delete($id);
        setFlash('success', 'Teacher soft-deleted successfully.');
        redirect('teachers');
    }
}
