<?php
require_once '../../config/db.php';
require_once '../../includes/session.php';
checkRole(['admin', 'Admin', 1]);

// Fetch warehouses for dropdown
$warehouses = [];
$stmt = $pdo->query("SELECT warehouse_id, name FROM Warehouse");
$warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $warehouse_id = $_POST['warehouse_id'] ?: null;
    $contact_email = trim($_POST['contact_email']);
    $status = trim($_POST['status']);
    $subscription_cost = $_POST['subscription_cost'] ?: null;
    $billing_cycle = trim($_POST['billing_cycle']);
    $notes = trim($_POST['notes']);

    try {
        $query = "INSERT INTO ThirdPartyServices 
            (name, type, warehouse_id, contact_email, status, subscription_cost, billing_cycle, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $name, $type, $warehouse_id, $contact_email, $status, $subscription_cost, $billing_cycle, $notes
        ]);
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Add New Third Party Service</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Service Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="Repricing">Repricing</option>
                <option value="Analytics">Analytics</option>
                <option value="Freight">Freight</option>
                <option value="Automation">Automation</option>
                <option value="Prep Center">Prep Center</option>
            </select>
        </div>
        <div class="form-group">
            <label>Warehouse</label>
            <select name="warehouse_id" class="form-control">
                <option value="">None</option>
                <?php foreach ($warehouses as $w): ?>
                    <option value="<?= htmlspecialchars($w['warehouse_id']) ?>"><?= htmlspecialchars($w['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Contact Email</label>
            <input type="email" name="contact_email" class="form-control">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label>Subscription Cost</label>
            <input type="number" step="0.01" name="subscription_cost" class="form-control">
        </div>
        <div class="form-group">
            <label>Billing Cycle</label>
            <select name="billing_cycle" class="form-control">
                <option value="">None</option>
                <option value="Monthly">Monthly</option>
                <option value="Annually">Annually</option>
                <option value="One-Time">One-Time</option>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Service</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
