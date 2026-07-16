
<?php if (isLoggedIn()): ?>
        </main>
    </div>
<?php endif; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isLoggedIn() && (isActiveMenu('dashboard') || isset($loadCharts))): ?>
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
    
    <!-- Custom Application JS -->
    <script src="<?php echo BASE_URL; ?>/js/app.js"></script>
</body>
</html>
