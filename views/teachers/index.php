
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title text-white mb-0">Teacher Directory</h5>
        <a href="<?php echo BASE_URL; ?>/teachers/create" class="btn btn-sm btn-green text-white fw-semibold"><i class="bi bi-plus-lg me-1"></i>Add Teacher</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>TSC No</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Qualification</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td><?php echo $t['teacher_number']; ?></td>
                            <td><?php echo $t['first_name'] . ' ' . $t['last_name']; ?></td>
                            <td><?php echo ucfirst($t['gender']); ?></td>
                            <td><?php echo $t['phone']; ?></td>
                            <td><?php echo $t['qualification']; ?></td>
                            <td><?php echo getStatusBadge($t['status']); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/teachers/show?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-green me-1"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo BASE_URL; ?>/teachers/edit?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                                <a href="<?php echo BASE_URL; ?>/teachers/delete?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this teacher?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
