<?php
require_once APP_ROOT . '/models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        requireRole('super_admin');
        $this->userModel = new User();
    }

    public function index() {
        $search = sanitize($_GET['search'] ?? '');
        $page = (int)($_GET['page'] ?? 1);

        $total = $this->userModel->getCount($search);
        $pagination = paginate($total, ITEMS_PER_PAGE, $page);

        $users = $this->userModel->getAll($search, $pagination['currentPage'], ITEMS_PER_PAGE);

        $db = Database::getInstance()->getConnection();
        $roles = $db->query("SELECT * FROM roles")->fetchAll();

        $pageTitle = 'Manage System Users';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/users/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('users');
            }

            $data = [
                'username' => sanitize($_POST['username'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role_id' => (int)($_POST['role_id'] ?? 0),
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role_id'])) {
                setFlash('error', 'Please fill in all required fields.');
                redirect('users');
            }

            $userId = $this->userModel->create($data);

            // If the role is "parent", also create a record in the parents table
            $db = Database::getInstance()->getConnection();
            $roleStmt = $db->prepare("SELECT role_name FROM roles WHERE id = ?");
            $roleStmt->execute([$data['role_id']]);
            $role = $roleStmt->fetch();

            if ($role && $role['role_name'] === 'parent') {
                // Extract first/last name from username
                $nameParts = explode(' ', $data['username'], 2);
                $firstName = ucfirst($nameParts[0]);
                $lastName  = isset($nameParts[1]) ? ucfirst($nameParts[1]) : '';
                $schoolId  = currentSchoolId() ?? 1;

                // Check if parents record already exists for this user
                $checkStmt = $db->prepare("SELECT id FROM parents WHERE user_id = ?");
                $checkStmt->execute([$userId]);
                if (!$checkStmt->fetch()) {
                    $insertStmt = $db->prepare("
                        INSERT INTO parents (user_id, first_name, last_name, phone, email, relationship, school_id)
                        VALUES (?, ?, ?, '', ?, 'guardian', ?)
                    ");
                    $insertStmt->execute([$userId, $firstName, $lastName, $data['email'], $schoolId]);
                }
            }

            setFlash('success', 'User account created successfully.');
            redirect('users');
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'User not found.');
            redirect('users');
        }

        $db = Database::getInstance()->getConnection();
        $roles = $db->query("SELECT * FROM roles")->fetchAll();

        $pageTitle = 'Edit User Account';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/users/edit.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function update() {
        $id = (int)$_POST['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('users/edit?id=' . $id);
            }

            $data = [
                'username' => sanitize($_POST['username'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'role_id' => (int)($_POST['role_id'] ?? 0),
                'status' => sanitize($_POST['status'] ?? 'active'),
                'password' => $_POST['password'] ?? ''
            ];

            $this->userModel->update($id, $data);
            setFlash('success', 'User details updated.');
            redirect('users');
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $this->userModel->delete($id);
        setFlash('success', 'User account deactivated (soft-deleted).');
        redirect('users');
    }
}
