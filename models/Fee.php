<?php
class Fee {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getStructures($classId = '', $yearId = '') {
        $sql = "SELECT fs.*, c.class_name, c.section, ay.year_name, st.term_name FROM fee_structures fs JOIN classes c ON fs.class_id = c.id JOIN academic_years ay ON fs.academic_year_id = ay.id LEFT JOIN school_terms st ON fs.term_id = st.id WHERE 1=1";
        $params = [];
        if (!empty($classId)) {
            $sql .= " AND fs.class_id = ?";
            $params[] = $classId;
        }
        if (!empty($yearId)) {
            $sql .= " AND fs.academic_year_id = ?";
            $params[] = $yearId;
        }
        $sql .= " ORDER BY c.class_name, st.term_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createStructure($data) {
        $stmt = $this->db->prepare("INSERT INTO fee_structures (class_id, academic_year_id, term_id, amount, description) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['class_id'],
            $data['academic_year_id'],
            $data['term_id'] ?? null,
            $data['amount'],
            $data['description']
        ]);
    }

    public function updateStructure($id, $data) {
        $stmt = $this->db->prepare("UPDATE fee_structures SET amount = ?, description = ? WHERE id = ?");
        return $stmt->execute([$data['amount'], $data['description'], $id]);
    }

    public function deleteStructure($id) {
        $stmt = $this->db->prepare("DELETE FROM fee_structures WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPayments($studentId, $yearId = '') {
        $sql = "SELECT fp.*, ay.year_name, st.term_name FROM fee_payments fp JOIN academic_years ay ON fp.academic_year_id = ay.id LEFT JOIN school_terms st ON fp.term_id = st.id WHERE fp.student_id = ?";
        $params = [$studentId];
        if (!empty($yearId)) {
            $sql .= " AND fp.academic_year_id = ?";
            $params[] = $yearId;
        }
        $sql .= " ORDER BY fp.payment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAllPayments($yearId = '', $termId = '', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT fp.*, s.first_name, s.last_name, s.admission_number, c.class_name, c.section FROM fee_payments fp JOIN students s ON fp.student_id = s.id JOIN classes c ON s.class_id = c.id WHERE 1=1";
        $params = [];
        if (!empty($yearId)) {
            $sql .= " AND fp.academic_year_id = ?";
            $params[] = $yearId;
        }
        if (!empty($termId)) {
            $sql .= " AND fp.term_id = ?";
            $params[] = $termId;
        }
        $sql .= " ORDER BY fp.payment_date DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPaymentCount($yearId = '', $termId = '') {
        $sql = "SELECT COUNT(*) as count FROM fee_payments fp WHERE 1=1";
        $params = [];
        if (!empty($yearId)) {
            $sql .= " AND fp.academic_year_id = ?";
            $params[] = $yearId;
        }
        if (!empty($termId)) {
            $sql .= " AND fp.term_id = ?";
            $params[] = $termId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    public function recordPayment($data) {
        $stmt = $this->db->prepare("INSERT INTO fee_payments (student_id, academic_year_id, term_id, amount_paid, payment_date, payment_method, receipt_number, recorded_by, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['student_id'],
            $data['academic_year_id'],
            $data['term_id'] ?? null,
            $data['amount_paid'],
            $data['payment_date'],
            $data['payment_method'],
            $data['receipt_number'],
            $data['recorded_by'],
            $data['remarks'] ?? null
        ]);
    }

    public function getStudentBalance($studentId, $yearId) {
        // Get student's class
        $stmt = $this->db->prepare("SELECT class_id FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        if (!$student) return 0;

        // Total required from fee_structures
        $stmt = $this->db->prepare("SELECT SUM(amount) as total_required FROM fee_structures WHERE class_id = ? AND academic_year_id = ?");
        $stmt->execute([$student['class_id'], $yearId]);
        $required = $stmt->fetch()['total_required'] ?? 0;

        // Total paid
        $stmt = $this->db->prepare("SELECT SUM(amount_paid) as total_paid FROM fee_payments WHERE student_id = ? AND academic_year_id = ?");
        $stmt->execute([$studentId, $yearId]);
        $paid = $stmt->fetch()['total_paid'] ?? 0;

        return max(0, $required - $paid);
    }

    public function getTotalCollected($yearId = '', $termId = '') {
        $sql = "SELECT SUM(amount_paid) as total FROM fee_payments WHERE 1=1";
        $params = [];
        if (!empty($yearId)) {
            $sql .= " AND academic_year_id = ?";
            $params[] = $yearId;
        }
        if (!empty($termId)) {
            $sql .= " AND term_id = ?";
            $params[] = $termId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'] ?? 0;
    }

    public function getTotalPending($yearId) {
        // Total required = SUM(structure_amount * active_students_in_that_class)
        $stmt = $this->db->prepare("SELECT SUM(fs.amount) as total_required FROM fee_structures fs JOIN students s ON fs.class_id = s.class_id WHERE fs.academic_year_id = ? AND s.deleted_at IS NULL AND s.status = 'active'");
        $stmt->execute([$yearId]);
        $required = $stmt->fetch()['total_required'] ?? 0;

        // Total paid
        $stmt = $this->db->prepare("SELECT SUM(amount_paid) as total_paid FROM fee_payments WHERE academic_year_id = ?");
        $stmt->execute([$yearId]);
        $paid = $stmt->fetch()['total_paid'] ?? 0;

        return max(0, $required - $paid);
    }
}
