<?php
/**
 * Audit Controller
 * 
 * Allows admins to view teacher/user activity logs including:
 * - Login/logout timestamps
 * - Marks entry and attendance actions
 * - IP addresses and request URLs
 * - Filterable by action type, user search, date range
 *
 * @package SchoolManagementSystem
 */
require_once APP_ROOT . '/models/ActivityLog.php';

class AuditController {
    private $logModel;

    public function __construct() {
        requireRole(['super_admin', 'school_admin']);
        $this->logModel = new ActivityLog();
    }

    public function index() {
        $search = sanitize($_GET['search'] ?? '');
        $actionType = sanitize($_GET['action_type'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $logs = $this->logModel->getAll($search, $actionType, $page, $perPage);
        $totalLogs = $this->logModel->getCount($search, $actionType);
        $pagination = paginate($totalLogs, $perPage, $page);

        // Build base URL for pagination
        $baseUrl = BASE_URL . '/audit?search=' . urlencode($search) . '&action_type=' . urlencode($actionType);

        $actionTypes = ['login', 'logout', 'marks_entry', 'attendance', 'data_change', 'page_view'];

        $pageTitle = 'Activity Audit Logs';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/audit/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }
}
