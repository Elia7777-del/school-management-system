
<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">System Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?php echo $u['username']; ?></td>
                                    <td><?php echo $u['email']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $u['role_name'])); ?></span></td>
                                    <td><?php echo getStatusBadge($u['status']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/users/edit?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-warning me-2">Edit</a>
                                        <a href="<?php echo BASE_URL; ?>/users/delete?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deactivate account?')">Deactivate</a>
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
                <h5 class="card-title text-white mb-0">Add User Account</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/users/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Role</label>
                        <select class="form-select" name="role_id" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo ucfirst(str_replace('_', ' ', $r['role_name'])); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Create User</button>
                </form>
            </div>
        </div>
    </div>
</div>
