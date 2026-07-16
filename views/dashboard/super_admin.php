
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-dark-card border-secondary stat-card stat-blue">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total Students</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo $totalStudents; ?></h3>
                </div>
                <div class="stat-icon bg-blue-translucent"><i class="bi bi-people-fill text-blue"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-dark-card border-secondary stat-card stat-green">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total Teachers</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo $totalTeachers; ?></h3>
                </div>
                <div class="stat-icon bg-green-translucent"><i class="bi bi-person-badge-fill text-green"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-dark-card border-secondary stat-card stat-purple">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total Classes</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo $totalClasses; ?></h3>
                </div>
                <div class="stat-icon bg-purple-translucent"><i class="bi bi-building-fill text-purple"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-dark-card border-secondary stat-card stat-orange">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total Subjects</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo $totalSubjects; ?></h3>
                </div>
                <div class="stat-icon bg-orange-translucent"><i class="bi bi-book-half text-orange"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card bg-dark-card border-secondary h-100">
            <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title text-white mb-0">Today's Attendance</h5>
                <span class="badge bg-secondary"><?php echo date('d M Y'); ?></span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="row text-center g-2">
                    <div class="col-4">
                        <h4 class="text-success fw-bold"><?php echo $attendanceSummary['present'] ?? 0; ?></h4>
                        <small class="text-muted">Present</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-danger fw-bold"><?php echo $attendanceSummary['absent'] ?? 0; ?></h4>
                        <small class="text-muted">Absent</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-warning fw-bold"><?php echo $attendanceSummary['late'] ?? 0; ?></h4>
                        <small class="text-muted">Late</small>
                    </div>
                </div>
                <div class="progress mt-4 bg-dark" style="height: 10px;">
                    <?php
                    $tot = ($attendanceSummary['present'] ?? 0) + ($attendanceSummary['absent'] ?? 0) + ($attendanceSummary['late'] ?? 0);
                    $pres = $tot > 0 ? (($attendanceSummary['present'] ?? 0) / $tot) * 100 : 0;
                    $abs = $tot > 0 ? (($attendanceSummary['absent'] ?? 0) / $tot) * 100 : 0;
                    $lat = $tot > 0 ? (($attendanceSummary['late'] ?? 0) / $tot) * 100 : 0;
                    ?>
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $pres; ?>%"></div>
                    <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: <?php echo $lat; ?>%"></div>
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $abs; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card bg-dark-card border-secondary h-100">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Fee Summary</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Total Collected</span>
                        <span class="text-success fw-semibold"><?php echo formatCurrency($feesCollected); ?></span>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 70%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Total Pending</span>
                        <span class="text-danger fw-semibold"><?php echo formatCurrency($pendingFees); ?></span>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                        <div class="progress-bar bg-danger" style="width: 30%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card bg-dark-card border-secondary h-100">
            <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title text-white mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/students/create" class="btn btn-outline-cyan text-start py-2.5"><i class="bi bi-person-plus-fill me-3"></i>Register New Student</a>
                    <a href="<?php echo BASE_URL; ?>/teachers/create" class="btn btn-outline-green text-start py-2.5"><i class="bi bi-person-badge me-3"></i>Add New Teacher</a>
                    <a href="<?php echo BASE_URL; ?>/attendance/record" class="btn btn-outline-purple text-start py-2.5"><i class="bi bi-calendar-check me-3"></i>Take Today's Attendance</a>
                </div>
            </div>
        </div>
    </div>
</div>
