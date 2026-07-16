
<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Academic Years</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Year Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($years as $y): ?>
                                <tr>
                                    <td><?php echo $y['year_name']; ?></td>
                                    <td><?php echo formatDate($y['start_date']); ?></td>
                                    <td><?php echo formatDate($y['end_date']); ?></td>
                                    <td><?php echo $y['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/academic-years/terms?year_id=<?php echo $y['id']; ?>" class="btn btn-sm btn-outline-cyan me-2">Manage Terms</a>
                                        <?php if (!$y['is_active']): ?>
                                            <a href="<?php echo BASE_URL; ?>/academic-years/activate?id=<?php echo $y['id']; ?>" class="btn btn-sm btn-success text-dark fw-semibold">Activate</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Add Academic Year</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/academic-years/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Year Name</label>
                        <input type="text" class="form-control" name="year_name" placeholder="e.g. 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">End Date</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Create Year</button>
                </form>
            </div>
        </div>
    </div>
</div>
