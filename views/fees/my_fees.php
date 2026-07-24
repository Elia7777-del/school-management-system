<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h4 class="text-white fw-bold mb-1"><i class="bi bi-wallet2 me-2 text-cyan"></i>My Fees & Invoices</h4>
        <p class="text-muted mb-0">View your fee statements and pay online instantly.</p>
    </div>
</div>

<div class="row">
    <?php if (empty($invoices)): ?>
        <div class="col-12">
            <div class="card bg-dark-card border-secondary shadow-sm text-center py-5">
                <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                <h5 class="text-white">You are all caught up!</h5>
                <p class="text-muted">You have no pending invoices.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($invoices as $inv): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card bg-dark-card border-secondary shadow-sm h-100">
                    <div class="card-header border-secondary bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <span class="text-cyan fw-bold"><?php echo htmlspecialchars($inv['invoice_number']); ?></span>
                        <?php if ($inv['status'] === 'paid'): ?>
                            <span class="badge bg-success">Paid</span>
                        <?php elseif ($inv['status'] === 'partial'): ?>
                            <span class="badge bg-warning text-dark">Partial</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Pending</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-white mb-3">
                            <small class="text-muted fs-6 d-block mb-1">Amount Due</small>
                            TZS <?php echo number_format($inv['amount'] - $inv['amount_paid'], 2); ?>
                        </h5>
                        
                        <?php if (hasRole('parent') && isset($inv['first_name'])): ?>
                            <div class="mb-3 p-2 rounded" style="background:rgba(13,202,240,.1); border-left:3px solid #0dcaf0;">
                                <small class="text-cyan d-block" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;">Student</small>
                                <span class="text-white fw-bold"><?php echo htmlspecialchars($inv['first_name'] . ' ' . $inv['last_name']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>Total Amount:</span>
                            <span>TZS <?php echo number_format($inv['amount'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mb-3">
                            <span>Amount Paid:</span>
                            <span class="text-success">TZS <?php echo number_format($inv['amount_paid'], 2); ?></span>
                        </div>
                        
                        <?php if ($inv['description']): ?>
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i><?php echo htmlspecialchars($inv['description']); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($inv['due_date']): ?>
                            <p class="text-danger small mb-0 mt-2"><i class="bi bi-calendar-x me-1"></i>Due: <?php echo date('M d, Y', strtotime($inv['due_date'])); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($inv['status'] !== 'paid'): ?>
                        <div class="card-footer border-secondary bg-transparent py-3">
                            <button type="button" class="btn btn-cyan w-100 text-dark fw-bold btn-pay-now" 
                                    data-id="<?php echo $inv['id']; ?>" 
                                    data-amount="<?php echo $inv['amount'] - $inv['amount_paid']; ?>">
                                <i class="bi bi-credit-card me-2"></i>Pay Now
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- AzamPay Checkout Modal -->
<div class="modal fade" id="azamPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark-card border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="bi bi-phone me-2 text-cyan"></i>Mobile Money Checkout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="azamPayForm">
                    <input type="hidden" id="pay_invoice_id" name="invoice_id">
                    <input type="hidden" id="pay_amount" name="amount">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Amount to Pay (TZS)</label>
                        <h3 id="display_amount" class="text-white fw-bold">0.00</h3>
                    </div>
                    
                    <div class="mb-3">
                        <label for="provider" class="form-label text-white">Mobile Network</label>
                        <select class="form-select bg-dark border-secondary text-white" id="provider" name="provider" required>
                            <option value="Mpesa">Vodacom M-Pesa</option>
                            <option value="Tigo">Tigo Pesa</option>
                            <option value="Airtel">Airtel Money</option>
                            <option value="Halopesa">HaloPesa</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="form-label text-white">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted">+255</span>
                            <input type="text" class="form-control bg-dark border-secondary text-white" id="phone" name="phone" placeholder="712345678" required pattern="\d{9}">
                        </div>
                        <small class="text-muted">Enter 9 digits without the leading zero.</small>
                    </div>
                    
                    <div class="alert alert-info bg-dark border-cyan text-cyan">
                        <i class="bi bi-info-circle me-2"></i>You will receive a prompt on your phone to enter your PIN.
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-cyan text-dark fw-bold" id="btnConfirmPay">
                            Confirm Payment
                        </button>
                    </div>
                </form>
                
                <div id="paymentProcessing" class="text-center d-none py-4">
                    <div class="spinner-border text-cyan mb-3" role="status"></div>
                    <h5>Processing Payment...</h5>
                    <p class="text-muted">Please check your phone and enter your PIN.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButtons = document.querySelectorAll('.btn-pay-now');
    const modal = new bootstrap.Modal(document.getElementById('azamPayModal'));
    
    payButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('pay_invoice_id').value = this.dataset.id;
            document.getElementById('pay_amount').value = this.dataset.amount;
            document.getElementById('display_amount').innerText = Number(this.dataset.amount).toLocaleString('en-US', {minimumFractionDigits: 2});
            
            document.getElementById('azamPayForm').classList.remove('d-none');
            document.getElementById('paymentProcessing').classList.add('d-none');
            
            modal.show();
        });
    });
    
    document.getElementById('azamPayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const btn = document.getElementById('btnConfirmPay');
        const formData = new FormData(form);
        
        form.classList.add('d-none');
        document.getElementById('paymentProcessing').classList.remove('d-none');
        
        fetch('<?php echo BASE_URL; ?>/api/azampay/checkout', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Simulate waiting for webhook to process
                setTimeout(() => {
                    window.location.reload();
                }, 5000);
            } else {
                alert(data.message || 'Payment initiation failed.');
                form.classList.remove('d-none');
                document.getElementById('paymentProcessing').classList.add('d-none');
            }
        })
        .catch(err => {
            alert('An error occurred.');
            form.classList.remove('d-none');
            document.getElementById('paymentProcessing').classList.add('d-none');
        });
    });
});
</script>
