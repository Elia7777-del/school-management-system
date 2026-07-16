
<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Fee Structures</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($structures)): ?>
                                <tr><td colspan="3" class="text-center text-muted">No fee structures defined.</td></tr>
                            <?php else: ?>
                                <?php foreach ($structures as $s): ?>
                                    <tr>
                                        <td><?php echo $s['class_name']; ?></td>
                                        <td><?php echo $s['description']; ?></td>
                                        <td><?php echo formatCurrency($s['amount']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Add Fee Structure</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/fees/structure/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Class</label>
                        <select class="form-select" name="class_id" required>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['class_name'] . ' ' . $c['section']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Amount Required (TZS)</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Description</label>
                        <input type="text" class="form-control" name="description" placeholder="e.g. Tuition Fee Term I" required>
                    </div>
                    <button type="submit" class="btn btn-cyan text-dark fw-semibold mt-3"><i class="bi bi-save me-2"></i>Save Structure</button>
                </form>
            </div>
        </div>
    </div>
</div>
