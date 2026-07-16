<?php
class Timetable {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByClass($classId, $termId) {
        $stmt = $this->db->prepare("SELECT t.*, s.subject_name, s.subject_code, tc.first_name, tc.last_name FROM timetables t JOIN subjects s ON t.subject_id = s.id JOIN teachers tc ON t.teacher_id = tc.id WHERE t.class_id = ? AND t.term_id = ? ORDER BY CASE t.day_of_week WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3 WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 ELSE 6 END, t.start_time");
        $stmt->execute([$classId, $termId]);
        return $stmt->fetchAll();
    }

    public function getByTeacher($teacherId, $termId) {
        $stmt = $this->db->prepare("SELECT t.*, s.subject_name, s.subject_code, c.class_name, c.section FROM timetables t JOIN subjects s ON t.subject_id = s.id JOIN classes c ON t.class_id = c.id WHERE t.teacher_id = ? AND t.term_id = ? ORDER BY CASE t.day_of_week WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3 WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 ELSE 6 END, t.start_time");
        $stmt->execute([$teacherId, $termId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO timetables (class_id, subject_id, teacher_id, academic_year_id, term_id, day_of_week, start_time, end_time, room) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['class_id'],
            $data['subject_id'],
            $data['teacher_id'],
            $data['academic_year_id'],
            $data['term_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $data['room'] ?? null
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM timetables WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
