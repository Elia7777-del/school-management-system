<?php
/**
 * ActivityLog Model
 * 
 * Manages auditing of user sessions, page requests, and database modifications.
 * 
 * @package SchoolManagementSystem
 */

class ActivityLog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new activity log entry.
     * 
     * @param int $userId
     * @param string $actionType
     * @param string $description
     * @param string $ipAddress
     * @param string $requestUrl
     * @return bool
     */
    public function log($userId, $actionType, $description, $ipAddress, $requestUrl) {
        $stmt = $this->db->prepare("INSERT INTO teacher_activity_logs (user_id, action_type, description, ip_address, request_url) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $actionType, $description, $ipAddress, $requestUrl]);
    }

    /**
     * Get all logs with search and pagination, filtered by user.
     * 
     * @param string $search
     * @param string $actionType
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAll($search = '', $actionType = '', $page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT tal.*, u.username, u.email, r.role_name 
                FROM teacher_activity_logs tal 
                JOIN users u ON tal.user_id = u.id 
                JOIN roles r ON u.role_id = r.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.username LIKE ? OR u.email LIKE ? OR tal.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($actionType)) {
            $sql .= " AND tal.action_type = ?";
            $params[] = $actionType;
        }

        $sql .= " ORDER BY tal.created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get count of all log records matching filters.
     * 
     * @param string $search
     * @param string $actionType
     * @return int
     */
    public function getCount($search = '', $actionType = '') {
        $sql = "SELECT COUNT(*) as count 
                FROM teacher_activity_logs tal 
                JOIN users u ON tal.user_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.username LIKE ? OR u.email LIKE ? OR tal.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($actionType)) {
            $sql .= " AND tal.action_type = ?";
            $params[] = $actionType;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }
}
