
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Edit Teacher</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/teachers/update" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="<?php echo $teacher['first_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="<?php echo $teacher['last_name']; ?>" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Phone</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo $teacher['phone']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo $teacher['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $teacher['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Qualification</label>
                        <input type="text" class="form-control" name="qualification" value="<?php echo $teacher['qualification']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-green text-white fw-semibold mt-3"><i class="bi bi-save me-2"></i>Update Teacher</button>
                </form>
            </div>
        </div>
    </div>
</div>
