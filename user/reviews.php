<?php
include_once('../includes/session.php');
include_once('../includes/header.php');
require_once('../config/db.php');

// Use customer_id from session for WebsiteCustomers table
$customer_id = $_SESSION['customer_id'] ?? null;

if (!$customer_id) {
    echo "<div class='alert alert-danger'>Session expired. Please log in again.</div>";
    include_once('../includes/footer.php');
    exit;
}

// Handle review submission
$review_success = null;
$review_error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $product_id = $_POST['product_id'];
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    // Check if this customer has purchased this product and hasn't already reviewed it
    $stmt = $pdo->prepare("
        SELECT oi.order_id
        FROM OrderItems oi
        JOIN WebsiteOrders o ON o.order_id = oi.order_id
        WHERE o.customer_id = ? AND oi.product_id = ?
        LIMIT 1
    ");
    $stmt->execute([$customer_id, $product_id]);
    $has_order = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT review_id FROM ProductReviews WHERE product_id = ? AND source = 'Website'");
    $stmt->execute([$product_id]);
    $already_reviewed = $stmt->fetch();

    if (!$has_order) {
        $review_error = "You can only review products you have purchased.";
    } elseif ($already_reviewed) {
        $review_error = "This product already has a review.";
    } elseif ($rating < 1 || $rating > 5) {
        $review_error = "Rating must be between 1 and 5.";
    } elseif (empty($review_text)) {
        $review_error = "Review text cannot be empty.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO ProductReviews (product_id, rating, review_text, source, created_at) VALUES (?, ?, ?, 'Website', NOW())");
        $stmt->execute([$product_id, $rating, $review_text]);
        $review_success = "Review submitted successfully!";
    }
}

try {
    $reviews = [];
    // Fetch reviews for products this customer has ordered (Website orders only)
    $stmt = $pdo->prepare("
        SELECT pr.*, p.name AS product_name
        FROM ProductReviews pr
        JOIN Products p ON pr.product_id = p.asin
        JOIN OrderItems oi ON oi.product_id = pr.product_id
        JOIN WebsiteOrders o ON o.order_id = oi.order_id
        WHERE pr.source = 'Website' AND o.customer_id = ?
        GROUP BY pr.review_id
        ORDER BY pr.created_at DESC
    ");
    $stmt->execute([$customer_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch products purchased by this customer that have not been reviewed yet
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.asin, p.name
        FROM Products p
        JOIN OrderItems oi ON oi.product_id = p.asin
        JOIN WebsiteOrders o ON o.order_id = oi.order_id
        WHERE o.customer_id = ?
        AND p.asin NOT IN (
            SELECT product_id FROM ProductReviews WHERE source = 'Website'
        )
        ORDER BY p.name
    ");
    $stmt->execute([$customer_id]);
    $purchasable_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    include_once('../includes/footer.php');
    exit;
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">My Reviews</h2>

    <?php if ($review_success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($review_success) ?></div>
    <?php elseif ($review_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($review_error) ?></div>
    <?php endif; ?>

    <?php if (!empty($reviews)): ?>
        <div class="list-group mb-4">
            <?php foreach ($reviews as $review): ?>
                <div class="list-group-item">
                    <h5 class="mb-1"><?= htmlspecialchars($review['product_name']) ?></h5>
                    <p class="mb-1">Rating: <?= htmlspecialchars($review['rating']) ?>/5</p>
                    <p class="mb-1">Review: <?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                    <small class="text-muted">Date: <?= htmlspecialchars($review['created_at']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No reviews found.</div>
    <?php endif; ?>

    <h4 class="mb-3">Add a Review</h4>
    <?php if (!empty($purchasable_products)): ?>
        <form method="post" class="card card-body mb-4" style="max-width:500px;">
            <input type="hidden" name="add_review" value="1">
            <div class="mb-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" id="product_id" class="form-select" required>
                    <option value="">Select a product</option>
                    <?php foreach ($purchasable_products as $prod): ?>
                        <option value="<?= htmlspecialchars($prod['asin']) ?>"><?= htmlspecialchars($prod['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select name="rating" id="rating" class="form-select" required>
                    <option value="">Select rating</option>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="review_text" class="form-label">Review</label>
                <textarea name="review_text" id="review_text" class="form-control" rows="3" maxlength="1000" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    <?php else: ?>
        <div class="alert alert-info">You have reviewed all your purchased products.</div>
    <?php endif; ?>
</div>

<?php include_once('../includes/footer.php'); ?>
