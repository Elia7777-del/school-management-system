<?php
$statusColor = ['active' => 'success', 'suspended' => 'danger', 'expired' => 'warning'];
$planBadge   = ['trial' => 'secondary', 'basic' => 'info', 'premium' => 'primary', 'enterprise' => 'warning'];
$subColor    = ['active' => 'success', 'expiring_soon' => 'warning', 'expired' => 'danger', 'no_subscription' => 'secondary'];
?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white fw-bold mb-1"><i class="bi bi-building me-2 text-cyan"></i> School Management</h4>
        <p class="text-muted mb-0">Manage all schools and their subscriptions on this platform.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/schools/create" class="btn btn-cyan text-dark fw-bold">
        <i class="bi bi-plus-circle me-2"></i> Register New School
    </a>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <?php
    $totalActive    = count(array_filter($schools, fn($s) => $s['status'] === 'active'));
    $totalSuspended = count(array_filter($schools, fn($s) => $s['status'] === 'suspended'));
    $totalExpiring  = count(array_filter($schools, fn($s) => ($s['subscription_status'] ?? '') === 'expiring_soon'));
    ?>
    <div class="col-md-3">
        <div class="card bg-dark-card border-secondary text-center py-3">
            <h2 class="text-cyan fw-bold mb-0"><?php echo count($schools); ?></h2>
            <small class="text-muted">Total Schools</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark-card border-secondary text-center py-3">
            <h2 class="text-success fw-bold mb-0"><?php echo $totalActive; ?></h2>
            <small class="text-muted">Active</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark-card border-secondary text-center py-3">
            <h2 class="text-danger fw-bold mb-0"><?php echo $totalSuspended; ?></h2>
            <small class="text-muted">Suspended</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark-card border-secondary text-center py-3">
            <h2 class="text-warning fw-bold mb-0"><?php echo $totalExpiring; ?></h2>
            <small class="text-muted">Expiring Soon</small>
        </div>
    </div>
</div>

<!-- Schools Table -->
<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="border-secondary">
                    <tr>
                        <th class="ps-4">School</th>
                        <th>Plan</th>
                        <th>Subscription</th>
                        <th>Users</th>
                        <th>Students</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schools)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">No schools registered yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($schools as $school): ?>
                            <?php
                            $subStat = $school['subscription_status'] ?? 'no_subscription';
                            $sc      = $subColor[$subStat] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-white"><?php echo htmlspecialchars($school['name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($school['email'] ?? '—'); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $planBadge[$school['plan'] ?? 'trial'] ?? 'secondary'; ?>">
                                        <?php echo strtoupper($school['plan'] ?? 'NONE'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($school['subscription_end']): ?>
                                        <span class="badge bg-<?php echo $sc; ?>">
                                            Expires: <?php echo date('d M Y', strtotime($school['subscription_end'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Subscription</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="text-muted"><?php echo $school['user_count']; ?></span></td>
                                <td><span class="text-muted"><?php echo $school['student_count']; ?></span></td>
                                <td>
                                    <span class="badge bg-<?php echo $statusColor[$school['status']] ?? 'secondary'; ?>">
                                        <?php echo ucfirst($school['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?php echo BASE_URL; ?>/schools/edit?id=<?php echo $school['id']; ?>" 
                                       class="btn btn-sm btn-outline-cyan me-1" title="Edit & Manage">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="<?php echo BASE_URL; ?>/schools/toggle-status" class="d-inline">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="id" value="<?php echo $school['id']; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo $school['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                                title="<?php echo $school['status'] === 'active' ? 'Suspend' : 'Activate'; ?>"
                                                onclick="return confirm('<?php echo $school['status'] === 'active' ? 'Suspend' : 'Activate'; ?> this school?')">
                                            <i class="bi bi-<?php echo $school['status'] === 'active' ? 'pause-circle' : 'play-circle'; ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
