
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Create Examination</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/exams/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Exam Name</label>
                        <input type="text" class="form-control" name="exam_name" placeholder="e.g. Midterm Term I" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Exam Type</label>
                        <select class="form-select" name="exam_type" required>
                            <option value="midterm">Midterm Exam</option>
                            <option value="terminal">Terminal Exam</option>
                            <option value="annual">Annual Exam</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Class</label>
                        <select class="form-select" name="class_id" required>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['class_name'] . ' ' . $c['section']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Start Date</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">End Date</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Create Exam</button>
                </form>
            </div>
        </div>
    </div>
</div>
