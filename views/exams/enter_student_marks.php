
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/exams/student-marks" method="GET" class="row g-3">
            <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
            <div class="col-md-9">
                <select class="form-select bg-dark border-secondary text-white" name="student_id" onchange="this.form.submit()">
                    <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo $studentId == $s['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['admission_number'] . ' - ' . $s['first_name'] . ' ' . $s['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>/exams" class="btn btn-outline-secondary w-100">Back to Exams</a>
            </div>
        </form>
    </div>
</div>

<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-header border-secondary bg-transparent py-3">
        <h5 class="card-title text-white mb-0">Record Marks for <?php echo htmlspecialchars($exam['exam_name']); ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/exams/student-marks/store" method="POST">
            <?php echo csrfField(); ?>
            <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
            <input type="hidden" name="student_id" value="<?php echo $studentId; ?>">

            <div class="table-responsive mb-4">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Marks Obtained (Max 100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($subject['subject_code']); ?></code></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm bg-dark border-secondary text-white w-50" name="marks[<?php echo $subject['id']; ?>]" value="<?php echo $existingMarks[$subject['id']] ?? ''; ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-cyan text-dark fw-semibold">Save All Marks</button>
        </form>
    </div>
</div>
