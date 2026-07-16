
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title text-white mb-0">Subjects Directory</h5>
        <a href="<?php echo BASE_URL; ?>/subjects/create" class="btn btn-sm btn-cyan text-dark fw-semibold"><i class="bi bi-plus-lg me-1"></i>Add Subject</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $s): ?>
                        <tr>
                            <td><?php echo $s['subject_code']; ?></td>
                            <td><?php echo $s['subject_name']; ?></td>
                            <td><?php echo ucfirst($s['education_level']); ?></td>
                            <td><?php echo getStatusBadge($s['status']); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/subjects/edit?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-warning me-1">Edit</a>
                                <a href="<?php echo BASE_URL; ?>/subjects/delete?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this subject?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
