
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Create Subject</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/subjects/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Subject Code</label>
                        <input type="text" class="form-control" name="subject_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Education Level</label>
                        <select class="form-select" name="education_level" required>
                            <option value="primary">Primary (Std I-VII)</option>
                            <option value="secondary">Secondary (Form I-IV)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Save Subject</button>
                </form>
            </div>
        </div>
    </div>
</div>
