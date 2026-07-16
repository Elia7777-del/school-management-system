
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex align-items-center">
                    <div class="profile-avatar bg-cyan-gradient text-dark d-flex align-items-center justify-content-center rounded-circle fs-2 fw-bold" style="width: 80px; height: 80px;">
                        <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                    </div>
                    <div class="ms-4">
                        <h3 class="text-white fw-bold mb-1"><?php echo $user['username']; ?></h3>
                        <p class="text-muted mb-0"><i class="bi bi-envelope me-2"></i><?php echo $user['email']; ?></p>
                        <span class="badge bg-cyan text-dark mt-2"><?php echo ucfirst(str_replace('_', ' ', $user['role_name'])); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0"><i class="bi bi-info-circle text-cyan me-2"></i>Account Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Last Login</div>
                    <div class="col-sm-9 text-white"><?php echo formatDateTime($user['last_login']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Created At</div>
                    <div class="col-sm-9 text-white"><?php echo formatDateTime($user['created_at']); ?></div>
                </div>
                
                <?php if ($profile): ?>
                    <hr class="border-secondary my-4">
                    <h5 class="text-cyan mb-3">Personal Details</h5>
                    
                    <?php if ($user['role_name'] === 'student'): ?>
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Admission Number</div>
                            <div class="col-sm-9 text-white"><?php echo $profile['admission_number']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Full Name</div>
                            <div class="col-sm-9 text-white"><?php echo $profile['first_name'] . ' ' . $profile['middle_name'] . ' ' . $profile['last_name']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Class</div>
                            <div class="col-sm-9 text-white"><?php echo $profile['class_name'] . ' ' . $profile['section']; ?></div>
                        </div>
                    <?php elseif ($user['role_name'] === 'teacher'): ?>
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">TSC Number</div>
                            <div class="col-sm-9 text-white"><?php echo $profile['teacher_number']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Full Name</div>
                            <div class="col-sm-9 text-white"><?php echo $profile['first_name'] . ' ' . $profile['last_name']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Qualification</div>
                            <div class="col-sm-9 text-white"><?php echo $profile['qualification']; ?></div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
