<?php
session_start();
require_once '../config/db.php';

// Handle add to cart logic and redirect BEFORE any HTML or includes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asin'])) {
    $asin = $_POST['asin'];
    $_SESSION['cart'][$asin] = ($_SESSION['cart'][$asin] ?? 0) + 1;
    header("Location: cart.php");
    exit;
}

require_once '../includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<div class="cart-main-wrap" style="max-width:1100px; margin:40px auto 32px auto; display:flex; gap:32px; align-items:flex-start; background:none; box-shadow:none; padding:0;">
    <div style="flex:2; min-width:320px;">
        <table class="cart-table-modern w-100">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($cart as $asin => $qty):
                    $stmt = $pdo->prepare("SELECT name, retail_price, image_url FROM Products WHERE asin = ?");
                    $stmt->execute([$asin]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    $subtotal = $product['retail_price'] * $qty;
                    $total += $subtotal;
                ?>
                <tr>
                    <td>
                        <form method="post" action="remove_from_cart.php" style="display:inline;">
                            <input type="hidden" name="asin" value="<?= htmlspecialchars($asin) ?>">
                            <button type="submit" class="cart-remove-btn" title="Remove">&times;</button>
                        </form>
                    </td>
                    <td>
                        <div class="cart-product-info">
                            <img src="/uploads/product_images/<?= htmlspecialchars($product['image_url']) ?>" class="cart-product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            <span><?= htmlspecialchars($product['name']) ?></span>
                        </div>
                    </td>
                    <td style="white-space:nowrap;">$<?= number_format($product['retail_price'], 2) ?></td>
                    <td>
                        <div class="cart-qty-wrap">
                            <form method="post" action="update_cart.php" style="display:inline-flex;">
                                <input type="hidden" name="asin" value="<?= htmlspecialchars($asin) ?>">
                                <button type="submit" name="decrease" value="1" class="cart-qty-btn" <?= $qty <= 1 ? 'disabled' : '' ?>>-</button>
                                <input type="number" name="quantity" value="<?= htmlspecialchars($qty) ?>" min="1" class="cart-qty-input" onchange="this.form.submit()">
                                <button type="submit" name="increase" value="1" class="cart-qty-btn">+</button>
                            </form>
                        </div>
                    </td>
                    <td style="white-space:nowrap;">$<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="flex:1; min-width:290px;">
        <div class="cart-summary-box agstation-cart-summary" style="background:#f0f6ff; border:1.5px solid #3B5BDB; border-radius:10px; box-shadow:0 2px 8px #3b5bdb22;">
            <div class="cart-summary-title" style="color:#3B5BDB; font-weight:600;">Shipping Address</div>
            <div class="cart-summary-row" style="font-size:1rem; display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <span>
                    Shipping to <b><?= isset($_SESSION['shipping_address']) ? htmlspecialchars($_SESSION['shipping_address']) : 'CA.' ?></b>
                </span>
                <button 
                    type="button" 
                    onclick="document.getElementById('address-modal').style.display='block';" 
                    style="background:#3B5BDB; color:#fff; border:none; border-radius:4px; padding:4px 10px; font-size:0.95rem; cursor:pointer;">
                    Change
                </button>
            </div>
            <!-- Modal for changing address -->
            <div id="address-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:#0005; z-index:1000;">
                <div style="background:#fff; max-width:400px; margin:80px auto; padding:24px 20px 16px 20px; border-radius:8px; box-shadow:0 2px 16px #3b5bdb33; position:relative;">
                    <form method="post" action="update_address.php">
                        <label for="shipping_address" style="font-weight:500;">Enter Shipping Address:</label>
                        <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control" style="margin-top:8px;"><?= isset($_SESSION['shipping_address']) ? htmlspecialchars($_SESSION['shipping_address']) : '' ?></textarea>
                        <div style="margin-top:12px; display:flex; gap:10px;">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('address-modal').style.display='none';">Cancel</button>
                        </div>
                    </form>
                    <button onclick="document.getElementById('address-modal').style.display='none';" style="position:absolute; top:8px; right:12px; background:none; border:none; font-size:1.5rem; color:#888; cursor:pointer;">&times;</button>
                </div>
            </div>
            <div class="cart-summary-title" style="margin-top:24px;">Summary</div>
            <div class="cart-summary-row" style="display:flex; justify-content:space-between;">
                <span>Subtotal</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>
            <div class="cart-summary-row" style="display:flex; justify-content:space-between;">
                <span>Shipping</span>
                <span>$5.00</span>
            </div>
            <div class="cart-summary-total" style="font-size:2rem; margin-top:10px; color:#3B5BDB;">
                $<?= number_format($total + 5, 2) ?>
            </div>
            <a href="checkout.php" class="cart-checkout-btn" style="margin-top:18px;">PROCEED TO CHECKOUT</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<!-- Close wrappers opened in header.php -->
        </div> <!-- .container mt-4 from header.php -->
    </div> <!-- .main-content -->
</div> <!-- .layout-wrapper -->
</body>
</html>
