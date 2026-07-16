
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/students" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search by name or admission no..." value="<?php echo $search; ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="class_id">
                    <option value="">-- Filter by Class --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $classFilter == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo $c['class_name'] . ' ' . $c['section']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">-- Filter by Status --</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-cyan text-dark w-100 fw-semibold"><i class="bi bi-search me-2"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title text-white mb-0">Student Roster</h5>
        <a href="<?php echo BASE_URL; ?>/students/create" class="btn btn-sm btn-cyan text-dark fw-semibold"><i class="bi bi-plus-lg me-1"></i>Add Student</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Adm No</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Class</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No students found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo $s['admission_number']; ?></td>
                                <td><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></td>
                                <td><?php echo ucfirst($s['gender']); ?></td>
                                <td><?php echo $s['class_name'] . ' ' . $s['section']; ?></td>
                                <td><?php echo ucfirst($s['education_level']); ?></td>
                                <td><?php echo getStatusBadge($s['status']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/students/show?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-cyan me-1"><i class="bi bi-eye"></i></a>
                                    <a href="<?php echo BASE_URL; ?>/students/edit?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                                    <a href="<?php echo BASE_URL; ?>/students/delete?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this student?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <?php echo getPaginationHtml($pagination['totalPages'], $pagination['currentPage'], BASE_URL . "/students?search={$search}&class_id={$classFilter}&status={$statusFilter}"); ?>
        </div>
    </div>
</div>
