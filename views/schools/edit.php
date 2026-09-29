<?php
$plans = ['trial' => 'Trial', 'basic' => 'Basic', 'premium' => 'Premium', 'enterprise' => 'Enterprise'];
$statusOpts = ['active' => 'Active', 'suspended' => 'Suspended', 'expired' => 'Expired'];
$subStatusColors = ['active' => 'success', 'expiring_soon' => 'warning', 'expired' => 'danger', 'no_subscription' => 'secondary'];
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?php echo BASE_URL; ?>/schools" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="text-white fw-bold mb-0"><i class="bi bi-building-gear me-2 text-cyan"></i><?php echo htmlspecialchars($school['name']); ?></h4>
        <small class="text-muted">School ID: #<?php echo $school['id']; ?> &bull; <?php echo htmlspecialchars($school['email'] ?? ''); ?></small>
    </div>
    <span class="badge bg-<?php echo $school['status'] === 'active' ? 'success' : 'danger'; ?> ms-auto">
        <?php echo ucfirst($school['status']); ?>
    </span>
</div>

<div class="row g-4">
    <!-- Edit School Details -->
    <div class="col-lg-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h6 class="text-white mb-0"><i class="bi bi-info-circle me-2 text-cyan"></i>School Details</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>/schools/update">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo $school['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted">School Name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($school['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Slug (URL identifier)</label>
                        <input type="text" class="form-control" name="slug" value="<?php echo htmlspecialchars($school['slug']); ?>">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($school['email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Phone</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($school['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Address</label>
                        <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($school['address'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <select class="form-select" name="status">
                            <?php foreach ($statusOpts as $val => $label): ?>
                                <option value="<?php echo $val; ?>" <?php echo $school['status'] === $val ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-bold w-100">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Subscription History + Add New -->
    <div class="col-lg-6">
        <div class="card bg-dark-card border-secondary shadow-sm mb-4">
            <div class="card-header border-secondary bg-transparent py-3">
                <h6 class="text-white mb-0"><i class="bi bi-calendar-check me-2 text-success"></i>Add New Subscription</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>/schools/add-subscription">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="school_id" value="<?php echo $school['id']; ?>">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Plan</label>
                            <select class="form-select" name="plan">
                                <?php foreach ($plans as $val => $label): ?>
                                    <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date" required value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Amount Paid (TZS)</label>
                            <input type="number" class="form-control" name="amount_paid" value="0" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Payment Reference</label>
                            <input type="text" class="form-control" name="payment_reference" placeholder="Invoice / Receipt number">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success fw-bold w-100">
                        <i class="bi bi-plus-circle me-2"></i>Add Subscription
                    </button>
                </form>
            </div>
        </div>

        <!-- Subscription History -->
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h6 class="text-white mb-0"><i class="bi bi-clock-history me-2 text-cyan"></i>Subscription History</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($subscriptions)): ?>
                    <p class="text-muted text-center py-4">No subscriptions yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0 small">
                            <thead><tr><th class="ps-3">Plan</th><th>Start</th><th>End</th><th>Amount</th></tr></thead>
                            <tbody>
                                <?php foreach ($subscriptions as $sub): ?>
                                    <?php $isActive = strtotime($sub['end_date']) >= time(); ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-primary"><?php echo strtoupper($sub['plan']); ?></span>
                                            <?php if ($isActive): ?><span class="badge bg-success ms-1">Active</span><?php endif; ?>
                                        </td>
                                        <td class="text-muted"><?php echo date('d/m/Y', strtotime($sub['start_date'])); ?></td>
                                        <td class="<?php echo $isActive ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo date('d/m/Y', strtotime($sub['end_date'])); ?>
                                        </td>
                                        <td class="text-muted"><?php echo number_format($sub['amount_paid']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
