
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark-card border-secondary shadow-sm">
            <div class="card-header border-secondary bg-transparent py-3">
                <h5 class="card-title text-white mb-0">Add Teacher Details</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/teachers/store" method="POST">
                    <?php echo csrfField(); ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Phone Number</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Qualification</label>
                        <input type="text" class="form-control" name="qualification" required>
                    </div>
                    <button type="submit" class="btn btn-green text-white fw-semibold mt-3"><i class="bi bi-save me-2"></i>Save Teacher</button>
                </form>
            </div>
        </div>
    </div>
</div>
