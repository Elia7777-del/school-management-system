
<div class="auth-wrapper d-flex align-items-center justify-content-center">
    <div class="auth-card p-4 shadow-lg border-0 text-center">
        <div class="brand-icon-wrapper mb-3 mx-auto d-flex align-items-center justify-content-center">
            <i class="bi bi-key-fill fs-1 text-warning"></i>
        </div>
        <h2 class="text-white fw-bold mb-2">Password Recovery</h2>
        <p class="text-muted mb-4">Forgot your password? Please submit a recovery request or contact the administrator.</p>

        <form action="<?php echo BASE_URL; ?>/forgot-password" method="POST">
            <?php echo csrfField(); ?>
            <div class="form-group mb-4">
                <input type="email" class="form-control" name="email" placeholder="Enter your email address" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold mb-3">Request Password Reset</button>
            <a href="<?php echo BASE_URL; ?>/login" class="text-muted text-decoration-none d-block mt-2"><i class="bi bi-arrow-left"></i> Back to Login</a>
        </form>
    </div>
</div>
