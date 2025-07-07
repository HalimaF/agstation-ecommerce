<?php
require_once '../../config/db.php';
require_once '../../includes/session.php';
require_once '../../includes/header.php';
checkRole(['admin','Admin', 1]); // Ensure the user has admin privileges

// Handle accept/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'], $_POST['action'])) {
    $return_id = intval($_POST['return_id']);
    $status = ($_POST['action'] === 'accept') ? 'Accepted' : 'Rejected';
    $stmt = $pdo->prepare("UPDATE Returns SET status = ? WHERE return_id = ?");
    $stmt->execute([$status, $return_id]);
    header("Location: index.php");
    exit;
}

try {
    // Fetch all returns from the database
    $query = "SELECT * FROM Returns ORDER BY return_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="container">
    <h2>Returns</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Order ID</th>
                <th>Product ID</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($returns as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['return_id']) ?></td>
                <td><?= htmlspecialchars($row['order_id']) ?></td>
                <td><?= htmlspecialchars($row['product_id']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <?php if ($row['status'] === 'Pending'): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="return_id" value="<?= $row['return_id'] ?>">
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">No actions</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
