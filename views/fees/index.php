
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary stat-card stat-teal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total Fees Collected</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo formatCurrency($totalCollected); ?></h3>
                </div>
                <div class="stat-icon bg-teal-translucent"><i class="bi bi-cash-coin text-teal"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary stat-card stat-red">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total Outstanding balance</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo formatCurrency($totalPending); ?></h3>
                </div>
                <div class="stat-icon bg-red-translucent"><i class="bi bi-exclamation-circle-fill text-red"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title text-white mb-0">Recent Fee Payments</h5>
        <div>
            <a href="<?php echo BASE_URL; ?>/fees/structure" class="btn btn-sm btn-outline-cyan me-2">Fee Structures</a>
            <a href="<?php echo BASE_URL; ?>/fees/record" class="btn btn-sm btn-cyan text-dark fw-semibold">Record Payment</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Amount Paid</th>
                        <th>Date</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPayments)): ?>
                        <tr><td colspan="6" class="text-center text-muted">No payments recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentPayments as $p): ?>
                            <tr>
                                <td><?php echo $p['receipt_number']; ?></td>
                                <td><?php echo $p['first_name'] . ' ' . $p['last_name']; ?></td>
                                <td><?php echo $p['class_name'] . ' ' . $p['section']; ?></td>
                                <td class="text-success"><?php echo formatCurrency($p['amount_paid']); ?></td>
                                <td><?php echo formatDate($p['payment_date']); ?></td>
                                <td><?php echo ucfirst($p['payment_method']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
