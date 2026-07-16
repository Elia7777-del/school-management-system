<?php
/**
 * Authentication Helper
 * 
 * Provides authentication guards and role-checking utilities.
 * 
 * @package SchoolManagementSystem
 */

/**
 * Check if a user is currently logged in.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require user to be logged in. Redirects to login page if not.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please login to access this page.');
        redirect('login');
        exit;
    }
}

/**
 * Require user to have a specific role. Redirects to dashboard if unauthorized.
 *
 * @param string|array $roles Allowed role(s)
 */
function requireRole($roles): void
{
    requireLogin();

    if (is_string($roles)) {
        $roles = [$roles];
    }

    if (!in_array(currentUserRole(), $roles)) {
        setFlash('error', 'You do not have permission to access this page.');
        redirect('dashboard');
        exit;
    }
}

/**
 * Get the current logged-in user data from session.
 *
 * @return array|null User data array or null
 */
function currentUser(): ?array
{
    if (isLoggedIn()) {
        return [
            'id'       => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
            'email'    => $_SESSION['email'] ?? '',
            'role'     => $_SESSION['role'] ?? '',
            'role_id'  => $_SESSION['role_id'] ?? 0,
        ];
    }
    return null;
}

/**
 * Get the current user's ID.
 *
 * @return int|null
 */
function currentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get the current user's role name.
 *
 * @return string|null
 */
function currentUserRole(): ?string
{
    return $_SESSION['role'] ?? null;
}

/**
 * Check if the current user has a given role.
 *
 * @param string $role Role name to check
 * @return bool
 */
function hasRole(string $role): bool
{
    return currentUserRole() === $role;
}

/**
 * Check if current user is any kind of admin.
 *
 * @return bool
 */
function isAdmin(): bool
{
    return in_array(currentUserRole(), ['super_admin', 'school_admin']);
}

/**
 * Set user session data on login.
 *
 * @param array $user User record from database
 */
function setUserSession(array $user): void
{
    session_regenerate_id(true); // Prevent session fixation
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email']    = $user['email'];
    $_SESSION['role']     = $user['role_name'];
    $_SESSION['role_id']  = $user['role_id'];
}

/**
 * Destroy user session on logout.
 */
function destroyUserSession(): void
{
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
