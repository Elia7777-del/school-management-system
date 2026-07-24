<?php
// ─── Parent Dashboard ──────────────────────────────────────────────────────
$scope = "parent";
?>

<style>
.child-card { transition: transform .2s ease, box-shadow .2s ease; }
.child-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.45) !important; }
.comment-bubble {
    background: rgba(255,255,255,.05);
    border-left: 3px solid var(--cyan,#0dcaf0);
    border-radius: 0 .5rem .5rem 0;
    padding: .65rem 1rem;
    margin-bottom: .5rem;
}
.stat-pill {
    background: rgba(255,255,255,.07);
    border-radius: 2rem;
    padding: .25rem .8rem;
    font-size: .8rem;
}
.section-title {
    font-size: .75rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: .6rem;
}
.comment-form textarea { resize: vertical; min-height: 90px; }
</style>

<?php if (empty($childrenData)): ?>
<div class="d-flex flex-column align-items-center justify-content-center py-5 text-center">
    <i class="bi bi-person-x-fill fs-1 text-muted mb-3"></i>
    <h5 class="text-white">No Linked Students Found</h5>
    <p class="text-muted">Please contact the school administrator to link your account to your child.</p>
</div>
<?php else: ?>

<!-- ── welcome strip ──────────────────────────────────────── -->
<div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3"
     style="background:linear-gradient(135deg,rgba(13,202,240,.12),rgba(13,202,240,.03));border:1px solid rgba(13,202,240,.2)">
    <div class="rounded-circle d-flex align-items-center justify-content-center"
         style="width:52px;height:52px;background:rgba(13,202,240,.18)">
        <i class="bi bi-person-hearts fs-3 text-cyan"></i>
    </div>
    <div>
        <h5 class="text-white mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h5>
        <small class="text-muted">Monitoring <?php echo count($childrenData); ?> student(s)</small>
    </div>
</div>

<!-- ── children cards ─────────────────────────────────────── -->
<div class="row g-4">
<?php foreach ($childrenData as $childData): ?>
<?php $c = $childData['student']; ?>

<div class="col-12">
    <div class="card bg-dark-card border-secondary shadow child-card">

        <!-- card header ──────────────────────── -->
        <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:46px;height:46px;background:rgba(13,202,240,.15)">
                    <i class="bi bi-person-fill fs-4 text-cyan"></i>
                </div>
                <div>
                    <h5 class="text-white mb-0"><?php echo htmlspecialchars($c['first_name'] . ' ' . $c['last_name']); ?></h5>
                    <small class="text-muted"><?php echo htmlspecialchars($c['class_name'] . ' ' . $c['section']); ?></small>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="stat-pill text-muted"><i class="bi bi-hash me-1"></i><?php echo htmlspecialchars($c['admission_number']); ?></span>
                <span class="stat-pill text-<?php echo $childData['balance'] > 0 ? 'danger' : 'success'; ?>">
                    <i class="bi bi-wallet2 me-1"></i><?php echo formatCurrency($childData['balance']); ?> deni
                </span>
                <span class="stat-pill text-<?php echo $c['status'] === 'active' ? 'success' : 'warning'; ?>">
                    <i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i><?php echo ucfirst($c['status']); ?>
                </span>
            </div>
        </div><!-- /card-header -->

        <div class="card-body">
            <div class="row g-4">

                <!-- ── Attendance ─────────────────── -->
                <div class="col-md-4">
                    <p class="section-title"><i class="bi bi-calendar-check me-1"></i>Attendance</p>
                    <?php $att = $childData['attendance']; ?>
                    <?php if (!empty($att) && ($att['present'] + $att['absent'] + $att['late']) > 0): ?>
                    <?php
                        $total   = ($att['present'] ?? 0) + ($att['absent'] ?? 0) + ($att['late'] ?? 0);
                        $pct     = $total > 0 ? round((($att['present'] ?? 0) / $total) * 100) : 0;
                        $color   = $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
                    ?>
                    <div class="d-flex justify-content-between text-muted mb-1" style="font-size:.82rem">
                        <span>Days <?php echo $att['present'] ?? 0; ?>/<?php echo $total; ?></span>
                        <span class="text-<?php echo $color; ?> fw-semibold"><?php echo $pct; ?>%</span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar bg-<?php echo $color; ?>" style="width:<?php echo $pct; ?>%"></div>
                    </div>
                    <?php else: ?>
                    <p class="text-muted small">No attendance records found.</p>
                    <?php endif; ?>
                </div>

                <!-- ── Results ────────────────────── -->
                <div class="col-md-4">
                    <p class="section-title"><i class="bi bi-award me-1"></i>Recent Results</p>
                    <?php if (empty($childData['results'])): ?>
                        <p class="text-muted small">No exam results recorded yet.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                        <?php foreach (array_slice($childData['results'], 0, 4) as $r): ?>
                            <li class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small"><?php echo htmlspecialchars($r['subject_name']); ?></span>
                                <span class="badge" style="background:rgba(13,202,240,.2);color:#0dcaf0">
                                    <?php echo htmlspecialchars($r['grade']); ?> (<?php echo $r['marks_obtained']; ?>%)
                                </span>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- ── Comments summary ───────────── -->
                <div class="col-md-4">
                    <p class="section-title"><i class="bi bi-chat-left-text me-1"></i>My Remarks / Notes</p>
                    <?php if (empty($childData['comments'])): ?>
                        <p class="text-muted small">No remarks submitted yet.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($childData['comments'], 0, 2) as $cmt): ?>
                        <div class="comment-bubble">
                            <p class="text-white small mb-1"><?php echo nl2br(htmlspecialchars($cmt['comment'])); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size:.72rem"><?php echo date('d M Y H:i', strtotime($cmt['created_at'])); ?></span>
                                <a href="<?php echo BASE_URL; ?>/parent/delete-comment?id=<?php echo $cmt['id']; ?>"
                                   class="text-danger" style="font-size:.75rem"
                                   onclick="return confirm('Delete this remark?')">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div><!-- /row -->

            <!-- ══ Comment Form ═══════════════════════════════════════ -->
            <div class="mt-4 pt-3 border-top border-secondary">
                <p class="section-title"><i class="bi bi-pencil-square me-1"></i>Add Remark for <?php echo htmlspecialchars($c['first_name']); ?></p>
                <form action="<?php echo BASE_URL; ?>/parent/store-comment" method="POST" class="comment-form">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="student_id" value="<?php echo $c['id']; ?>">
                    <div class="mb-2">
                        <textarea name="comment" class="form-control bg-dark text-white border-secondary"
                                  placeholder="Write a note or feedback regarding <?php echo htmlspecialchars($c['first_name']); ?>'s progress..."
                                  required></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-cyan text-dark fw-semibold">
                        <i class="bi bi-send-fill me-1"></i>Submit Remark
                    </button>
                    <?php if (count($childData['comments']) > 2): ?>
                    <a href="#all-comments-<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-secondary ms-2"
                       data-bs-toggle="collapse">
                        <i class="bi bi-chat-dots me-1"></i>View All (<?php echo count($childData['comments']); ?>)
                    </a>
                    <?php endif; ?>
                </form>

                <!-- All comments collapse ──────────── -->
                <?php if (count($childData['comments']) > 2): ?>
                <div class="collapse mt-3" id="all-comments-<?php echo $c['id']; ?>">
                    <p class="section-title mt-2">Maoni Yote</p>
                    <?php foreach ($childData['comments'] as $cmt): ?>
                    <div class="comment-bubble">
                        <p class="text-white small mb-1"><?php echo nl2br(htmlspecialchars($cmt['comment'])); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size:.72rem"><?php echo date('d M Y H:i', strtotime($cmt['created_at'])); ?></span>
                            <a href="<?php echo BASE_URL; ?>/parent/delete-comment?id=<?php echo $cmt['id']; ?>"
                               class="text-danger small"
                               onclick="return confirm('Futa maoni haya?')">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div><!-- /comment form -->

        </div><!-- /card-body -->
    </div><!-- /card -->
</div><!-- /col -->

<?php endforeach; ?>
</div><!-- /row -->
<?php endif; ?>
