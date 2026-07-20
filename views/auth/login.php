
<div class="auth-wrapper d-flex align-items-center justify-content-center">
    <div class="auth-card p-4 shadow-lg border-0">
        <div class="text-center mb-4">
            <div class="brand-icon-wrapper mb-3 mx-auto d-flex align-items-center justify-content-center">
                <i class="bi bi-mortarboard-fill fs-1 text-cyan"></i>
            </div>
            <h2 class="text-white fw-bold mb-1"><?php echo APP_NAME; ?></h2>
            <p class="text-muted">Tanzanian School Portal (MVP)</p>
        </div>

        <?php echo displayFlashMessages(); ?>

        <form action="<?php echo BASE_URL; ?>/login" method="POST" class="needs-validation" novalidate>
            <?php echo csrfField(); ?>
            
            <div class="form-group mb-3">
                <label for="username" class="form-label text-white">Username or Admission Number</label>
                <div class="input-group">
                    <span class="input-group-text border-secondary bg-dark text-white"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username or admission number" value="<?php echo old('username'); ?>" required>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="password" class="form-label text-white">Password</label>
                <div class="input-group">
                    <span class="input-group-text border-secondary bg-dark text-white"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input border-secondary" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-light" for="remember">Remember me</label>
                </div>
                <a href="<?php echo BASE_URL; ?>/forgot-password" class="text-cyan text-decoration-none">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold mb-3">Sign In</button>
        </form>
    </div>
</div>
