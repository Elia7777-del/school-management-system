<?php
/**
 * Front Controller
 * 
 * Entry point for all HTTP requests. Parses the URL,
 * matches it to a route, and dispatches to the appropriate controller.
 * 
 * @package SchoolManagementSystem
 */

// ─── Bootstrap ──────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/session.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';

// Start the session
startSession();

// ─── Load Routes ────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/routes.php';

// ─── Parse URL ──────────────────────────────────────────────────
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url = filter_var($url, FILTER_SANITIZE_URL);

// ─── Route Matching ─────────────────────────────────────────────
try {
    if (array_key_exists($url, $routes)) {
        [$controllerName, $method] = $routes[$url];

        $controllerFile = APP_ROOT . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: {$controllerName}");
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            throw new Exception("Controller class not found: {$controllerName}");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            throw new Exception("Method not found: {$controllerName}::{$method}");
        }

        // Call the controller method
        $controller->$method();
    } else {
        // 404 — Page Not Found
        http_response_code(404);
        require_once APP_ROOT . '/views/layouts/header.php';
        echo '<div class="container-fluid py-4">';
        echo '<div class="row justify-content-center">';
        echo '<div class="col-md-6 text-center">';
        echo '<div class="card shadow-sm border-0">';
        echo '<div class="card-body py-5">';
        echo '<h1 class="display-1 text-muted">404</h1>';
        echo '<h3 class="mb-3">Page Not Found</h3>';
        echo '<p class="text-muted mb-4">The page you are looking for does not exist.</p>';
        echo '<a href="' . BASE_URL . '/dashboard" class="btn btn-primary"><i class="bi bi-house-door"></i> Back to Dashboard</a>';
        echo '</div></div></div></div></div>';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
} catch (Exception $e) {
    // ─── Error Handler ──────────────────────────────────────────
    http_response_code(500);
    error_log("Application Error: " . $e->getMessage());

    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<div style="padding:20px;font-family:monospace;">';
        echo '<h2>Application Error</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        require_once APP_ROOT . '/views/layouts/header.php';
        echo '<div class="container-fluid py-4">';
        echo '<div class="row justify-content-center">';
        echo '<div class="col-md-6 text-center">';
        echo '<div class="card shadow-sm border-0">';
        echo '<div class="card-body py-5">';
        echo '<h1 class="display-1 text-muted">500</h1>';
        echo '<h3 class="mb-3">Internal Server Error</h3>';
        echo '<p class="text-muted mb-4">Something went wrong. Please try again later.</p>';
        echo '<a href="' . BASE_URL . '/dashboard" class="btn btn-primary"><i class="bi bi-house-door"></i> Back to Dashboard</a>';
        echo '</div></div></div></div></div>';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
}
