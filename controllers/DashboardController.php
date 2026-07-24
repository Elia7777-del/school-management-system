<?php
require_once APP_ROOT . '/models/Student.php';
require_once APP_ROOT . '/models/Teacher.php';
require_once APP_ROOT . '/models/SchoolClass.php';
require_once APP_ROOT . '/models/Subject.php';
require_once APP_ROOT . '/models/Attendance.php';
require_once APP_ROOT . '/models/Fee.php';
require_once APP_ROOT . '/models/Exam.php';
require_once APP_ROOT . '/models/ExamResult.php';
require_once APP_ROOT . '/models/Timetable.php';
require_once APP_ROOT . '/models/ParentComment.php';

class DashboardController {
    private $db;

    public function __construct() {
        requireLogin();
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $role = currentUserRole();
        $user_id = currentUserId();

        // Load active academic year
        $activeYear = getActiveAcademicYear($this->db);
        $activeTerm = getActiveSchoolTerm($this->db);
        $yearId = $activeYear['id'] ?? 0;
        $termId = $activeTerm['id'] ?? 0;

        if (isAdmin()) {
            $studentModel = new Student();
            $teacherModel = new Teacher();
            $classModel = new SchoolClass();
            $subjectModel = new Subject();
            $attendanceModel = new Attendance();
            $feeModel = new Fee();
            $examModel = new Exam();

            $scope = currentAdminScope(); // 'all', 'primary', or 'secondary'

            // Stats – filtered by scope for scoped admins
            if ($scope === 'all') {
                $totalStudents = $studentModel->getTotalCount();
                $totalClasses  = $classModel->getTotalCount();
            } else {
                $totalStudents = $studentModel->getCountByLevel($scope);
                // Count classes for this scope only
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM classes WHERE education_level = ?");
                $stmt->execute([$scope]);
                $totalClasses = $stmt->fetch()['count'];
            }

            $totalTeachers  = $teacherModel->getTotalCount();
            $totalSubjects  = $subjectModel->getTotalCount();

            $feesCollected    = $feeModel->getTotalCollected($yearId);
            $pendingFees      = $feeModel->getTotalPending($yearId);
            $attendanceSummary = $attendanceModel->getTodaySummary();
            $upcomingExams    = $examModel->getUpcoming();

            // Stats for level charts
            $primaryCount   = $studentModel->getCountByLevel('primary');
            $secondaryCount = $studentModel->getCountByLevel('secondary');

            // Announcements
            $stmt = $this->db->query("SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC LIMIT 5");
            $announcements = $stmt->fetchAll();

            $pageTitle = 'Dashboard';
            require_once APP_ROOT . '/views/layouts/header.php';
            if ($role === 'super_admin') {
                require_once APP_ROOT . '/views/dashboard/super_admin.php';
            } else {
                require_once APP_ROOT . '/views/dashboard/school_admin.php';
            }
            require_once APP_ROOT . '/views/layouts/footer.php';

        } elseif ($role === 'teacher') {
            $teacherModel = new Teacher();
            $examModel = new Exam();
            $attendanceModel = new Attendance();

            $teacher = $teacherModel->findByUserId($user_id);
            $teacherId = $teacher['id'] ?? 0;

            $assignedClasses = $teacherModel->getClasses($teacherId, $yearId);
            $upcomingExams = $examModel->getUpcoming();
            $todayAttendance = $attendanceModel->getTodayCount();

            $pageTitle = 'Teacher Dashboard';
            require_once APP_ROOT . '/views/layouts/header.php';
            require_once APP_ROOT . '/views/dashboard/teacher.php';
            require_once APP_ROOT . '/views/layouts/footer.php';

        } elseif ($role === 'student') {
            $studentModel = new Student();
            $attendanceModel = new Attendance();
            $examResultModel = new ExamResult();
            $timetableModel = new Timetable();

            $student = $studentModel->findByUserId($user_id);
            $studentId = $student['id'] ?? 0;

            $attendanceRate = $attendanceModel->getStudentAttendanceSummary($studentId, $termId);
            $recentResults = $examResultModel->getByStudentAll($studentId);
            $timetable = $timetableModel->getByClass($student['class_id'] ?? 0, $termId);

            $pageTitle = 'Student Dashboard';
            require_once APP_ROOT . '/views/layouts/header.php';
            require_once APP_ROOT . '/views/dashboard/student.php';
            require_once APP_ROOT . '/views/layouts/footer.php';

        } elseif ($role === 'parent') {
            $studentModel = new Student();
            $attendanceModel = new Attendance();
            $examResultModel = new ExamResult();
            $feeModel = new Fee();

            // Fetch parent record
            $stmt = $this->db->prepare("SELECT id FROM parents WHERE user_id = ? AND deleted_at IS NULL");
            $stmt->execute([$user_id]);
            $parentRecord = $stmt->fetch();
            $parentId = $parentRecord['id'] ?? 0;

            // Fetch children linked to parent
            $stmt = $this->db->prepare(
                "SELECT s.*, c.class_name, c.section
                 FROM students s
                 JOIN student_parents sp ON s.id = sp.student_id
                 JOIN parents p ON sp.parent_id = p.id
                 LEFT JOIN classes c ON s.class_id = c.id
                 WHERE p.user_id = ? AND s.deleted_at IS NULL"
            );
            $stmt->execute([$user_id]);
            $children = $stmt->fetchAll();

            $childrenData = [];
            $commentModel = new ParentComment();
            foreach ($children as $child) {
                $childId = $child['id'];

                $attendance = ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0];
                try {
                    $attendance = $attendanceModel->getStudentAttendanceSummary($childId, $termId);
                } catch (Exception $e) {}

                $results = [];
                try {
                    $results = $examResultModel->getByStudentAll($childId);
                } catch (Exception $e) {}

                $balance = 0;
                try {
                    $balance = $feeModel->getStudentBalance($childId, $yearId);
                } catch (Exception $e) {}

                $comments = [];
                try {
                    $comments = $commentModel->getByParentAndStudent($parentId, $childId);
                } catch (Exception $e) {}

                $childrenData[] = [
                    'student' => $child,
                    'attendance' => $attendance,
                    'results' => $results,
                    'balance' => $balance,
                    'comments' => $comments
                ];
            }

            $pageTitle = 'Parent Dashboard';
            require_once APP_ROOT . '/views/layouts/header.php';
            require_once APP_ROOT . '/views/dashboard/parent.php';
            require_once APP_ROOT . '/views/layouts/footer.php';
        }
    }
}
