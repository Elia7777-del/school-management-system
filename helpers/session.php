<?php
/**
 * Session Helper
 * 
 * Manages PHP sessions, CSRF tokens, and flash messages.
 * 
 * @package SchoolManagementSystem
 */

/**
 * Start a session if not already started.
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Generate and store a CSRF token in the session.
 *
 * @return string The generated token
 */
function generateCsrfToken(): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

/**
 * Get the current CSRF token, generating one if needed.
 *
 * @return string The CSRF token
 */
function getCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        return generateCsrfToken();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate the submitted CSRF token against the session token.
 *
 * @param string $token Token from form submission
 * @return bool True if valid
 */
function validateCsrfToken(string $token): bool
{
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden input field with the CSRF token.
 *
 * @return string HTML hidden input element
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . getCsrfToken() . '">';
}

/**
 * Set a flash message in the session.
 *
 * @param string $key   Message key (e.g., 'success', 'error', 'warning')
 * @param string $message The message content
 */
function setFlash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get and clear a flash message from the session.
 *
 * @param string $key Message key
 * @return string|null The message, or null if not set
 */
function getFlash(string $key): ?string
{
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Check if a flash message exists.
 *
 * @param string $key Message key
 * @return bool
 */
function hasFlash(string $key): bool
{
    return isset($_SESSION['flash'][$key]);
}

/**
 * Display flash messages as Bootstrap alerts.
 *
 * @return string HTML for all pending flash messages
 */
function displayFlashMessages(): string
{
    $html = '';
    $types = [
        'success' => 'alert-success',
        'error'   => 'alert-danger',
        'warning' => 'alert-warning',
        'info'    => 'alert-info',
    ];

    foreach ($types as $key => $class) {
        $message = getFlash($key);
        if ($message) {
            $html .= '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
            $html .= htmlspecialchars($message);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
        }
    }

    return $html;
}
