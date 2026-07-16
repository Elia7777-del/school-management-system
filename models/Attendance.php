<?php
class Attendance {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function record($data) {
        $stmt = $this->db->prepare("INSERT INTO attendance (student_id, class_id, academic_year_id, term_id, date, status, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), remarks = VALUES(remarks), recorded_by = VALUES(recorded_by)");
        return $stmt->execute([
            $data['student_id'],
            $data['class_id'],
            $data['academic_year_id'],
            $data['term_id'],
            $data['date'],
            $data['status'],
            $data['remarks'] ?? null,
            $data['recorded_by']
        ]);
    }

    public function bulkRecord($records) {
        $this->db->beginTransaction();
        try {
            foreach ($records as $record) {
                $this->record($record);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getByClassAndDate($classId, $date) {
        $stmt = $this->db->prepare("SELECT s.id as student_id, s.first_name, s.middle_name, s.last_name, s.admission_number, a.status, a.remarks FROM students s LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ? WHERE s.class_id = ? AND s.deleted_at IS NULL AND s.status = 'active' ORDER BY s.first_name, s.last_name");
        $stmt->execute([$date, $classId]);
        return $stmt->fetchAll();
    }

    public function getByStudent($studentId, $month, $year) {
        $stmt = $this->db->prepare("SELECT * FROM attendance WHERE student_id = ? AND MONTH(date) = ? AND YEAR(date) = ? ORDER BY date");
        $stmt->execute([$studentId, $month, $year]);
        return $stmt->fetchAll();
    }

    public function getByClass($classId, $month, $year) {
        $stmt = $this->db->prepare("SELECT a.*, s.first_name, s.last_name FROM attendance a JOIN students s ON a.student_id = s.id WHERE a.class_id = ? AND MONTH(a.date) = ? AND YEAR(a.date) = ? ORDER BY a.date, s.first_name");
        $stmt->execute([$classId, $month, $year]);
        return $stmt->fetchAll();
    }

    public function getTodayCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM attendance WHERE date = CURDATE()");
        return $stmt->fetch()['count'];
    }

    public function getTodaySummary() {
        $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM attendance WHERE date = CURDATE() GROUP BY status");
        $results = $stmt->fetchAll();
        $summary = ['present' => 0, 'absent' => 0, 'late' => 0];
        foreach ($results as $row) {
            $summary[$row['status']] = $row['count'];
        }
        return $summary;
    }

    public function getStudentAttendanceSummary($studentId, $termId) {
        $stmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM attendance WHERE student_id = ? AND term_id = ? GROUP BY status");
        $stmt->execute([$studentId, $termId]);
        $results = $stmt->fetchAll();
        $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0];
        foreach ($results as $row) {
            $summary[$row['status']] = $row['count'];
            $summary['total'] += $row['count'];
        }
        return $summary;
    }
}
