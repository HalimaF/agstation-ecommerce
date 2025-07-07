<?php
require_once '../../config/db.php';
require_once '../../includes/session.php';
checkRole(['admin', 'Admin', 1]);

// Validate and sanitize the ID parameter
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo "Service ID is required.";
    exit;
}

// Fetch warehouses for dropdown
$warehouses = [];
$stmt = $pdo->query("SELECT warehouse_id, name FROM Warehouse");
$warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

try {
    // Fetch the service details
    $query = "SELECT * FROM ThirdPartyServices WHERE service_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        echo "Service not found.";
        exit;
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name']);
        $type = trim($_POST['type']);
        $warehouse_id = $_POST['warehouse_id'] ?: null;
        $contact_email = trim($_POST['contact_email']);
        $status = trim($_POST['status']);
        $subscription_cost = $_POST['subscription_cost'] ?: null;
        $billing_cycle = trim($_POST['billing_cycle']);
        $notes = trim($_POST['notes']);

        // Update the service in the database
        $update_query = "UPDATE ThirdPartyServices SET name = ?, type = ?, warehouse_id = ?, contact_email = ?, status = ?, subscription_cost = ?, billing_cycle = ?, notes = ? WHERE service_id = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$name, $type, $warehouse_id, $contact_email, $status, $subscription_cost, $billing_cycle, $notes, $id]);
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Edit Third Party Service</h2>
    <form method="post">
        <div class="form-group">
            <label>Service Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($service['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="">Select Type</option>
                <?php
                $types = ['Repricing', 'Analytics', 'Freight', 'Automation', 'Prep Center'];
                foreach ($types as $type):
                ?>
                    <option value="<?= htmlspecialchars($type) ?>" <?= $service['type'] === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Warehouse</label>
            <select name="warehouse_id" class="form-control">
                <option value="">None</option>
                <?php foreach ($warehouses as $w): ?>
                    <option value="<?= htmlspecialchars($w['warehouse_id']) ?>" <?= $service['warehouse_id'] == $w['warehouse_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($w['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Contact Email</label>
            <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($service['contact_email']) ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Active" <?= $service['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $service['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label>Subscription Cost</label>
            <input type="number" step="0.01" name="subscription_cost" class="form-control" value="<?= htmlspecialchars($service['subscription_cost']) ?>">
        </div>
        <div class="form-group">
            <label>Billing Cycle</label>
            <select name="billing_cycle" class="form-control">
                <option value="">None</option>
                <option value="Monthly" <?= $service['billing_cycle'] === 'Monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="Annually" <?= $service['billing_cycle'] === 'Annually' ? 'selected' : '' ?>>Annually</option>
                <option value="One-Time" <?= $service['billing_cycle'] === 'One-Time' ? 'selected' : '' ?>>One-Time</option>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control"><?= htmlspecialchars($service['notes']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Service</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
