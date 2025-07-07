<?php 
require_once '../../includes/session.php';
checkRole(['admin', 'Admin', 1]);
require_once '../../config/db.php';

// Validate and sanitize the order ID
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$order_id) {
    header("Location: index.php");
    exit;
}

try {
    // Fetch the order details from WebsiteOrders
    $query = "SELECT * FROM WebsiteOrders WHERE order_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: index.php");
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_status = trim($_POST['status']);
        $update_query = "UPDATE WebsiteOrders SET status = ? WHERE order_id = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$new_status, $order_id]);
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h3>Update Status for Order #<?= htmlspecialchars($order['order_id']) ?></h3>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Current Status: <?= ucfirst(htmlspecialchars($order['status'])) ?></label>
            <select name="status" class="form-select" required>
                <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Status</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
