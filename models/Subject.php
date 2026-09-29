<?php
class Subject {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($educationLevel = '') {
        $schoolId = currentSchoolId();
        $scope = $_SESSION['admin_scope'] ?? 'all';
        if ($scope !== 'all') { $educationLevel = $scope; }

        $sql = "SELECT * FROM subjects WHERE 1=1";
        $params = [];
        if ($schoolId) {
            $sql .= " AND school_id = ?";
            $params[] = $schoolId;
        }
        if (!empty($educationLevel)) {
            $sql .= " AND education_level = ? AND status = 'active'";
            $params[] = $educationLevel;
        }
        $sql .= " ORDER BY education_level, subject_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $schoolId = currentSchoolId() ?? 1;
        $stmt = $this->db->prepare("INSERT INTO subjects (subject_name, subject_code, education_level, description, status, school_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['subject_name'],
            $data['subject_code'],
            $data['education_level'],
            $data['description'] ?? null,
            $data['status'] ?? 'active',
            $schoolId
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE subjects SET subject_name = ?, subject_code = ?, education_level = ?, description = ?, status = ? WHERE id = ?");
        return $stmt->execute([
            $data['subject_name'],
            $data['subject_code'],
            $data['education_level'],
            $data['description'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM subjects WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM subjects WHERE status = 'active'");
        return $stmt->fetch()['count'];
    }
}
