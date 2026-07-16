
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Record Fee Payment</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/fees/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Select Student</label>
                        <select class="form-select" name="student_id" required>
                            <?php foreach ($students as $s): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo $s['admission_number'] . ' - ' . $s['first_name'] . ' ' . $s['last_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Amount Paid (TZS)</label>
                        <input type="number" step="0.01" class="form-control" name="amount_paid" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Payment Method</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Payment Date</label>
                        <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Remarks / Details</label>
                        <input type="text" class="form-control" name="remarks">
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-check-lg me-1"></i>Record Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
