<?php
require_once APP_ROOT . '/models/User.php';
require_once APP_ROOT . '/models/Student.php';
require_once APP_ROOT . '/models/Teacher.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('login');
            }

            $username = sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                setFlash('error', 'Please fill in all fields.');
                setOldInput();
                redirect('login');
            }

            // First, try finding the user by regular username
            $user = $this->userModel->findByUsername($username);

            // If not found, check if the input is an admission number (student login)
            if (!$user) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT user_id FROM students WHERE admission_number = ? AND deleted_at IS NULL");
                $stmt->execute([$username]);
                $studentRecord = $stmt->fetch();

                if ($studentRecord && $studentRecord['user_id']) {
                    $user = $this->userModel->findById($studentRecord['user_id']);
                }
            }

            // If still not found, check if a PARENT is trying to log in using their child's admission number
            // In this case the password must match the parent's account password
            if (!$user) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare(
                    "SELECT u.* , r.role_name
                     FROM students s
                     JOIN student_parents sp ON s.id = sp.student_id
                     JOIN parents p ON sp.parent_id = p.id
                     JOIN users u ON p.user_id = u.id
                     JOIN roles r ON u.role_id = r.id
                     WHERE s.admission_number = ? AND s.deleted_at IS NULL AND u.status = 'active'
                     LIMIT 1"
                );
                $stmt->execute([$username]);
                $parentUser = $stmt->fetch();
                if ($parentUser) {
                    $user = $parentUser;
                }
            }

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    setFlash('error', 'Your account has been deactivated. Contact your administrator.');
                    redirect('login');
                }

                $db = Database::getInstance()->getConnection();
                $school = null;

                // For non-system-admin users, load school and check subscription
                if (!empty($user['school_id'])) {
                    $stmt = $db->prepare("SELECT * FROM schools WHERE id = ?");
                    $stmt->execute([$user['school_id']]);
                    $school = $stmt->fetch();

                    if (!$school || $school['status'] !== 'active') {
                        setFlash('error', 'Your school account is suspended or inactive. Please contact the platform administrator.');
                        redirect('login');
                    }

                    // Check active subscription
                    $subStmt = $db->prepare("
                        SELECT * FROM school_subscriptions 
                        WHERE school_id = ? AND end_date >= CURDATE() 
                        ORDER BY end_date DESC LIMIT 1
                    ");
                    $subStmt->execute([$user['school_id']]);
                    $subscription = $subStmt->fetch();

                    if (!$subscription) {
                        setFlash('error', 'Your school\'s subscription has expired. Please contact the platform administrator.');
                        redirect('login');
                    }
                }

                $this->userModel->updateLastLogin($user['id']);
                setUserSession($user, $school);
                clearOldInput();
                logActivity('login', 'User logged in: ' . $user['username'] . ' (Role: ' . ($user['role_name'] ?? '') . ')');

                // System admin goes to schools management page
                if (($user['role_name'] ?? '') === 'system_admin') {
                    redirect('schools');
                }
                redirect('dashboard');
            } else {
                setFlash('error', 'Invalid username or password.');
                setOldInput();
                redirect('login');
            }
        }

        $pageTitle = 'Login';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/auth/login.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function logout() {
        logActivity('logout', 'User logged out: ' . (currentUser()['username'] ?? 'unknown'));
        destroyUserSession();
        redirect('login');
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('forgot-password');
            }
            // Simple MVP implementation: Just log warning and direct to administrator
            setFlash('info', 'Password reset request submitted. Please contact the School Administrator to reset your password.');
            redirect('login');
        }
        $pageTitle = 'Forgot Password';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/auth/forgot_password.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function changePassword() {
        requireLogin();
        $pageTitle = 'Change Password';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/auth/change_password.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    public function updatePassword() {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'CSRF validation failed.');
                redirect('change-password');
            }

            $oldPassword = $_POST['old_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
                setFlash('error', 'All fields are required.');
                redirect('change-password');
            }

            if ($newPassword !== $confirmPassword) {
                setFlash('error', 'New passwords do not match.');
                redirect('change-password');
            }

            $user = $this->userModel->findById(currentUserId());
            if ($user && password_verify($oldPassword, $user['password'])) {
                $this->userModel->changePassword(currentUserId(), $newPassword);
                setFlash('success', 'Password updated successfully.');
                redirect('dashboard');
            } else {
                setFlash('error', 'Incorrect current password.');
                redirect('change-password');
            }
        }
    }

    public function profile() {
        requireLogin();
        $user = $this->userModel->findById(currentUserId());
        $profile = null;
        if ($user['role_name'] === 'student') {
            $studentModel = new Student();
            $profile = $studentModel->findByUserId(currentUserId());
        } elseif ($user['role_name'] === 'teacher') {
            $teacherModel = new Teacher();
            $profile = $teacherModel->findByUserId(currentUserId());
        }

        $pageTitle = 'My Profile';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/auth/profile.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
}
