
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h4 class="text-white fw-bold mb-1"><i class="bi bi-shield-check me-2 text-cyan"></i>Activity Audit Logs</h4>
        <p class="text-muted mb-0">Monitor teacher and user activities — logins, logouts, marks entries, attendance, and changes.</p>
    </div>
    <span class="badge bg-cyan text-dark fs-6 px-3 py-2"><?php echo number_format($pagination['total']); ?> Records</span>
</div>

<!-- Filters -->
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/audit" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-dark border-secondary text-white" name="search" 
                           placeholder="Search by username, email, or description..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select bg-dark border-secondary text-white" name="action_type">
                    <option value="">All Action Types</option>
                    <?php foreach ($actionTypes as $type): ?>
                        <option value="<?php echo $type; ?>" <?php echo $actionType === $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-cyan text-dark w-100 fw-semibold"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo BASE_URL; ?>/audit" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle me-1"></i> Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Log Table -->
<div class="card bg-dark-card border-secondary shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Page / URL</th>
                        <th>Date & Time</th>
                        <th>Day</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No activity logs found.</td></tr>
                    <?php else: ?>
                        <?php 
                        $rowNum = $pagination['offset'] + 1;
                        foreach ($logs as $log): 
                        ?>
                            <tr>
                                <td class="text-muted"><?php echo $rowNum++; ?></td>
                                <td>
                                    <span class="fw-medium text-white"><?php echo htmlspecialchars($log['username']); ?></span>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($log['email']); ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $roleBadge = [
                                        'super_admin' => 'bg-danger',
                                        'school_admin' => 'bg-warning text-dark',
                                        'teacher' => 'bg-info text-dark',
                                        'student' => 'bg-success',
                                        'parent' => 'bg-secondary'
                                    ];
                                    $badgeClass = $roleBadge[$log['role_name']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $log['role_name'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $actionBadge = [
                                        'login' => 'bg-success',
                                        'logout' => 'bg-secondary',
                                        'marks_entry' => 'bg-primary',
                                        'attendance' => 'bg-info text-dark',
                                        'data_change' => 'bg-warning text-dark',
                                        'page_view' => 'bg-dark border border-secondary'
                                    ];
                                    $aBadge = $actionBadge[$log['action_type']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $aBadge; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $log['action_type'])); ?>
                                    </span>
                                </td>
                                <td class="text-muted" style="max-width: 250px;">
                                    <small><?php echo htmlspecialchars($log['description']); ?></small>
                                </td>
                                <td><code class="text-cyan"><?php echo htmlspecialchars($log['ip_address']); ?></code></td>
                                <td class="text-muted" style="max-width: 200px;">
                                    <small class="text-break"><?php echo htmlspecialchars($log['request_url']); ?></small>
                                </td>
                                <td class="text-nowrap">
                                    <small>
                                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                                        <?php echo date('d M Y', strtotime($log['created_at'])); ?>
                                        <br>
                                        <i class="bi bi-clock me-1 text-muted"></i>
                                        <?php echo date('h:i:s A', strtotime($log['created_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-dark border border-secondary">
                                        <?php echo date('l', strtotime($log['created_at'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    <?php echo getPaginationHtml($pagination['totalPages'], $pagination['currentPage'], $baseUrl); ?>
</div>
