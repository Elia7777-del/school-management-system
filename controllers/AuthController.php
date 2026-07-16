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

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    setFlash('error', 'Your account has been deactivated.');
                    redirect('login');
                }

                $this->userModel->updateLastLogin($user['id']);
                setUserSession($user);
                clearOldInput();
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
