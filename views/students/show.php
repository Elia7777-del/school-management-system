
<div class="row">
    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary shadow-sm text-center py-4 mb-4">
            <div class="profile-avatar bg-cyan-gradient text-dark d-flex align-items-center justify-content-center rounded-circle fs-1 fw-bold mx-auto mb-3" style="width: 100px; height: 100px;">
                <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
            </div>
            <h4 class="text-white fw-bold mb-1"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></h4>
            <p class="text-muted mb-2"><?php echo $student['admission_number']; ?></p>
            <span class="badge bg-cyan text-dark"><?php echo $student['class_name'] . ' ' . $student['section']; ?></span>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Overview</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Gender</div>
                    <div class="col-sm-9 text-white"><?php echo ucfirst($student['gender']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Date of Birth</div>
                    <div class="col-sm-9 text-white"><?php echo formatDate($student['date_of_birth']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Admission Date</div>
                    <div class="col-sm-9 text-white"><?php echo formatDate($student['admission_date']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Education Level</div>
                    <div class="col-sm-9 text-white"><?php echo ucfirst($student['education_level']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
