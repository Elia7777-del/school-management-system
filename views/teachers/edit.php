
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
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Qualification</label>
                            <input type="text" class="form-control" name="qualification" value="<?php echo htmlspecialchars($teacher['qualification']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Link System Account</label>
                            <select class="form-select border-secondary bg-dark text-white" name="user_id">
                                <option value="">-- No Account --</option>
                                <?php foreach ($allUsers as $u): ?>
                                    <?php if (strpos(strtolower($u['role_name']), 'teacher') !== false): ?>
                                        <option value="<?php echo $u['id']; ?>" <?php echo $teacher['user_id'] == $u['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u['username']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1">Link this teacher to their system user account.</small>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Assign Teaching Subjects</label>
                        <div class="card bg-dark border-secondary">
                            <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                <div class="row g-2">
                                    <?php foreach ($allSubjects as $subject): ?>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input border-secondary" type="checkbox" name="subjects[]" 
                                                       value="<?php echo $subject['id']; ?>" id="sub_<?php echo $subject['id']; ?>"
                                                       <?php echo in_array($subject['id'], $assignedSubjectIds) ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-white" for="sub_<?php echo $subject['id']; ?>">
                                                    <?php echo htmlspecialchars($subject['subject_name'] . ' (' . $subject['subject_code'] . ')'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Select all subjects this teacher is qualified to teach across the school.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Assign Classes & Roles</label>
                        <div class="card bg-dark border-secondary">
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <div class="table-responsive">
                                    <table class="table table-dark-custom table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Class</th>
                                                <th class="text-center">Teaches in Class?</th>
                                                <th class="text-center">Class Teacher?</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($allClasses as $class): ?>
                                                <?php $isAssigned = isset($assignedClasses[$class['id']]); ?>
                                                <?php $isClassTeacher = $isAssigned ? $assignedClasses[$class['id']] : 0; ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?></td>
                                                    <td class="text-center">
                                                        <input class="form-check-input border-secondary class-checkbox" type="checkbox" name="classes[]" 
                                                               value="<?php echo $class['id']; ?>" id="class_<?php echo $class['id']; ?>"
                                                               <?php echo $isAssigned ? 'checked' : ''; ?>
                                                               onchange="document.getElementById('ct_<?php echo $class['id']; ?>').disabled = !this.checked; if(!this.checked) document.getElementById('ct_<?php echo $class['id']; ?>').checked = false;">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input border-secondary" type="checkbox" name="class_teacher[<?php echo $class['id']; ?>]" 
                                                               value="1" id="ct_<?php echo $class['id']; ?>"
                                                               <?php echo $isClassTeacher ? 'checked' : ''; ?>
                                                               <?php echo !$isAssigned ? 'disabled' : ''; ?>>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Check "Teaches in Class?" to allow the teacher to enter marks for this class. Check "Class Teacher?" if they are the head of this class.</small>
                    </div>

                    <button type="submit" class="btn btn-green text-white fw-semibold mt-3"><i class="bi bi-save me-2"></i>Update Teacher</button>
                </form>
            </div>
        </div>
    </div>
</div>
