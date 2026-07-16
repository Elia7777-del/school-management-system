
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Create Class</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/classes/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Class Name</label>
                        <input type="text" class="form-control" name="class_name" placeholder="e.g. Standard I, Form I" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Education Level</label>
                        <select class="form-select" name="education_level" required>
                            <option value="primary">Primary (Std I-VII)</option>
                            <option value="secondary">Secondary (Form I-IV)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Section / Stream</label>
                        <input type="text" class="form-control" name="section" placeholder="e.g. A, B, Blue">
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Create Class</button>
                </form>
            </div>
        </div>
    </div>
</div>
