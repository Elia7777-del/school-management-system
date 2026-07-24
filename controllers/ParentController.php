<?php
require_once APP_ROOT . "/models/Student.php";
require_once APP_ROOT . "/models/Attendance.php";
require_once APP_ROOT . "/models/ExamResult.php";
require_once APP_ROOT . "/models/Fee.php";
require_once APP_ROOT . "/models/ParentComment.php";

class ParentController {
    private $db;

    public function __construct() {
        requireRole(["parent"]);
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Parent dashboard – shows children info + comments section.
     */
    public function dashboard() {
        $userId = currentUserId();

        $activeYear = getActiveAcademicYear($this->db);
        $activeTerm = getActiveSchoolTerm($this->db);
        $yearId = $activeYear["id"] ?? 0;
        $termId = $activeTerm["id"] ?? 0;

        // Fetch parent record
        $stmt = $this->db->prepare("SELECT * FROM parents WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $parent = $stmt->fetch();

        if (!$parent) {
            setFlash("error", "Parent record not found.");
            redirect("dashboard");
        }

        $parentId = $parent["id"];

        // Fetch children
        $stmt = $this->db->prepare(
            "SELECT s.*, c.class_name, c.section
             FROM students s
             JOIN student_parents sp ON s.id = sp.student_id
             LEFT JOIN classes c ON s.class_id = c.id
             WHERE sp.parent_id = ? AND s.deleted_at IS NULL"
        );
        $stmt->execute([$parentId]);
        $children = $stmt->fetchAll();

        $attendanceModel   = new Attendance();
        $examResultModel   = new ExamResult();
        $feeModel          = new Fee();
        $commentModel      = new ParentComment();

        $childrenData = [];
        foreach ($children as $child) {
            $childId = $child["id"];
            $childrenData[] = [
                "student"    => $child,
                "attendance" => $attendanceModel->getStudentAttendanceSummary($childId, $termId),
                "results"    => $examResultModel->getByStudentAll($childId),
                "balance"    => $feeModel->getStudentBalance($childId, $yearId),
                "comments"   => $commentModel->getByParentAndStudent($parentId, $childId),
            ];
        }

        $pageTitle = "Parent Dashboard";
        require_once APP_ROOT . "/views/layouts/header.php";
        require_once APP_ROOT . "/views/dashboard/parent.php";
        require_once APP_ROOT . "/views/layouts/footer.php";
    }

    /**
     * Store a new parent comment about a child.
     */
    public function storeComment() {
        requireRole(["parent"]);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            redirect("dashboard");
        }

        if (!validateCsrfToken($_POST["csrf_token"] ?? "")) {
            setFlash("error", "CSRF validation failed.");
            redirect("dashboard");
        }

        $userId    = currentUserId();
        $studentId = (int)($_POST["student_id"] ?? 0);
        $comment   = trim(sanitize($_POST["comment"] ?? ""));

        if (empty($comment)) {
            setFlash("error", "Maoni hayawezi kuwa matupu.");
            redirect("dashboard");
        }

        // Verify that the parent owns this student
        $stmt = $this->db->prepare("SELECT p.id FROM parents p WHERE p.user_id = ? AND p.deleted_at IS NULL");
        $stmt->execute([$userId]);
        $parent = $stmt->fetch();

        if (!$parent) {
            setFlash("error", "Rekodi ya mzazi haikupatikana.");
            redirect("dashboard");
        }

        $parentId = $parent["id"];

        // Confirm student belongs to this parent
        $stmt = $this->db->prepare(
            "SELECT sp.student_id FROM student_parents sp WHERE sp.parent_id = ? AND sp.student_id = ?"
        );
        $stmt->execute([$parentId, $studentId]);
        if (!$stmt->fetch()) {
            setFlash("error", "Huna ruhusa ya kuandika maoni kwa mwanafunzi huyu.");
            redirect("dashboard");
        }

        $commentModel = new ParentComment();
        $commentModel->create($parentId, $studentId, $comment);

        setFlash("success", "Maoni yametumwa kwa mafanikio.");
        redirect("dashboard");
    }

    /**
     * Delete a comment (parent can only delete their own).
     */
    public function deleteComment() {
        requireRole(["parent"]);

        $commentId = (int)($_GET["id"] ?? 0);
        $userId    = currentUserId();

        $stmt = $this->db->prepare("SELECT id FROM parents WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $parent = $stmt->fetch();

        if ($parent) {
            $commentModel = new ParentComment();
            $commentModel->delete($commentId, $parent["id"]);
        }

        setFlash("success", "Maoni yamefutwa.");
        redirect("dashboard");
    }
}
