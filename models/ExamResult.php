<?php
class ExamResult {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByExam($examId) {
        $stmt = $this->db->prepare("SELECT er.*, s.first_name, s.last_name, s.admission_number, sub.subject_name, sub.subject_code FROM exam_results er JOIN students s ON er.student_id = s.id JOIN subjects sub ON er.subject_id = sub.id WHERE er.exam_id = ? ORDER BY s.first_name, s.last_name, sub.subject_name");
        $stmt->execute([$examId]);
        return $stmt->fetchAll();
    }

    public function getByStudent($studentId, $examId) {
        $stmt = $this->db->prepare("SELECT er.*, sub.subject_name, sub.subject_code FROM exam_results er JOIN subjects sub ON er.subject_id = sub.id WHERE er.student_id = ? AND er.exam_id = ? ORDER BY sub.subject_name");
        $stmt->execute([$studentId, $examId]);
        return $stmt->fetchAll();
    }

    public function getByStudentAll($studentId) {
        $stmt = $this->db->prepare("SELECT er.*, e.exam_name, e.exam_type, sub.subject_name, sub.subject_code, ay.year_name, st.term_name FROM exam_results er JOIN exams e ON er.exam_id = e.id JOIN subjects sub ON er.subject_id = sub.id JOIN academic_years ay ON e.academic_year_id = ay.id JOIN school_terms st ON e.term_id = st.id WHERE er.student_id = ? ORDER BY ay.year_name DESC, st.term_name DESC, e.exam_name, sub.subject_name");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function storeMarks($examId, $studentId, $subjectId, $marks, $educationLevel) {
        if ($educationLevel === 'primary') {
            $grade = getGradePrimary($marks);
            $points = null;
            $remarks = getRemarksPrimary($grade);
        } else {
            $gradeData = getGradeSecondary($marks);
            $grade = $gradeData['grade'];
            $points = $gradeData['points'];
            $remarks = $gradeData['remarks'];
        }

        $stmt = $this->db->prepare("INSERT INTO exam_results (exam_id, student_id, subject_id, marks_obtained, grade, points, remarks) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), grade = VALUES(grade), points = VALUES(points), remarks = VALUES(remarks)");
        return $stmt->execute([$examId, $studentId, $subjectId, $marks, $grade, $points, $remarks]);
    }

    public function bulkStoreMarks($examId, $subjectId, $marksData, $educationLevel) {
        $this->db->beginTransaction();
        try {
            foreach ($marksData as $studentId => $marks) {
                if ($marks === '' || $marks === null) {
                    $this->deleteMark($examId, $studentId, $subjectId);
                } else {
                    $this->storeMarks($examId, $studentId, $subjectId, (float)$marks, $educationLevel);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteMark($examId, $studentId, $subjectId) {
        $stmt = $this->db->prepare("DELETE FROM exam_results WHERE exam_id = ? AND student_id = ? AND subject_id = ?");
        return $stmt->execute([$examId, $studentId, $subjectId]);
    }

    public function getClassResults($examId) {
        // Fetch all student marks grouped by student
        $stmt = $this->db->prepare("SELECT er.student_id, s.first_name, s.middle_name, s.last_name, s.admission_number, SUM(er.marks_obtained) as total_marks, AVG(er.marks_obtained) as average_marks, COUNT(er.id) as subjects_count FROM exam_results er JOIN students s ON er.student_id = s.id WHERE er.exam_id = ? GROUP BY er.student_id ORDER BY total_marks DESC");
        $stmt->execute([$examId]);
        return $stmt->fetchAll();
    }

    public function getStudentResultSummary($studentId, $examId) {
        $stmt = $this->db->prepare("SELECT SUM(marks_obtained) as total, AVG(marks_obtained) as average, COUNT(*) as count FROM exam_results WHERE student_id = ? AND exam_id = ?");
        $stmt->execute([$studentId, $examId]);
        return $stmt->fetch();
    }

    public function getStudentRank($studentId, $examId) {
        // Optimized rank calculation using subqueries for cross-version MySQL compatibility
        $sql = "
            SELECT 
                1 + (
                    SELECT COUNT(*) FROM (
                        SELECT SUM(marks_obtained) as t 
                        FROM exam_results 
                        WHERE exam_id = ? 
                        GROUP BY student_id
                    ) as totals 
                    WHERE t > IFNULL(student_totals.my_total, 0)
                ) as rank,
                (
                    SELECT COUNT(DISTINCT student_id) 
                    FROM exam_results 
                    WHERE exam_id = ?
                ) as total_students
            FROM (
                SELECT SUM(marks_obtained) as my_total 
                FROM exam_results 
                WHERE exam_id = ? AND student_id = ?
            ) as student_totals
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$examId, $examId, $examId, $studentId]);
        $result = $stmt->fetch();
        return [
            'rank' => (int)($result['rank'] ?? 0),
            'total_students' => (int)($result['total_students'] ?? 0)
        ];
    }
}
