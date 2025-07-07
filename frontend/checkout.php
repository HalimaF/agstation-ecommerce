<?php
session_start();
require_once '../config/db.php';

// --- GUEST CHECKOUT HANDLING ---
if (!isset($_SESSION['customer_id'])) {
    // If guest submits the registration form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest_checkout'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $phone = trim($_POST['phone']);
        $shipping_address = trim($_POST['shipping_address']);

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT customer_id FROM WebsiteCustomers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered. Please log in.";
        } else {
            // Register new customer
            $stmt = $pdo->prepare("INSERT INTO WebsiteCustomers (name, email, password_hash, phone, shipping_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $phone, $shipping_address]);
            $customer_id = $pdo->lastInsertId();
            $_SESSION['customer_id'] = $customer_id;
            // Reload as logged-in customer
            header("Location: checkout.php");
            exit;
        }
    }

    require_once '../includes/header.php';
    ?>
    <div class="container mt-5 mb-5">
        <div class="card shadow-sm p-4">
            <h2 class="mb-4 text-center" style="color:#3D52A0;">Guest Checkout</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <input type="hidden" name="guest_checkout" value="1">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Address</label>
                    <textarea name="shipping_address" class="form-control" rows="2" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">Continue to Checkout</button>
            </form>
        </div>
    </div>
    <?php
    require_once '../includes/footer.php';
    exit;
}

// --- LOGGED-IN CUSTOMER CHECKOUT ---
require_once '../includes/header.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<div class='container mt-4'><div class='alert alert-warning text-center'>Your cart is empty.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$customer_id = $_SESSION['customer_id'];
$total = 0;
$order_items = [];
$customer = null;

// Fetch customer info
$stmt = $pdo->prepare("SELECT name, email, phone, shipping_address FROM WebsiteCustomers WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch cart product details
foreach ($cart as $asin => $qty) {
    $stmt = $pdo->prepare("SELECT asin, name, retail_price, image_url FROM Products WHERE asin = ?");
    $stmt->execute([$asin]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $product['quantity'] = $qty;
        $product['subtotal'] = $product['retail_price'] * $qty;
        $order_items[] = $product;
        $total += $product['subtotal'];
    }
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['guest_checkout'])) {
    $payment_method = $_POST['payment_method'] ?? 'Card';
    $shipping_address = trim($_POST['shipping_address'] ?? $customer['shipping_address']);
    $payment_status = 'Paid'; // Assume payment is successful

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO WebsiteOrders (customer_id, total_amount, payment_status, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$customer_id, $total, $payment_status, 'Pending']);
    $order_id = $pdo->lastInsertId();

    // Insert order items
    foreach ($order_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO OrderItems (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['asin'], $item['quantity'], $item['retail_price']]);
    }

    // Insert payment record (already present)
    $allowed_methods = ['Card', 'PayPal'];
    if (!in_array($payment_method, $allowed_methods)) {
        $payment_method = 'Card';
    }
    $stmt = $pdo->prepare("INSERT INTO Payments (order_id, method, amount, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $payment_method, $total, $payment_status]);

    // Update shipping address if changed
    if ($shipping_address && $shipping_address !== $customer['shipping_address']) {
        $stmt = $pdo->prepare("UPDATE WebsiteCustomers SET shipping_address = ? WHERE customer_id = ?");
        $stmt->execute([$shipping_address, $customer_id]);
    }

    // --- ADD THIS BLOCK TO SAVE CUSTOMER INVOICE ---
    $invoice_items = [];
    foreach ($order_items as $item) {
        $invoice_items[] = [
            'asin' => $item['asin'],
            'name' => $item['name'],
            'unit_price' => $item['retail_price'],
            'quantity' => $item['quantity'],
            'subtotal' => $item['subtotal']
        ];
    }
    $items_json = json_encode($invoice_items);

    $stmt = $pdo->prepare("INSERT INTO CustomerInvoices (customer_id, issue_date, due_date, total_amount, status, items) VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), ?, 'Paid', ?)");
    $stmt->execute([$customer_id, $total, $items_json]);
    // --- END INVOICE BLOCK ---

    // Clear cart
    unset($_SESSION['cart']);

    // Prepare bill HTML for display only (mail sending removed)
    ?>
    <div class="container mt-5 mb-5">
        <div class="alert alert-success text-center">
            <h3>Thank you for your order!</h3>
            <p>Your order ID is <strong>#<?= htmlspecialchars($order_id) ?></strong>.</p>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">Order Bill / Receipt</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                <p><strong>Shipping Address:</strong> <?= nl2br(htmlspecialchars($shipping_address)) ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment_method) ?></p>
                <hr>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>$<?= number_format($item['retail_price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th>$<?= number_format($total, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="text-center">
            <a href="orders.php" class="btn btn-outline-primary">View My Orders</a>
            <a href="products.php" class="btn btn-outline-secondary">Continue Shopping</a>
        </div>
    </div>
    <?php

    require_once '../includes/footer.php';
    exit;
}
?>

<div class="container mt-4 mb-5">
    <h2 class="text-center mb-4" style="color:#3D52A0;font-weight:700;">Checkout</h2>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="checkout-container">
                <div class="checkout-summary-table card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white fw-bold">Order Summary</div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="../uploads/product_images/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:40px;height:40px;object-fit:contain;border-radius:6px;margin-right:6px;">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </td>
                                        <td>$<?= number_format($item['retail_price'], 2) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>$<?= number_format($total, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <form method="post" class="card shadow-sm p-4" id="checkoutForm" autocomplete="off">
                    <h5 class="mb-3 fw-bold" style="color:#3D52A0;">Shipping Details</h5>
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" class="form-control" rows="2" required><?= htmlspecialchars($customer['shipping_address'] ?? '') ?></textarea>
                    </div>
                    <h5 class="mb-3 fw-bold" style="color:#3D52A0;">Payment Method</h5>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCard" value="Card" checked onclick="toggleCardFields(true)">
                            <label class="form-check-label" for="payCard">Credit/Debit Card</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="payment_method" id="payPaypal" value="PayPal" onclick="toggleCardFields(false)">
                            <label class="form-check-label" for="payPaypal">PayPal</label>
                        </div>
                    </div>
                    <div id="cardFields">
                        <div class="mb-3">
                            <label for="card_holder" class="form-label">Card Holder Name</label>
                            <input type="text" name="card_holder" id="card_holder" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" name="card_number" id="card_number" class="form-control" maxlength="32">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_expiry" class="form-label">Expiry (MM/YY)</label>
                                <input type="text" name="card_expiry" id="card_expiry" class="form-control" maxlength="7">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="card_cvv" class="form-label">CVV</label>
                                <input type="text" name="card_cvv" id="card_cvv" class="form-control" maxlength="4">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="paypalFields" style="display:none;">
                        <label for="paypal_email" class="form-label">PayPal Email</label>
                        <input type="email" name="paypal_email" id="paypal_email" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Place Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCardFields(show) {
    document.getElementById('cardFields').style.display = show ? 'block' : 'none';
    document.getElementById('paypalFields').style.display = (!show && document.getElementById('payPaypal').checked) ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    toggleCardFields(document.getElementById('payCard').checked);
    document.querySelectorAll('input[name="payment_method"]').forEach(function(el) {
        el.addEventListener('change', function() {
            toggleCardFields(document.getElementById('payCard').checked);
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>

<!-- Close wrappers opened in header.php -->
        </div> <!-- .container mt-4 from header.php -->
    </div> <!-- .main-content -->
</div> <!-- .layout-wrapper -->
</body>
</html>
