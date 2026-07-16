
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Register New Student</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/students/store" method="POST">
                    <?php echo csrfField(); ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Education Level</label>
                            <select class="form-select" name="education_level" required>
                                <option value="primary">Primary (Std I-VII)</option>
                                <option value="secondary">Secondary (Form I-IV)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Class</label>
                            <select class="form-select" name="class_id" required>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo $c['class_name'] . ' ' . $c['section']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Save Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
