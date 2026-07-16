
<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Scheduled Slots</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($timetable)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No timetable slots added.</td></tr>
                            <?php else: ?>
                                <?php foreach ($timetable as $t): ?>
                                    <tr>
                                        <td><?php echo $t['day_of_week']; ?></td>
                                        <td><?php echo substr($t['start_time'], 0, 5) . ' - ' . substr($t['end_time'], 0, 5); ?></td>
                                        <td><?php echo $t['subject_name']; ?></td>
                                        <td><?php echo $t['first_name'] . ' ' . $t['last_name']; ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/timetable/delete?id=<?php echo $t['id']; ?>&class_id=<?php echo $classId; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove slot?')">Remove</a>
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

    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Add Timetable Slot</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/timetable/store" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="class_id" value="<?php echo $classId; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Day</label>
                        <select class="form-select" name="day_of_week" required>
                            <?php foreach (getDaysOfWeek() as $day): ?>
                                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Subject</label>
                        <select class="form-select" name="subject_id" required>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo $s['subject_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Teacher</label>
                        <select class="form-select" name="teacher_id" required>
                            <?php foreach ($teachers as $tc): ?>
                                <option value="<?php echo $tc['id']; ?>"><?php echo $tc['first_name'] . ' ' . $tc['last_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted">Start Time</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">End Time</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Room</label>
                        <input type="text" class="form-control" name="room" placeholder="e.g. Room 3">
                    </div>

                    <button type="submit" class="btn btn-cyan text-dark fw-semibold w-100"><i class="bi bi-save me-2"></i>Save Slot</button>
                </form>
            </div>
        </div>
    </div>
</div>
