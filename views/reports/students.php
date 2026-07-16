
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/reports/students" method="GET" class="row g-3">
            <div class="col-md-10">
                <select class="form-select" name="class_id" onchange="this.form.submit()">
                    <option value="">-- All Classes --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $classId == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo $c['class_name'] . ' ' . $c['section']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-cyan w-100 fw-semibold" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print</button>
            </div>
        </form>
    </div>
</div>

<div class="card bg-dark-card border-secondary shadow-sm printable-area">
    <div class="card-body">
        <h4 class="text-white fw-bold text-center mb-1"><?php echo APP_NAME; ?></h4>
        <h6 class="text-muted text-center mb-4">Student Registry List</h6>
        <div class="table-responsive">
            <table class="table table-striped table-dark">
                <thead>
                    <tr>
                        <th>Adm No</th>
                        <th>Student Name</th>
                        <th>Gender</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo $s['admission_number']; ?></td>
                            <td><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></td>
                            <td><?php echo ucfirst($s['gender']); ?></td>
                            <td><?php echo ucfirst($s['education_level']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
