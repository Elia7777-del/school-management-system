
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0"><i class="bi bi-shield-lock-fill text-cyan me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/change-password/update" method="POST">
                    <?php echo csrfField(); ?>
                    
                    <div class="mb-3">
                        <label for="old_password" class="form-label text-muted">Current Password</label>
                        <input type="password" class="form-control bg-dark border-secondary text-white" id="old_password" name="old_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label text-muted">New Password</label>
                        <input type="password" class="form-control bg-dark border-secondary text-white" id="new_password" name="new_password" required>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label text-muted">Confirm New Password</label>
                        <input type="password" class="form-control bg-dark border-secondary text-white" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-cyan text-dark fw-semibold"><i class="bi bi-save me-2"></i>Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
