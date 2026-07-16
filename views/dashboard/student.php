
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card bg-dark-card border-secondary stat-card stat-blue">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Attendance Rate (Current Term)</h6>
                    <h3 class="text-white fw-bold mb-0">
                        <?php
                        $rate = $attendanceRate['total'] > 0 ? round((($attendanceRate['present'] + $attendanceRate['late']) / $attendanceRate['total']) * 100, 1) : 0;
                        echo $rate . '%';
                        ?>
                    </h3>
                </div>
                <div class="stat-icon bg-blue-translucent"><i class="bi bi-calendar-check text-blue"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm mb-4">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0"><i class="bi bi-award-fill me-2 text-warning"></i>Recent Examination Results</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentResults)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No examination results available.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentResults as $r): ?>
                                    <tr>
                                        <td><?php echo $r['exam_name']; ?></td>
                                        <td><?php echo $r['subject_name']; ?></td>
                                        <td><?php echo $r['marks_obtained']; ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $r['grade']; ?></span></td>
                                        <td><?php echo $r['remarks']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
