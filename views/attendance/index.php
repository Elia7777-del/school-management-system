
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/attendance" method="GET" class="row g-3">
            <div class="col-md-5">
                <select class="form-select" name="class_id">
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $classId == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo $c['class_name'] . ' ' . $c['section']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <input type="date" class="form-control" name="date" value="<?php echo $date; ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-cyan text-dark w-100 fw-semibold">View</button>
            </div>
        </form>
    </div>
</div>

<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title text-white mb-0">Attendance Status</h5>
        <a href="<?php echo BASE_URL; ?>/attendance/record?class_id=<?php echo $classId; ?>&date=<?php echo $date; ?>" class="btn btn-sm btn-cyan text-dark fw-semibold">Record Attendance</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Adm No</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="4" class="text-center text-muted">No attendance recorded for this date.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo $s['admission_number']; ?></td>
                                <td><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></td>
                                <td><?php echo $s['status'] ? getStatusBadge($s['status']) : '<span class="badge bg-secondary">Not Marked</span>'; ?></td>
                                <td><?php echo $s['remarks'] ?: '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
