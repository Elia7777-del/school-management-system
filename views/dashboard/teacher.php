
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card bg-dark-card border-secondary stat-card stat-blue">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">My Assigned Classes</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo count($assignedClasses); ?></h3>
                </div>
                <div class="stat-icon bg-blue-translucent"><i class="bi bi-building-fill text-blue"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card bg-dark-card border-secondary stat-card stat-green">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Today's Total Attendance</h6>
                    <h3 class="text-white fw-bold mb-0"><?php echo $todayAttendance; ?></h3>
                </div>
                <div class="stat-icon bg-green-translucent"><i class="bi bi-calendar-check-fill text-green"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm mb-4">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0"><i class="bi bi-building me-2 text-cyan"></i>Assigned Classes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Section</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($assignedClasses)): ?>
                                <tr><td colspan="4" class="text-center text-muted">No classes assigned.</td></tr>
                            <?php else: ?>
                                <?php foreach ($assignedClasses as $c): ?>
                                    <tr>
                                        <td><?php echo $c['class_name']; ?></td>
                                        <td><?php echo $c['section'] ?: '-'; ?></td>
                                        <td><?php echo $c['is_class_teacher'] ? '<span class="badge bg-success">Class Teacher</span>' : 'Subject Teacher'; ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/attendance/record?class_id=<?php echo $c['class_id']; ?>" class="btn btn-sm btn-cyan text-dark me-2">Take Attendance</a>
                                            <a href="<?php echo BASE_URL; ?>/timetable?class_id=<?php echo $c['class_id']; ?>" class="btn btn-sm btn-outline-secondary">Timetable</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
