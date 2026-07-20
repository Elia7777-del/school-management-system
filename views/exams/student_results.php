
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h4 class="text-white fw-bold mb-1"><i class="bi bi-award me-2 text-cyan"></i>My Examination Results</h4>
        <p class="text-muted mb-0">View your academic performance across all terms and years.</p>
    </div>
</div>

<?php if (empty($groupedResults)): ?>
    <div class="card bg-dark-card border-secondary shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
            <h5 class="text-white">No Results Found</h5>
            <p class="text-muted">You do not have any examination results published yet.</p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($groupedResults as $examKey => $examData): ?>
        <div class="card bg-dark-card border-secondary shadow-sm mb-4">
            <div class="card-header border-secondary bg-transparent py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-white mb-1"><?php echo htmlspecialchars($examData['exam_name']); ?></h5>
                    <small class="text-muted"><?php echo htmlspecialchars($examData['term_name'] . ' - ' . $examData['year_name']); ?></small>
                </div>
                <div>
                    <span class="badge bg-cyan text-dark px-3 py-2">
                        <?php
                        $totalMarks = array_sum(array_column($examData['subjects'], 'marks_obtained'));
                        $avgMarks = count($examData['subjects']) > 0 ? $totalMarks / count($examData['subjects']) : 0;
                        echo 'Average: ' . number_format($avgMarks, 1) . '%';
                        ?>
                    </span>
                    <?php if(isset($examData['rank']) && $examData['rank'] > 0): ?>
                        <span class="badge bg-warning text-dark px-3 py-2 ms-2">
                            Position: <?php echo $examData['rank']; ?> out of <?php echo $examData['total_students']; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th class="text-end">Marks Obtained</th>
                                <th class="text-center">Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examData['subjects'] as $subject): ?>
                                <tr>
                                    <td><code class="text-cyan"><?php echo htmlspecialchars($subject['subject_code']); ?></code></td>
                                    <td class="text-white"><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td class="text-end fw-medium text-white"><?php echo htmlspecialchars($subject['marks_obtained']); ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $gradeClass = 'bg-secondary';
                                        if (in_array($subject['grade'], ['A', 'B'])) $gradeClass = 'bg-success';
                                        elseif ($subject['grade'] == 'C') $gradeClass = 'bg-primary';
                                        elseif ($subject['grade'] == 'D') $gradeClass = 'bg-warning text-dark';
                                        elseif (in_array($subject['grade'], ['E', 'F'])) $gradeClass = 'bg-danger';
                                        ?>
                                        <span class="badge <?php echo $gradeClass; ?>"><?php echo htmlspecialchars($subject['grade']); ?></span>
                                    </td>
                                    <td class="text-muted"><small><?php echo htmlspecialchars($subject['remarks']); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-top border-secondary bg-dark">
                            <tr>
                                <th colspan="2" class="text-end">Total Marks:</th>
                                <th class="text-end text-white fs-5"><?php echo number_format($totalMarks, 1); ?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
