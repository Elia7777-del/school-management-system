<?php
class SchoolClass {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $schoolId = currentSchoolId();
        $scope = $_SESSION['admin_scope'] ?? 'all';
        $params = [];
        $sql = "SELECT * FROM classes WHERE 1=1";
        if ($schoolId) {
            $sql .= " AND school_id = ?";
            $params[] = $schoolId;
        }
        if ($scope !== 'all') {
            $sql .= " AND education_level = ?";
            $params[] = $scope;
        }
        $sql .= " ORDER BY CASE education_level WHEN 'primary' THEN 1 ELSE 2 END, class_name, section";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByLevel($level) {
        $schoolId = currentSchoolId();
        $sql = "SELECT * FROM classes WHERE education_level = ?";
        $params = [$level];
        if ($schoolId) {
            $sql .= " AND school_id = ?";
            $params[] = $schoolId;
        }
        $sql .= " ORDER BY class_name, section";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $schoolId = currentSchoolId() ?? 1;
        $stmt = $this->db->prepare("INSERT INTO classes (class_name, education_level, section, capacity, school_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['class_name'],
            $data['education_level'],
            $data['section'] ?? null,
            $data['capacity'] ?? 40,
            $schoolId
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE classes SET class_name = ?, education_level = ?, section = ?, capacity = ? WHERE id = ?");
        return $stmt->execute([
            $data['class_name'],
            $data['education_level'],
            $data['section'] ?? null,
            $data['capacity'] ?? 40,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM classes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM classes");
        return $stmt->fetch()['count'];
    }

    public function getSubjects($classId) {
        $stmt = $this->db->prepare("SELECT s.* FROM subjects s JOIN class_subjects cs ON s.id = cs.subject_id WHERE cs.class_id = ?");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public function assignSubject($classId, $subjectId) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO class_subjects (class_id, subject_id) VALUES (?, ?)");
        return $stmt->execute([$classId, $subjectId]);
    }

    public function removeSubject($classId, $subjectId) {
        $stmt = $this->db->prepare("DELETE FROM class_subjects WHERE class_id = ? AND subject_id = ?");
        return $stmt->execute([$classId, $subjectId]);
    }

    public function getStudentCount($classId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM students WHERE class_id = ? AND deleted_at IS NULL AND status = 'active'");
        $stmt->execute([$classId]);
        return $stmt->fetch()['count'];
    }
}
