
document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar elements ──────────────────────────────────────────
    const sidebar       = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose  = document.getElementById('sidebar-close');

    // Create overlay element dynamically
    let overlay = document.getElementById('sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'sidebar-overlay';
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    function openSidebar() {
        if (sidebar) sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent background scroll
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Open sidebar on hamburger click
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            openSidebar();
        });
    }

    // Close sidebar on X button
    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }

    // Close sidebar when clicking the overlay backdrop
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar when a nav link is clicked on mobile
    if (sidebar) {
        sidebar.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });
    }

    // On resize to desktop, ensure sidebar is always shown
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) {
            closeSidebar(); // reset any lingering mobile state
            document.body.style.overflow = '';
        }
    });

    // ── Auto-dismiss Flash Alerts ─────────────────────────────────
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) { /* ignore if already gone */ }
        }, 5000);
    });

    // ── Form Validation ───────────────────────────────────────────
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ── Make all tables inside .card responsive ───────────────────
    document.querySelectorAll('.card table, .table-responsive-auto table').forEach(function (table) {
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });

});
