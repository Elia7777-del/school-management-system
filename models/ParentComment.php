<?php
class ParentComment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByStudent(int $studentId): array {
        $stmt = $this->db->prepare(
            "SELECT pc.*, p.first_name AS parent_first, p.last_name AS parent_last, p.relationship
             FROM parent_comments pc
             JOIN parents p ON pc.parent_id = p.id
             WHERE pc.student_id = ?
             ORDER BY pc.created_at DESC"
        );
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getByParentAndStudent(int $parentId, int $studentId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM parent_comments WHERE parent_id = ? AND student_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$parentId, $studentId]);
        return $stmt->fetchAll();
    }

    public function create(int $parentId, int $studentId, string $comment): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO parent_comments (parent_id, student_id, comment) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$parentId, $studentId, $comment]);
    }

    public function delete(int $commentId, int $parentId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM parent_comments WHERE id = ? AND parent_id = ?"
        );
        return $stmt->execute([$commentId, $parentId]);
    }
}
