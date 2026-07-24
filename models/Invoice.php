<?php
class Invoice {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO invoices (student_id, academic_year_id, term_id, invoice_number, amount, description, due_date, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['student_id'],
            $data['academic_year_id'],
            $data['term_id'] ?? null,
            $data['invoice_number'],
            $data['amount'],
            $data['description'] ?? null,
            $data['due_date'] ?? null,
            $data['created_by']
        ]);
    }

    public function getByStudent($studentId) {
        $stmt = $this->db->prepare("
            SELECT i.*, ay.year_name, st.term_name, s.first_name, s.last_name 
            FROM invoices i 
            JOIN academic_years ay ON i.academic_year_id = ay.id
            LEFT JOIN school_terms st ON i.term_id = st.id
            JOIN students s ON i.student_id = s.id
            WHERE i.student_id = ?
            ORDER BY i.created_at DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getByParent($parentId) {
        $stmt = $this->db->prepare("
            SELECT i.*, ay.year_name, st.term_name, s.first_name, s.last_name 
            FROM invoices i 
            JOIN academic_years ay ON i.academic_year_id = ay.id
            LEFT JOIN school_terms st ON i.term_id = st.id
            JOIN students s ON i.student_id = s.id
            JOIN student_parents sp ON s.id = sp.student_id
            WHERE sp.parent_id = ?
            ORDER BY i.created_at DESC
        ");
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByInvoiceNumber($invoiceNumber) {
        $stmt = $this->db->prepare("SELECT * FROM invoices WHERE invoice_number = ?");
        $stmt->execute([$invoiceNumber]);
        return $stmt->fetch();
    }

    public function getAll($yearId = '', $status = '') {
        $sql = "
            SELECT i.*, s.first_name, s.last_name, s.admission_number, c.class_name, ay.year_name 
            FROM invoices i
            JOIN students s ON i.student_id = s.id
            JOIN classes c ON s.class_id = c.id
            JOIN academic_years ay ON i.academic_year_id = ay.id
            WHERE 1=1
        ";
        $params = [];
        if (!empty($yearId)) {
            $sql .= " AND i.academic_year_id = ?";
            $params[] = $yearId;
        }
        if (!empty($status)) {
            $sql .= " AND i.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function generateInvoiceNumber() {
        return 'INV-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public function recordPayment($id, $amountPaid) {
        $invoice = $this->findById($id);
        if (!$invoice) return false;

        $newAmountPaid = $invoice['amount_paid'] + $amountPaid;
        $status = 'partial';
        if ($newAmountPaid >= $invoice['amount']) {
            $status = 'paid';
            $newAmountPaid = $invoice['amount']; // Cap it just in case
        }

        $stmt = $this->db->prepare("UPDATE invoices SET amount_paid = ?, status = ? WHERE id = ?");
        return $stmt->execute([$newAmountPaid, $status, $id]);
    }
}
