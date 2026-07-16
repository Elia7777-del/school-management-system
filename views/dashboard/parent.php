
<div class="row">
    <?php foreach ($childrenData as $childData): ?>
        <?php $c = $childData['student']; ?>
        <div class="col-md-6 mb-4">
            <div class="card bg-dark-card border-secondary shadow-sm h-100">
                <div class="card-header border-secondary bg-transparent py-3">
                    <h5 class="card-title text-white mb-0"><i class="bi bi-person-fill text-cyan me-2"></i><?php echo $c['first_name'] . ' ' . $c['last_name']; ?></h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Class: <span class="text-white"><?php echo $c['class_name'] . ' ' . $c['section']; ?></span></p>
                    <p class="text-muted mb-2">Admission No: <span class="text-white"><?php echo $c['admission_number']; ?></span></p>
                    <p class="text-muted mb-2">Fee Balance: <span class="text-danger fw-semibold"><?php echo formatCurrency($childData['balance']); ?></span></p>
                    
                    <div class="mt-4">
                        <h6 class="text-cyan mb-2">Recent Results</h6>
                        <ul class="list-group list-group-flush bg-transparent">
                            <?php if (empty($childData['results'])): ?>
                                <li class="list-group-item bg-transparent text-muted px-0">No results recorded.</li>
                            <?php else: ?>
                                <?php foreach (array_slice($childData['results'], 0, 3) as $r): ?>
                                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between px-0">
                                        <span><?php echo $r['subject_name']; ?></span>
                                        <span class="badge bg-secondary"><?php echo $r['grade'] . ' (' . $r['marks_obtained'] . '%)'; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
