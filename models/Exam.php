<?php
class Exam {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($classId = '', $yearId = '', $termId = '') {
        $sql = "SELECT e.*, c.class_name, c.section, ay.year_name, st.term_name FROM exams e JOIN classes c ON e.class_id = c.id JOIN academic_years ay ON e.academic_year_id = ay.id JOIN school_terms st ON e.term_id = st.id WHERE 1=1";
        $params = [];
        if (!empty($classId)) {
            $sql .= " AND e.class_id = ?";
            $params[] = $classId;
        }
        if (!empty($yearId)) {
            $sql .= " AND e.academic_year_id = ?";
            $params[] = $yearId;
        }
        if (!empty($termId)) {
            $sql .= " AND e.term_id = ?";
            $params[] = $termId;
        }
        $sql .= " ORDER BY e.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT e.*, c.class_name, c.section, c.education_level FROM exams e JOIN classes c ON e.class_id = c.id WHERE e.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO exams (exam_name, exam_type, class_id, academic_year_id, term_id, start_date, end_date, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['exam_name'],
            $data['exam_type'],
            $data['class_id'],
            $data['academic_year_id'],
            $data['term_id'],
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['status'] ?? 'pending',
            $data['created_by']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE exams SET exam_name = ?, exam_type = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
        return $stmt->execute([
            $data['exam_name'],
            $data['exam_type'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM exams WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getUpcoming() {
        $stmt = $this->db->query("SELECT e.*, c.class_name, c.section FROM exams e JOIN classes c ON e.class_id = c.id WHERE e.start_date >= CURDATE() AND e.status = 'pending' ORDER BY e.start_date ASC LIMIT 5");
        return $stmt->fetchAll();
    }
}
