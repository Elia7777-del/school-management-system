<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ? AND u.deleted_at IS NULL");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ? AND u.deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll($search = '', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (u.username LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY u.created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCount($search = '') {
        $sql = "SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (username LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role_id'],
            $data['status'] ?? 'active'
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET username = ?, email = ?, role_id = ?, status = ?";
        $params = [$data['username'], $data['email'], $data['role_id'], $data['status']];
        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $sql .= " WHERE id = ?";
        $params[] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function changePassword($id, $newPassword) {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
