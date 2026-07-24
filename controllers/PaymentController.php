<?php
require_once APP_ROOT . '/models/Invoice.php';
require_once APP_ROOT . '/models/PaymentTransaction.php';
require_once APP_ROOT . '/models/Student.php';
require_once APP_ROOT . '/models/Fee.php';

class PaymentController {
    private $invoiceModel;
    private $transactionModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->transactionModel = new PaymentTransaction();
    }

    /**
     * Initiates AzamPay Checkout Push USSD
     */
    public function checkout() {
        requireRole(['student', 'parent']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $provider = sanitize($_POST['provider'] ?? ''); // e.g. Mpesa, Tigo, Airtel
        $phone = sanitize($_POST['phone'] ?? '');

        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice || $invoice['status'] === 'paid') {
            echo json_encode(['success' => false, 'message' => 'Invalid or already paid invoice.']);
            return;
        }

        // 1. Create a local payment transaction record
        $reference = 'REF-' . time() . '-' . rand(1000, 9999);
        $this->transactionModel->create([
            'invoice_id' => $invoiceId,
            'student_id' => $invoice['student_id'],
            'reference' => $reference,
            'amount' => $amount,
            'provider' => $provider,
            'account_number' => $phone,
            'status' => 'initiated'
        ]);

        // 2. MOCK AzamPay API Call
        // In a real scenario, you would generate a token and call the Checkout endpoint here.
        // We will simulate a successful push to the user's phone.
        
        // Mock response
        $apiSuccess = true;

        if ($apiSuccess) {
            $this->transactionModel->updateStatus($reference, 'pending', 'AZAM-' . rand(100000, 999999), 'Push sent successfully');
            echo json_encode([
                'success' => true, 
                'message' => 'Please check your phone and enter your PIN to complete the payment.',
                'reference' => $reference
            ]);
        } else {
            $this->transactionModel->updateStatus($reference, 'failed', null, 'API Call Failed');
            echo json_encode(['success' => false, 'message' => 'Failed to initiate payment with provider.']);
        }
    }

    /**
     * Webhook to receive payment status from AzamPay
     * Route: POST /api/azampay/webhook
     */
    public function webhook() {
        // Read JSON payload from AzamPay
        $payload = file_get_contents('php://input');
        $data = json_decode($payload, true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            return;
        }

        // Example AzamPay payload structure (simplified)
        // { "reference": "REF-...", "status": "success", "transaction_id": "AZAM-...", "message": "..." }

        $reference = $data['reference'] ?? '';
        $status = $data['status'] ?? ''; // 'success' or 'failed'
        $azampayTxId = $data['transaction_id'] ?? '';
        $message = $data['message'] ?? '';

        $transaction = $this->transactionModel->findByReference($reference);
        if (!$transaction) {
            http_response_code(404);
            echo json_encode(['error' => 'Transaction not found']);
            return;
        }

        if ($transaction['status'] === 'success') {
            // Already processed
            echo json_encode(['success' => true]);
            return;
        }

        if ($status === 'success') {
            // 1. Update Transaction
            $this->transactionModel->updateStatus($reference, 'success', $azampayTxId, $message);
            
            // 2. Update Invoice
            $this->invoiceModel->recordPayment($transaction['invoice_id'], $transaction['amount']);
            
            // 3. Generate Official Receipt in fee_payments table
            $feeModel = new Fee();
            $invoice = $this->invoiceModel->findById($transaction['invoice_id']);
            
            $feeModel->recordPayment([
                'student_id' => $transaction['student_id'],
                'academic_year_id' => $invoice['academic_year_id'],
                'term_id' => $invoice['term_id'],
                'amount_paid' => $transaction['amount'],
                'payment_date' => date('Y-m-d'),
                'payment_method' => 'mobile_money',
                'receipt_number' => $azampayTxId,
                'recorded_by' => 0, // 0 = System/Automated
                'remarks' => 'Auto-generated via AzamPay. Ref: ' . $reference
            ]);

        } else {
            // Failed
            $this->transactionModel->updateStatus($reference, 'failed', $azampayTxId, $message);
        }

        echo json_encode(['success' => true]);
    }
}
