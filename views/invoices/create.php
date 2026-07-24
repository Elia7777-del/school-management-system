<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h4 class="text-white fw-bold mb-1"><i class="bi bi-plus-circle me-2 text-cyan"></i>Create Invoice</h4>
        <p class="text-muted mb-0">Generate a new fee invoice for a student.</p>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>/invoices" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Invoices
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-body p-4">
                <form action="<?php echo BASE_URL; ?>/invoices/store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    
                    <div class="mb-4">
                        <label for="student_id" class="form-label text-white">Select Student</label>
                        <select class="form-select bg-dark border-secondary text-white" id="student_id" name="student_id" required>
                            <option value="">-- Choose Student --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['admission_number'] . ' - ' . $student['first_name'] . ' ' . $student['last_name'] . ' (' . $student['class_name'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label text-white">Invoice Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted">TZS</span>
                            <input type="number" class="form-control bg-dark border-secondary text-white" id="amount" name="amount" min="1" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="due_date" class="form-label text-white">Due Date (Optional)</label>
                        <input type="date" class="form-control bg-dark border-secondary text-white" id="due_date" name="due_date">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label text-white">Description</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" id="description" name="description" placeholder="e.g. Term 1 School Fees">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-cyan text-dark"><i class="bi bi-save me-1"></i>Create Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
