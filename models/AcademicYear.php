<?php
class AcademicYear {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM academic_years ORDER BY year_name DESC")->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM academic_years WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getActive() {
        return $this->db->query("SELECT * FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO academic_years (year_name, start_date, end_date, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['year_name'],
            $data['start_date'],
            $data['end_date'],
            $data['is_active'] ?? 0
        ]);
        $id = $this->db->lastInsertId();
        if ($data['is_active'] ?? 0) {
            $this->activate($id);
        }
        return $id;
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE academic_years SET year_name = ?, start_date = ?, end_date = ? WHERE id = ?");
        return $stmt->execute([
            $data['year_name'],
            $data['start_date'],
            $data['end_date'],
            $id
        ]);
    }

    public function activate($id) {
        $this->db->query("UPDATE academic_years SET is_active = 0");
        $stmt = $this->db->prepare("UPDATE academic_years SET is_active = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTerms($yearId) {
        $stmt = $this->db->prepare("SELECT * FROM school_terms WHERE academic_year_id = ? ORDER BY start_date");
        $stmt->execute([$yearId]);
        return $stmt->fetchAll();
    }

    public function createTerm($data) {
        $stmt = $this->db->prepare("INSERT INTO school_terms (academic_year_id, term_name, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['academic_year_id'],
            $data['term_name'],
            $data['start_date'],
            $data['end_date'],
            $data['is_active'] ?? 0
        ]);
        $id = $this->db->lastInsertId();
        if ($data['is_active'] ?? 0) {
            $this->activateTerm($id, $data['academic_year_id']);
        }
        return $id;
    }

    public function activateTerm($id, $yearId) {
        $stmt = $this->db->prepare("UPDATE school_terms SET is_active = 0 WHERE academic_year_id = ?");
        $stmt->execute([$yearId]);
        $stmt = $this->db->prepare("UPDATE school_terms SET is_active = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
