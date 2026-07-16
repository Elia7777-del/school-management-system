
<div class="card bg-dark-card border-secondary shadow-sm mb-4 d-print-none">
    <div class="card-body text-end">
        <button type="button" class="btn btn-outline-cyan fw-semibold" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print Report</button>
    </div>
</div>
<div class="card bg-dark-card border-secondary shadow-sm printable-area">
    <div class="card-body">
        <h4 class="text-white fw-bold text-center mb-1"><?php echo APP_NAME; ?></h4>
        <h6 class="text-muted text-center mb-4">Fee Collection Statement</h6>
        <div class="table-responsive">
            <table class="table table-striped table-dark">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Amount Paid</th>
                        <th>Date</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; foreach ($payments as $p): $total += $p['amount_paid']; ?>
                        <tr>
                            <td><?php echo $p['receipt_number']; ?></td>
                            <td><?php echo $p['first_name'] . ' ' . $p['last_name']; ?></td>
                            <td><?php echo $p['class_name'] . ' ' . $p['section']; ?></td>
                            <td class="text-success"><?php echo formatCurrency($p['amount_paid']); ?></td>
                            <td><?php echo formatDate($p['payment_date']); ?></td>
                            <td><?php echo ucfirst($p['payment_method']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold border-top">
                        <td colspan="3" class="text-end text-white">Total Revenue:</td>
                        <td class="text-success"><?php echo formatCurrency($total); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
