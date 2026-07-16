
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/timetable" method="GET" class="row g-3">
            <div class="col-md-10">
                <select class="form-select" name="class_id" onchange="this.form.submit()">
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $classId == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo $c['class_name'] . ' ' . $c['section']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (isAdmin()): ?>
                <div class="col-md-2">
                    <a href="<?php echo BASE_URL; ?>/timetable/manage?class_id=<?php echo $classId; ?>" class="btn btn-cyan text-dark w-100 fw-semibold">Manage</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-header border-secondary bg-transparent py-3">
        <h5 class="card-title text-white mb-0">Timetable Schedule</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 15%;">Day</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (getDaysOfWeek() as $day): ?>
                        <tr>
                            <td class="fw-bold align-middle"><?php echo $day; ?></td>
                            <td>
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <?php if (empty($grouped[$day])): ?>
                                        <span class="text-muted">No classes scheduled.</span>
                                    <?php else: ?>
                                        <?php foreach ($grouped[$day] as $t): ?>
                                            <div class="p-2 border border-secondary rounded bg-dark" style="min-width: 160px;">
                                                <div class="fw-bold text-cyan"><?php echo $t['subject_name']; ?></div>
                                                <small class="text-muted"><?php echo substr($t['start_time'], 0, 5) . ' - ' . substr($t['end_time'], 0, 5); ?></small><br>
                                                <small class="text-white"><?php echo $t['first_name'] . ' ' . $t['last_name']; ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
