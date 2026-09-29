<?php
/**
 * School Model
 * Handles all database operations for the schools table (multi-tenant).
 */
class School {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Get all schools with latest subscription info */
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT s.*,
                   sub.plan,
                   sub.end_date AS subscription_end,
                   sub.start_date AS subscription_start,
                   CASE
                       WHEN sub.end_date IS NULL THEN 'no_subscription'
                       WHEN sub.end_date < CURDATE() THEN 'expired'
                       WHEN sub.end_date < DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring_soon'
                       ELSE 'active'
                   END AS subscription_status,
                   (SELECT COUNT(*) FROM users u WHERE u.school_id = s.id AND u.deleted_at IS NULL) AS user_count,
                   (SELECT COUNT(*) FROM students st WHERE st.school_id = s.id AND st.deleted_at IS NULL) AS student_count
            FROM schools s
            LEFT JOIN school_subscriptions sub ON sub.school_id = s.id
                AND sub.id = (
                    SELECT id FROM school_subscriptions
                    WHERE school_id = s.id
                    ORDER BY end_date DESC LIMIT 1
                )
            ORDER BY s.name ASC
        ");
        return $stmt->fetchAll();
    }

    /** Find school by ID */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM schools WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Find school by slug */
    public function findBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM schools WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    /** Create a new school */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO schools (name, slug, email, phone, address, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['slug'] ?? $this->makeSlug($data['name']),
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['status'] ?? 'active',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update school */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE schools SET name=?, slug=?, email=?, phone=?, address=?, status=?, updated_at=NOW()
            WHERE id=?
        ");
        return $stmt->execute([
            $data['name'],
            $data['slug'] ?? $this->makeSlug($data['name']),
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['status'] ?? 'active',
            $id
        ]);
    }

    /** Get subscriptions for a school */
    public function getSubscriptions(int $schoolId): array {
        $stmt = $this->db->prepare("
            SELECT ss.*, u.username AS created_by_name
            FROM school_subscriptions ss
            LEFT JOIN users u ON ss.created_by = u.id
            WHERE ss.school_id = ?
            ORDER BY ss.created_at DESC
        ");
        $stmt->execute([$schoolId]);
        return $stmt->fetchAll();
    }

    /** Get latest active subscription for a school */
    public function getActiveSubscription(int $schoolId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM school_subscriptions
            WHERE school_id = ? AND end_date >= CURDATE()
            ORDER BY end_date DESC LIMIT 1
        ");
        $stmt->execute([$schoolId]);
        return $stmt->fetch() ?: null;
    }

    /** Add a subscription to a school */
    public function addSubscription(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO school_subscriptions (school_id, plan, start_date, end_date, amount_paid, currency, payment_reference, notes, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['school_id'],
            $data['plan'],
            $data['start_date'],
            $data['end_date'],
            $data['amount_paid'] ?? 0,
            $data['currency'] ?? 'TZS',
            $data['payment_reference'] ?? null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
        ]);
    }

    /** Get count of all schools */
    public function getCount(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM schools")->fetchColumn();
    }

    /** Generate URL-safe slug from name */
    public function makeSlug(string $name): string {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        // Ensure uniqueness
        $base = rtrim($slug, '-');
        $i = 1;
        while ($this->findBySlug($slug)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /** Create a school admin user */
    public function createAdminUser(int $schoolId, array $userData): int {
        // Get school_admin role id
        $roleId = $this->db->query("SELECT id FROM roles WHERE role_name = 'school_admin'")->fetchColumn();
        $hashed = password_hash($userData['password'], PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, role_id, school_id, status)
            VALUES (?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([
            $userData['username'],
            $userData['email'],
            $hashed,
            $roleId,
            $schoolId
        ]);
        return (int)$this->db->lastInsertId();
    }
}
