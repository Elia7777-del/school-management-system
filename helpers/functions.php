<?php
/**
 * Utility Functions
 * 
 * General-purpose helper functions used across the application.
 * 
 * @package SchoolManagementSystem
 */

/**
 * Redirect to a URL path (relative to BASE_URL).
 *
 * @param string $path URL path (e.g., 'dashboard', 'students/create')
 */
function redirect(string $path): void
{
    header("Location: " . BASE_URL . "/" . ltrim($path, '/'));
    exit;
}

/**
 * Sanitize user input — trim and escape HTML.
 *
 * @param mixed $data Input data
 * @return string Sanitized string
 */
function sanitize($data): string
{
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Get old input value from session for form re-population.
 *
 * @param string $key   Form field name
 * @param string $default Default value
 * @return string
 */
function old(string $key, string $default = ''): string
{
    $value = $_SESSION['old_input'][$key] ?? $default;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Store POST data in session for form re-population on validation failure.
 */
function setOldInput(): void
{
    $_SESSION['old_input'] = $_POST;
}

/**
 * Clear old input data from session.
 */
function clearOldInput(): void
{
    unset($_SESSION['old_input']);
}

/**
 * Format a date string.
 *
 * @param string $date   Date string
 * @param string $format Desired format (default: 'd M Y')
 * @return string Formatted date
 */
function formatDate(?string $date, string $format = 'd M Y'): string
{
    if (empty($date)) return 'N/A';
    return date($format, strtotime($date));
}

/**
 * Format a datetime string.
 *
 * @param string $datetime Datetime string
 * @return string Formatted datetime
 */
function formatDateTime(?string $datetime): string
{
    if (empty($datetime)) return 'N/A';
    return date('d M Y, h:i A', strtotime($datetime));
}

/**
 * Generate a unique admission number.
 * Format: ADM-YYYY-NNN
 *
 * @param PDO $db Database connection
 * @return string
 */
function generateAdmissionNumber(PDO $db): string
{
    $year = date('Y');
    $stmt = $db->query("SELECT COUNT(*) as count FROM students WHERE YEAR(created_at) = $year");
    $count = $stmt->fetch()['count'] + 1;
    return 'ADM-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
}

/**
 * Generate a unique teacher number.
 * Format: TSC-NNN
 *
 * @param PDO $db Database connection
 * @return string
 */
function generateTeacherNumber(PDO $db): string
{
    $stmt = $db->query("SELECT COUNT(*) as count FROM teachers");
    $count = $stmt->fetch()['count'] + 1;
    return 'TSC-' . str_pad($count, 3, '0', STR_PAD_LEFT);
}

/**
 * Generate a unique receipt number.
 * Format: RCP-YYYYMMDD-NNN
 *
 * @param PDO $db Database connection
 * @return string
 */
function generateReceiptNumber(PDO $db): string
{
    $date = date('Ymd');
    $stmt = $db->query("SELECT COUNT(*) as count FROM fee_payments WHERE DATE(created_at) = CURDATE()");
    $count = $stmt->fetch()['count'] + 1;
    return 'RCP-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
}

/**
 * Calculate pagination parameters.
 *
 * @param int $total       Total number of records
 * @param int $perPage     Records per page
 * @param int $currentPage Current page number
 * @return array ['offset', 'totalPages', 'currentPage', 'perPage', 'total']
 */
function paginate(int $total, int $perPage, int $currentPage): array
{
    $totalPages = max(1, ceil($total / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'offset'      => $offset,
        'totalPages'  => $totalPages,
        'currentPage' => $currentPage,
        'perPage'     => $perPage,
        'total'       => $total,
    ];
}

/**
 * Generate Bootstrap 5 pagination HTML.
 *
 * @param int    $totalPages  Total number of pages
 * @param int    $currentPage Current page
 * @param string $baseUrl     Base URL for pagination links
 * @return string HTML string
 */
function getPaginationHtml(int $totalPages, int $currentPage, string $baseUrl): string
{
    if ($totalPages <= 1) return '';

    // Ensure baseUrl has proper query string separator
    $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    // Previous button
    $prevDisabled = ($currentPage <= 1) ? 'disabled' : '';
    $prevPage = max(1, $currentPage - 1);
    $html .= "<li class=\"page-item {$prevDisabled}\"><a class=\"page-link\" href=\"{$baseUrl}{$separator}page={$prevPage}\"><i class=\"bi bi-chevron-left\"></i></a></li>";

    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);

    if ($startPage > 1) {
        $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$baseUrl}{$separator}page=1\">1</a></li>";
        if ($startPage > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        $active = ($i === $currentPage) ? 'active' : '';
        $html .= "<li class=\"page-item {$active}\"><a class=\"page-link\" href=\"{$baseUrl}{$separator}page={$i}\">{$i}</a></li>";
    }

    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$baseUrl}{$separator}page={$totalPages}\">{$totalPages}</a></li>";
    }

    // Next button
    $nextDisabled = ($currentPage >= $totalPages) ? 'disabled' : '';
    $nextPage = min($totalPages, $currentPage + 1);
    $html .= "<li class=\"page-item {$nextDisabled}\"><a class=\"page-link\" href=\"{$baseUrl}{$separator}page={$nextPage}\"><i class=\"bi bi-chevron-right\"></i></a></li>";

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Check if current URL matches the given path — used for active menu highlighting.
 *
 * @param string $path Path to check
 * @return string 'active' if matches, empty string otherwise
 */
function isActiveMenu(string $path): string
{
    $currentUrl = $_GET['url'] ?? '';
    // Exact match or starts with the path
    if ($currentUrl === $path || strpos($currentUrl, $path . '/') === 0) {
        return 'active';
    }
    return '';
}

/**
 * Get education level options.
 *
 * @return array
 */
function getEducationLevels(): array
{
    return [
        'primary'   => 'Primary Education (Std I-VII)',
        'secondary' => 'Secondary Education (Form I-IV)',
    ];
}

/**
 * Get days of the week for timetable.
 *
 * @return array
 */
function getDaysOfWeek(): array
{
    return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
}

/**
 * Get a Bootstrap badge for a given status.
 *
 * @param string $status Status value
 * @return string HTML badge
 */
function getStatusBadge(string $status): string
{
    $badges = [
        'active'      => '<span class="badge bg-success">Active</span>',
        'inactive'    => '<span class="badge bg-secondary">Inactive</span>',
        'graduated'   => '<span class="badge bg-info">Graduated</span>',
        'transferred' => '<span class="badge bg-warning text-dark">Transferred</span>',
        'present'     => '<span class="badge bg-success">Present</span>',
        'absent'      => '<span class="badge bg-danger">Absent</span>',
        'late'        => '<span class="badge bg-warning text-dark">Late</span>',
        'pending'     => '<span class="badge bg-warning text-dark">Pending</span>',
        'ongoing'     => '<span class="badge bg-primary">Ongoing</span>',
        'completed'   => '<span class="badge bg-success">Completed</span>',
    ];

    return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
}

/**
 * Format currency amount (Tanzanian Shilling).
 *
 * @param float $amount Amount
 * @return string Formatted amount
 */
function formatCurrency(float $amount): string
{
    return 'TZS ' . number_format($amount, 2);
}

/**
 * Get the current academic year from database.
 *
 * @param PDO $db Database connection
 * @return array|null Active academic year or null
 */
function getActiveAcademicYear(PDO $db): ?array
{
    $stmt = $db->query("SELECT * FROM academic_years WHERE is_active = 1 LIMIT 1");
    return $stmt->fetch() ?: null;
}

/**
 * Get the current school term from database.
 *
 * @param PDO $db Database connection
 * @return array|null Active school term or null
 */
function getActiveSchoolTerm(PDO $db): ?array
{
    $stmt = $db->query("SELECT st.*, ay.year_name FROM school_terms st JOIN academic_years ay ON st.academic_year_id = ay.id WHERE st.is_active = 1 LIMIT 1");
    return $stmt->fetch() ?: null;
}
