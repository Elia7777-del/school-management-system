
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
                            <label class="form-label text-muted">Admission Number / Register Number</label>
                            <input type="text" class="form-control" name="admission_number" 
                                   placeholder="<?php echo $suggestedAdmNumber; ?>" 
                                   value="">
                            <small class="text-muted">Leave empty to auto-generate: <strong><?php echo $suggestedAdmNumber; ?></strong></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Admission Date</label>
                            <input type="date" class="form-control" name="admission_date" value="<?php echo date('Y-m-d'); ?>">
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
                        <label class="form-label text-muted">Assign Parent / Guardian</label>
                        <select class="form-select" name="parent_id">
                            <option value="">-- Select Parent (Optional) --</option>
                            <?php foreach ($parents as $p): ?>
                                <option value="<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) . ' (' . ucfirst($p['relationship']) . ' - ' . $p['phone'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="card bg-dark border-secondary p-3 mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="create_account" id="create_account" value="1" checked onclick="document.getElementById('account_fields').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label text-white fw-semibold" for="create_account">
                                Create User Login Account for Student
                            </label>
                        </div>
                        <div id="account_fields" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Username</label>
                                <input type="text" class="form-control" name="username" placeholder="e.g. std2026_01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="student@school.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Default: Student@123" autocomplete="new-password">
                            </div>
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
