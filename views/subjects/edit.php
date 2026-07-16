
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Edit Subject</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/subjects/update" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" value="<?php echo $subject['subject_name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Subject Code</label>
                        <input type="text" class="form-control" name="subject_code" value="<?php echo $subject['subject_code']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Education Level</label>
                        <select class="form-select" name="education_level" required>
                            <option value="primary" <?php echo $subject['education_level'] === 'primary' ? 'selected' : ''; ?>>Primary (Std I-VII)</option>
                            <option value="secondary" <?php echo $subject['education_level'] === 'secondary' ? 'selected' : ''; ?>>Secondary (Form I-IV)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" <?php echo $subject['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $subject['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Update Subject</button>
                </form>
            </div>
        </div>
    </div>
</div>
