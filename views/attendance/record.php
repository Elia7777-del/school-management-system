
<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-header border-secondary bg-transparent py-3">
        <h5 class="card-title text-white mb-0">Take Attendance</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/attendance/store" method="POST">
            <?php echo csrfField(); ?>
            <input type="hidden" name="class_id" value="<?php echo $classId; ?>">
            <input type="hidden" name="date" value="<?php echo $date; ?>">

            <div class="table-responsive mb-4">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></td>
                                <td><input type="radio" name="attendance[<?php echo $s['student_id']; ?>]" value="present" <?php echo $s['status'] === 'present' || !$s['status'] ? 'checked' : ''; ?>></td>
                                <td><input type="radio" name="attendance[<?php echo $s['student_id']; ?>]" value="absent" <?php echo $s['status'] === 'absent' ? 'checked' : ''; ?>></td>
                                <td><input type="radio" name="attendance[<?php echo $s['student_id']; ?>]" value="late" <?php echo $s['status'] === 'late' ? 'checked' : ''; ?>></td>
                                <td><input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="remarks[<?php echo $s['student_id']; ?>]" value="<?php echo $s['remarks']; ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-cyan text-dark fw-semibold">Save Attendance</button>
        </form>
    </div>
</div>
