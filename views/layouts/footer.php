
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

    <?php if (isLoggedIn()): ?>
    <script>
        // Strict Security: Auto-logout when user leaves the page or switches tabs
        let logoutTimer;
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                // If they switch tabs or minimize, log them out after a short delay (1 second)
                // This delay prevents false positives during normal page navigation within the system.
                logoutTimer = setTimeout(() => {
                    fetch('<?php echo BASE_URL; ?>/logout', { method: 'GET' }).then(() => {
                        window.location.href = '<?php echo BASE_URL; ?>/login';
                    });
                }, 1500);
            } else {
                clearTimeout(logoutTimer);
            }
        });

        // Prevent browser back button from showing cached logged-in pages after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
