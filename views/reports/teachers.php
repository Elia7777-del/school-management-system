
<div class="card bg-dark-card border-secondary shadow-sm mb-4 d-print-none">
    <div class="card-body text-end">
        <button type="button" class="btn btn-outline-cyan fw-semibold" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print Report</button>
    </div>
</div>
<div class="card bg-dark-card border-secondary shadow-sm printable-area">
    <div class="card-body">
        <h4 class="text-white fw-bold text-center mb-1"><?php echo APP_NAME; ?></h4>
        <h6 class="text-muted text-center mb-4">Teachers Staff Directory</h6>
        <div class="table-responsive">
            <table class="table table-striped table-dark">
                <thead>
                    <tr>
                        <th>TSC No</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Qualification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td><?php echo $t['teacher_number']; ?></td>
                            <td><?php echo $t['first_name'] . ' ' . $t['last_name']; ?></td>
                            <td><?php echo ucfirst($t['gender']); ?></td>
                            <td><?php echo $t['phone']; ?></td>
                            <td><?php echo $t['qualification']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
