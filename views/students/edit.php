
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Edit Student Details</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/students/update" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="<?php echo $student['first_name']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" value="<?php echo $student['middle_name']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="<?php echo $student['last_name']; ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Admission Number / Register Number</label>
                            <input type="text" class="form-control" name="admission_number" 
                                   value="<?php echo htmlspecialchars($student['admission_number']); ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="male" <?php echo $student['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $student['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" value="<?php echo $student['date_of_birth']; ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Education Level</label>
                            <select class="form-select" name="education_level" required>
                                <option value="primary" <?php echo $student['education_level'] === 'primary' ? 'selected' : ''; ?>>Primary (Std I-VII)</option>
                                <option value="secondary" <?php echo $student['education_level'] === 'secondary' ? 'selected' : ''; ?>>Secondary (Form I-IV)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Class</label>
                            <select class="form-select" name="class_id" required>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $student['class_id'] == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo $c['class_name'] . ' ' . $c['section']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="active" <?php echo $student['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $student['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Update Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
