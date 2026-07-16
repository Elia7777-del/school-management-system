
<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary shadow-sm text-center py-4 h-100">
            <div class="fs-1 text-cyan mb-2"><i class="bi bi-people-fill"></i></div>
            <h5 class="text-white fw-bold mb-1">Student Rosters</h5>
            <p class="text-muted small mb-4">View active student details filtered by class</p>
            <a href="<?php echo BASE_URL; ?>/reports/students" class="btn btn-sm btn-cyan text-dark fw-semibold">Generate Roster</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary shadow-sm text-center py-4 h-100">
            <div class="fs-1 text-green mb-2"><i class="bi bi-person-badge-fill"></i></div>
            <h5 class="text-white fw-bold mb-1">Teacher Directory</h5>
            <p class="text-muted small mb-4">Staff registry and specialization summary</p>
            <a href="<?php echo BASE_URL; ?>/reports/teachers" class="btn btn-sm btn-green text-white fw-semibold">Generate Directory</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark-card border-secondary shadow-sm text-center py-4 h-100">
            <div class="fs-1 text-teal mb-2"><i class="bi bi-cash-stack"></i></div>
            <h5 class="text-white fw-bold mb-1">Fee Statements</h5>
            <p class="text-muted small mb-4">Payment tracking, total revenue statement</p>
            <a href="<?php echo BASE_URL; ?>/reports/fees" class="btn btn-sm btn-teal text-white fw-semibold">Generate Statement</a>
        </div>
    </div>
</div>
