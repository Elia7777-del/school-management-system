<?php
class Teacher {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($search = '', $status = '', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT t.*, u.username, u.email as user_email FROM teachers t LEFT JOIN users u ON t.user_id = u.id WHERE t.deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (t.first_name LIKE ? OR t.last_name LIKE ? OR t.teacher_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($status)) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY t.teacher_number ASC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCount($search = '', $status = '') {
        $sql = "SELECT COUNT(*) as count FROM teachers t WHERE t.deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (t.first_name LIKE ? OR t.last_name LIKE ? OR t.teacher_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($status)) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT t.*, u.username, u.email as user_email FROM teachers t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = ? AND t.deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO teachers (user_id, teacher_number, first_name, last_name, gender, phone, email, qualification, specialization, status, join_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['user_id'] ?? null,
            $data['teacher_number'],
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['phone'],
            $data['email'] ?? null,
            $data['qualification'],
            $data['specialization'] ?? null,
            $data['status'] ?? 'active',
            $data['join_date'] ?? date('Y-m-d')
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE teachers SET first_name = ?, last_name = ?, gender = ?, phone = ?, email = ?, qualification = ?, specialization = ?, status = ? WHERE id = ?");
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['phone'],
            $data['email'],
            $data['qualification'],
            $data['specialization'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE teachers SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM teachers WHERE deleted_at IS NULL AND status = 'active'");
        return $stmt->fetch()['count'];
    }

    public function getSubjects($teacherId) {
        $stmt = $this->db->prepare("SELECT s.* FROM subjects s JOIN teacher_subjects ts ON s.id = ts.subject_id WHERE ts.teacher_id = ?");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }

    public function assignSubject($teacherId, $subjectId) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO teacher_subjects (teacher_id, subject_id) VALUES (?, ?)");
        return $stmt->execute([$teacherId, $subjectId]);
    }

    public function removeSubject($teacherId, $subjectId) {
        $stmt = $this->db->prepare("DELETE FROM teacher_subjects WHERE teacher_id = ? AND subject_id = ?");
        return $stmt->execute([$teacherId, $subjectId]);
    }

    public function getClasses($teacherId, $yearId) {
        $stmt = $this->db->prepare("SELECT tc.*, c.class_name, c.section FROM teacher_classes tc JOIN classes c ON tc.class_id = c.id WHERE tc.teacher_id = ? AND tc.academic_year_id = ?");
        $stmt->execute([$teacherId, $yearId]);
        return $stmt->fetchAll();
    }

    public function assignClass($teacherId, $classId, $yearId, $isClassTeacher = 0) {
        $stmt = $this->db->prepare("INSERT INTO teacher_classes (teacher_id, class_id, academic_year_id, is_class_teacher) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE is_class_teacher = VALUES(is_class_teacher)");
        return $stmt->execute([$teacherId, $classId, $yearId, $isClassTeacher]);
    }

    public function removeClassAssignment($id) {
        $stmt = $this->db->prepare("DELETE FROM teacher_classes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
