
<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-header border-secondary bg-transparent py-3">
        <h5 class="card-title text-white mb-0">Results Sheet — <?php echo $exam['exam_name']; ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Adm No</th>
                        <th>Student Name</th>
                        <?php foreach ($subjects as $s): ?>
                            <th><?php echo $s['subject_code']; ?></th>
                        <?php endforeach; ?>
                        <th>Total</th>
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr><td colspan="<?php echo count($subjects) + 4; ?>" class="text-center text-muted">No marks entered yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td><?php echo $r['admission_number']; ?></td>
                                <td><?php echo $r['first_name'] . ' ' . $r['last_name']; ?></td>
                                <?php foreach ($subjects as $s): ?>
                                    <td><?php echo $marksMatrix[$r['student_id']][$s['id']] ?? '-'; ?></td>
                                <?php endforeach; ?>
                                <td class="fw-bold"><?php echo $r['total_marks']; ?></td>
                                <td class="fw-bold"><?php echo round($r['average_marks'], 1); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
