
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Edit User Account</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/users/update" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted">Username</label>
                        <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Role</label>
                        <select class="form-select" name="role_id" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['id']; ?>" <?php echo $user['role_id'] == $r['id'] ? 'selected' : ''; ?>>
                                    <?php echo ucfirst(str_replace('_', ' ', $r['role_name'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Update Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
