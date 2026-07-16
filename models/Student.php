<?php
class Student {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($search = '', $classId = '', $educationLevel = '', $status = '', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT s.*, c.class_name, c.section FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.admission_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($classId)) {
            $sql .= " AND s.class_id = ?";
            $params[] = $classId;
        }
        if (!empty($educationLevel)) {
            $sql .= " AND s.education_level = ?";
            $params[] = $educationLevel;
        }
        if (!empty($status)) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY s.admission_number ASC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCount($search = '', $classId = '', $educationLevel = '', $status = '') {
        $sql = "SELECT COUNT(*) as count FROM students s WHERE s.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.admission_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($classId)) {
            $sql .= " AND s.class_id = ?";
            $params[] = $classId;
        }
        if (!empty($educationLevel)) {
            $sql .= " AND s.education_level = ?";
            $params[] = $educationLevel;
        }
        if (!empty($status)) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT s.*, c.class_name, c.section, u.username, u.email FROM students s LEFT JOIN classes c ON s.class_id = c.id LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ? AND s.deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT s.*, c.class_name, c.section FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.user_id = ? AND s.deleted_at IS NULL");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO students (user_id, admission_number, first_name, middle_name, last_name, gender, date_of_birth, class_id, education_level, phone, address, admission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['user_id'] ?? null,
            $data['admission_number'],
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
            $data['gender'],
            $data['date_of_birth'],
            $data['class_id'],
            $data['education_level'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['admission_date']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE students SET first_name = ?, middle_name = ?, last_name = ?, gender = ?, date_of_birth = ?, class_id = ?, education_level = ?, phone = ?, address = ?, status = ? WHERE id = ?");
        return $stmt->execute([
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
            $data['gender'],
            $data['date_of_birth'],
            $data['class_id'],
            $data['education_level'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE students SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getByClass($classId) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE class_id = ? AND deleted_at IS NULL ORDER BY first_name, last_name");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM students WHERE deleted_at IS NULL AND status = 'active'");
        return $stmt->fetch()['count'];
    }

    public function getCountByLevel($level) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM students WHERE education_level = ? AND deleted_at IS NULL AND status = 'active'");
        $stmt->execute([$level]);
        return $stmt->fetch()['count'];
    }

    public function getParents($studentId) {
        $stmt = $this->db->prepare("SELECT p.* FROM parents p JOIN student_parents sp ON p.id = sp.parent_id WHERE sp.student_id = ? AND p.deleted_at IS NULL");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function linkParent($studentId, $parentId) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO student_parents (student_id, parent_id) VALUES (?, ?)");
        return $stmt->execute([$studentId, $parentId]);
    }
}
