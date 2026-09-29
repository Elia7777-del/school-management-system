<?php $plans = ['trial' => 'Trial (Free)', 'basic' => 'Basic', 'premium' => 'Premium', 'enterprise' => 'Enterprise']; ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center gap-3">
                <a href="<?php echo BASE_URL; ?>/schools" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h5 class="card-title text-white mb-0"><i class="bi bi-building-add me-2 text-cyan"></i>Register New School</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>/schools/store">
                    <?php echo csrfField(); ?>
                    <h6 class="text-cyan mb-3 fw-semibold text-uppercase" style="font-size:0.75rem;letter-spacing:1px;">School Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label text-muted">School Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Mwalimu Nyerere Secondary School">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Phone</label>
                            <input type="text" class="form-control" name="phone" placeholder="+255...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required placeholder="school@email.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Address</label>
                            <input type="text" class="form-control" name="address" placeholder="City, Region, Tanzania">
                        </div>
                    </div>

                    <hr class="border-secondary">
                    <h6 class="text-cyan mb-3 fw-semibold text-uppercase" style="font-size:0.75rem;letter-spacing:1px;">Subscription</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted">Plan</label>
                            <select class="form-select" name="plan">
                                <?php foreach ($plans as $val => $label): ?>
                                    <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date" required 
                                   value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Amount Paid (TZS)</label>
                            <input type="number" class="form-control" name="amount_paid" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Payment Reference</label>
                            <input type="text" class="form-control" name="payment_reference" placeholder="e.g. Invoice #123">
                        </div>
                    </div>

                    <hr class="border-secondary">
                    <h6 class="text-cyan mb-3 fw-semibold text-uppercase" style="font-size:0.75rem;letter-spacing:1px;">School Admin Account</h6>
                    <p class="text-muted small">This creates the administrator login for this school.</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="admin_username" required placeholder="e.g. admin_schoolname">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Password <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="admin_password" required placeholder="Strong password" value="Admin@<?php echo date('Y'); ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo BASE_URL; ?>/schools" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-cyan text-dark fw-bold px-4">
                            <i class="bi bi-check-circle me-2"></i>Register School
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
