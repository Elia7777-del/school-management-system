<?php
require_once APP_ROOT . '/models/School.php';

class SchoolController {
    private School $schoolModel;

    public function __construct() {
        requireRole('system_admin');
        $this->schoolModel = new School();
    }

    /** List all schools */
    public function index() {
        $schools = $this->schoolModel->getAll();
        $totalSchools = $this->schoolModel->getCount();
        $pageTitle = 'Manage Schools';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/schools/index.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    /** Show create school form */
    public function create() {
        $pageTitle = 'Register New School';
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/schools/create.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    /** Store new school */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('schools');
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'CSRF validation failed.');
            redirect('schools/create');
        }

        $name    = sanitize($_POST['name'] ?? '');
        $email   = sanitize($_POST['email'] ?? '');
        $phone   = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $plan    = sanitize($_POST['plan'] ?? 'trial');
        $endDate = sanitize($_POST['end_date'] ?? '');

        if (empty($name) || empty($email) || empty($endDate)) {
            setFlash('error', 'School name, email, and subscription end date are required.');
            redirect('schools/create');
        }

        // Admin credentials
        $adminUsername = sanitize($_POST['admin_username'] ?? '');
        $adminPassword = $_POST['admin_password'] ?? '';
        if (empty($adminUsername) || empty($adminPassword)) {
            setFlash('error', 'Admin username and password are required.');
            redirect('schools/create');
        }

        $db = Database::getInstance()->getConnection();

        // Check admin username uniqueness
        $exists = $db->prepare("SELECT id FROM users WHERE username = ?");
        $exists->execute([$adminUsername]);
        if ($exists->fetch()) {
            setFlash('error', "Username '$adminUsername' is already taken.");
            redirect('schools/create');
        }

        // Create school
        $schoolId = $this->schoolModel->create([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'address' => $address,
            'status'  => 'active',
        ]);

        // Add subscription
        $amountPaid = (float)($_POST['amount_paid'] ?? 0);
        $payRef     = sanitize($_POST['payment_reference'] ?? '');
        $this->schoolModel->addSubscription([
            'school_id'         => $schoolId,
            'plan'              => $plan,
            'start_date'        => date('Y-m-d'),
            'end_date'          => $endDate,
            'amount_paid'       => $amountPaid,
            'currency'          => 'TZS',
            'payment_reference' => $payRef,
            'created_by'        => currentUserId(),
        ]);

        // Create school admin user
        $this->schoolModel->createAdminUser($schoolId, [
            'username' => $adminUsername,
            'email'    => $email,
            'password' => $adminPassword,
        ]);

        setFlash('success', "School '$name' registered successfully! Admin login: $adminUsername");
        redirect('schools');
    }

    /** Show edit form */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $school = $this->schoolModel->findById($id);
        if (!$school) {
            setFlash('error', 'School not found.');
            redirect('schools');
        }
        $subscriptions = $this->schoolModel->getSubscriptions($id);
        $pageTitle = 'Edit School: ' . htmlspecialchars($school['name']);
        require_once APP_ROOT . '/views/layouts/header.php';
        require_once APP_ROOT . '/views/schools/edit.php';
        require_once APP_ROOT . '/views/layouts/footer.php';
    }

    /** Update school */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('schools');
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'CSRF validation failed.');
            redirect('schools');
        }

        $id = (int)($_POST['id'] ?? 0);
        $school = $this->schoolModel->findById($id);
        if (!$school) {
            setFlash('error', 'School not found.');
            redirect('schools');
        }

        $this->schoolModel->update($id, [
            'name'    => sanitize($_POST['name'] ?? ''),
            'slug'    => sanitize($_POST['slug'] ?? ''),
            'email'   => sanitize($_POST['email'] ?? ''),
            'phone'   => sanitize($_POST['phone'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'status'  => sanitize($_POST['status'] ?? 'active'),
        ]);

        setFlash('success', 'School updated successfully.');
        redirect('schools/edit?id=' . $id);
    }

    /** Toggle school status (active/suspended) */
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('schools');
        $id = (int)($_POST['id'] ?? 0);
        $school = $this->schoolModel->findById($id);
        if (!$school) { setFlash('error', 'School not found.'); redirect('schools'); }

        $newStatus = $school['status'] === 'active' ? 'suspended' : 'active';
        $this->schoolModel->update($id, array_merge($school, ['status' => $newStatus]));

        $label = $newStatus === 'active' ? 'activated' : 'suspended';
        setFlash('success', "School '{$school['name']}' has been $label.");
        redirect('schools');
    }

    /** Add a new subscription to a school */
    public function addSubscription() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('schools');
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'CSRF validation failed.'); redirect('schools');
        }

        $schoolId = (int)($_POST['school_id'] ?? 0);
        $school = $this->schoolModel->findById($schoolId);
        if (!$school) { setFlash('error', 'School not found.'); redirect('schools'); }

        $this->schoolModel->addSubscription([
            'school_id'         => $schoolId,
            'plan'              => sanitize($_POST['plan'] ?? 'basic'),
            'start_date'        => sanitize($_POST['start_date'] ?? date('Y-m-d')),
            'end_date'          => sanitize($_POST['end_date'] ?? ''),
            'amount_paid'       => (float)($_POST['amount_paid'] ?? 0),
            'currency'          => 'TZS',
            'payment_reference' => sanitize($_POST['payment_reference'] ?? ''),
            'notes'             => sanitize($_POST['notes'] ?? ''),
            'created_by'        => currentUserId(),
        ]);

        // Reactivate school if it was expired
        if ($school['status'] === 'expired' || $school['status'] === 'suspended') {
            $this->schoolModel->update($schoolId, array_merge($school, ['status' => 'active']));
        }

        setFlash('success', "New subscription added for '{$school['name']}'.");
        redirect('schools/edit?id=' . $schoolId);
    }
}
