<?php
require_once '../../includes/session.php';
require_once '../../config/db.php';

// Admin check (case-insensitive, supports numeric role)
checkRole(['admin', 'Admin', 1]);

require_once '../../includes/header.php';

try {
    // Fetch orders with customer details
    $query = "
        SELECT o.order_id AS id, c.name AS customer_name, o.total_amount, o.status, o.order_date, o.payment_status
        FROM WebsiteOrders o
        JOIN WebsiteCustomers c ON o.customer_id = c.customer_id
        ORDER BY o.order_date DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <h2>Customer Orders</h2>
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= htmlspecialchars($order['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['customer_name'] ?? '') ?></td>
                <td>$ <?= htmlspecialchars($order['total_amount'] ?? '') ?></td>
                <td><?= ucfirst(htmlspecialchars($order['status'] ?? '')) ?></td>
                <td><?= ucfirst(htmlspecialchars($order['payment_status'] ?? '')) ?></td>
                <td><?= isset($order['order_date']) ? date('d-M-Y H:i', strtotime($order['order_date'])) : '' ?></td>
                <td>
                    <a href="view.php?id=<?= urlencode($order['id']) ?>" class="btn btn-sm btn-primary">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
