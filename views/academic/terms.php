
<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Terms for <?php echo $year['year_name']; ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Term Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($terms)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No terms defined yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($terms as $t): ?>
                                    <tr>
                                        <td><?php echo $t['term_name']; ?></td>
                                        <td><?php echo formatDate($t['start_date']); ?></td>
                                        <td><?php echo formatDate($t['end_date']); ?></td>
                                        <td><?php echo $t['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                                        <td>
                                            <?php if (!$t['is_active']): ?>
                                                <a href="<?php echo BASE_URL; ?>/academic-years/terms/activate?id=<?php echo $t['id']; ?>&year_id=<?php echo $yearId; ?>" class="btn btn-sm btn-success text-dark fw-semibold">Activate</a>
                                            <?php endif; ?>
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
                <h5 class="card-title text-white mb-0">Add Term</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/academic-years/terms/store" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="academic_year_id" value="<?php echo $yearId; ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted">Term Name</label>
                        <input type="text" class="form-control" name="term_name" placeholder="e.g. Term I" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">End Date</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Create Term</button>
                </form>
            </div>
        </div>
    </div>
</div>
