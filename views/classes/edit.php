
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Edit Class</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/classes/update" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted">Class Name</label>
                        <input type="text" class="form-control" name="class_name" value="<?php echo $class['class_name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Education Level</label>
                        <select class="form-select" name="education_level" required>
                            <option value="primary" <?php echo $class['education_level'] === 'primary' ? 'selected' : ''; ?>>Primary (Std I-VII)</option>
                            <option value="secondary" <?php echo $class['education_level'] === 'secondary' ? 'selected' : ''; ?>>Secondary (Form I-IV)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Section / Stream</label>
                        <input type="text" class="form-control" name="section" value="<?php echo $class['section']; ?>">
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Update Class</button>
                </form>
            </div>
        </div>
    </div>
</div>
