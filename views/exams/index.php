
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title text-white mb-0">Examinations</h5>
        <a href="<?php echo BASE_URL; ?>/exams/create" class="btn btn-sm btn-cyan text-dark fw-semibold"><i class="bi bi-plus-lg me-1"></i>Create Exam</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($exams)): ?>
                        <tr><td colspan="6" class="text-center text-muted">No exams registered.</td></tr>
                    <?php else: ?>
                        <?php foreach ($exams as $e): ?>
                            <tr>
                                <td><?php echo $e['exam_name']; ?></td>
                                <td><?php echo $e['class_name'] . ' ' . $e['section']; ?></td>
                                <td><?php echo ucfirst($e['exam_type']); ?></td>
                                <td><?php echo formatDate($e['start_date']); ?></td>
                                <td><?php echo getStatusBadge($e['status']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/exams/marks?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-cyan me-1">Enter Marks</a>
                                    <a href="<?php echo BASE_URL; ?>/exams/results?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-success me-1">View Results</a>
                                    <a href="<?php echo BASE_URL; ?>/exams/delete?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this exam?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
