<?php 
require_once '../../includes/session.php';
require_once '../../config/db.php';

// Admin check
checkRole(['admin', 'Admin', 1]);

// Validate and sanitize the order ID
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$order_id) {
    header("Location: index.php");
    exit;
}

try {
    // Fetch order details from WebsiteOrders and WebsiteCustomers
    $order_query = "
        SELECT o.order_id, c.name AS customer_name, o.total_amount, o.status, o.order_date, o.payment_status
        FROM WebsiteOrders o
        JOIN WebsiteCustomers c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?
    ";
    $stmt = $pdo->prepare($order_query);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: index.php");
        exit;
    }

    // Fetch order items from OrderItems and Products
    $items_query = "
        SELECT p.name, oi.quantity, oi.price_per_unit
        FROM OrderItems oi
        JOIN Products p ON oi.product_id = p.asin
        WHERE oi.order_id = ?
    ";
    $items_stmt = $pdo->prepare($items_query);
    $items_stmt->execute([$order_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
?>

<div class="container mt-4">
    <h3>Order #<?= htmlspecialchars($order['order_id']) ?> Details</h3>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>Total:</strong> $ <?= htmlspecialchars($order['total_amount']) ?></p>
    <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($order['status'])) ?></p>
    <p><strong>Payment Status:</strong> <?= ucfirst(htmlspecialchars($order['payment_status'])) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>

    <h4>Items</h4>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['quantity']) ?></td>
                <td>Rs. <?= htmlspecialchars($item['price_per_unit']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="update_status.php?id=<?= htmlspecialchars($order_id) ?>" class="btn btn-primary">Update Status</a>
</div>

<?php include '../../includes/footer.php'; ?>
