
<?php if (empty($subjects)): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
        <div>
            You are not assigned to teach any subjects for this class. 
            <a href="<?php echo BASE_URL; ?>/exams" class="alert-link">Return to Exams</a>.
        </div>
    </div>
<?php else: ?>
    <div class="card bg-dark-card border-secondary shadow-sm mb-4">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/exams/marks" method="GET" class="row g-3">
                <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
                <div class="col-md-9">
                    <select class="form-select bg-dark border-secondary text-white" name="subject_id" onchange="this.form.submit()">
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo $subjectId == $s['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['subject_name'] . ' (' . $s['subject_code'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card bg-dark-card border-secondary shadow-sm">
        <div class="card-header border-secondary bg-transparent py-3">
            <h5 class="card-title text-white mb-0">Record Marks for <?php echo htmlspecialchars($exam['exam_name']); ?></h5>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/exams/marks/store" method="POST">
                <?php echo csrfField(); ?>
                <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
                <input type="hidden" name="subject_id" value="<?php echo $subjectId; ?>">

                <div class="table-responsive mb-4">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Adm No</th>
                                <th>Student Name</th>
                                <th>Marks Obtained (Max 100)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($s['admission_number']); ?></td>
                                    <td><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm bg-dark border-secondary text-white" style="width: 120px;" name="marks[<?php echo $s['id']; ?>]" value="<?php echo htmlspecialchars($existingMarks[$s['id']] ?? ''); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-cyan text-dark fw-semibold">Save Marks</button>
            </form>
        </div>
    </div>
<?php endif; ?>
