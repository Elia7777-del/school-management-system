<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h4 class="text-white fw-bold mb-1"><i class="bi bi-receipt me-2 text-cyan"></i>Manage Invoices</h4>
        <p class="text-muted mb-0">View and manage student fee invoices.</p>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>/invoices/create" class="btn btn-cyan text-dark">
            <i class="bi bi-plus-lg me-1"></i>Create Invoice
        </a>
    </div>
</div>

<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No invoices found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td><span class="text-cyan fw-medium"><?php echo htmlspecialchars($inv['invoice_number']); ?></span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-white"><?php echo htmlspecialchars($inv['first_name'] . ' ' . $inv['last_name']); ?></span>
                                        <small class="text-muted"><?php echo htmlspecialchars($inv['class_name']); ?> | <?php echo htmlspecialchars($inv['admission_number']); ?></small>
                                    </div>
                                </td>
                                <td><span class="fw-bold text-white"><?php echo number_format($inv['amount'], 2); ?></span></td>
                                <td><span class="text-success"><?php echo number_format($inv['amount_paid'], 2); ?></span></td>
                                <td>
                                    <?php if ($inv['status'] === 'paid'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php elseif ($inv['status'] === 'partial'): ?>
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($inv['due_date']): ?>
                                        <span class="text-muted"><i class="bi bi-calendar-event me-1"></i><?php echo date('M d, Y', strtotime($inv['due_date'])); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-cyan"><i class="bi bi-eye"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
