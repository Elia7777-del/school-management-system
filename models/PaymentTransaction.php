<?php
class PaymentTransaction {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO payment_transactions (invoice_id, student_id, reference, amount, provider, account_number, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['invoice_id'] ?? null,
            $data['student_id'],
            $data['reference'],
            $data['amount'],
            $data['provider'],
            $data['account_number'],
            $data['status'] ?? 'initiated'
        ]);
    }

    public function findByReference($reference) {
        $stmt = $this->db->prepare("SELECT * FROM payment_transactions WHERE reference = ?");
        $stmt->execute([$reference]);
        return $stmt->fetch();
    }

    public function updateStatus($reference, $status, $azampayTransactionId = null, $responseMessage = null) {
        $sql = "UPDATE payment_transactions SET status = ?";
        $params = [$status];

        if ($azampayTransactionId !== null) {
            $sql .= ", azampay_transaction_id = ?";
            $params[] = $azampayTransactionId;
        }
        if ($responseMessage !== null) {
            $sql .= ", response_message = ?";
            $params[] = $responseMessage;
        }

        $sql .= " WHERE reference = ?";
        $params[] = $reference;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
