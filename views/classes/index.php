
<div class="card bg-dark-card border-secondary shadow-sm mb-4">
    <div class="card-header border-secondary bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title text-white mb-0">Manage Classes</h5>
        <a href="<?php echo BASE_URL; ?>/classes/create" class="btn btn-sm btn-cyan text-dark fw-semibold"><i class="bi bi-plus-lg me-1"></i>Create Class</a>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <?php foreach ($classes as $c): ?>
                <div class="col-md-4">
                    <div class="card bg-dark border-secondary h-100">
                        <div class="card-body">
                            <h5 class="text-white fw-bold mb-1"><?php echo $c['class_name'] . ' ' . $c['section']; ?></h5>
                            <p class="text-muted small mb-3">Level: <?php echo ucfirst($c['education_level']); ?></p>
                            <div class="d-flex justify-content-between text-muted small mb-3">
                                <span>Students: <strong class="text-white"><?php echo $c['student_count']; ?></strong></span>
                                <span>Subjects: <strong class="text-white"><?php echo $c['subjects_count']; ?></strong></span>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="<?php echo BASE_URL; ?>/classes/edit?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-warning me-2">Edit</a>
                                <a href="<?php echo BASE_URL; ?>/classes/delete?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this class?')">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
