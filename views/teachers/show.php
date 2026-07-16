
<div class="row">
    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary text-center py-4 mb-4">
            <div class="profile-avatar bg-green-gradient text-dark d-flex align-items-center justify-content-center rounded-circle fs-1 fw-bold mx-auto mb-3" style="width: 100px; height: 100px;">
                <?php echo strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)); ?>
            </div>
            <h4 class="text-white fw-bold mb-1"><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></h4>
            <p class="text-muted mb-2"><?php echo $teacher['teacher_number']; ?></p>
            <span class="badge bg-green"><?php echo getStatusBadge($teacher['status']); ?></span>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Teacher Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Qualification</div>
                    <div class="col-sm-9 text-white"><?php echo $teacher['qualification']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Gender</div>
                    <div class="col-sm-9 text-white"><?php echo ucfirst($teacher['gender']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted">Phone</div>
                    <div class="col-sm-9 text-white"><?php echo $teacher['phone']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
