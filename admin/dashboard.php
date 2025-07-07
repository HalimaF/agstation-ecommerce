<?php
require_once '../includes/session.php';
require_once '../includes/header.php';
require_once '../config/db.php';

$counts = [];
$tables = ['Users', 'WebsiteCustomers', 'Products', 'WebsiteOrders', 'Payments', 'Returns', 'ProductReviews'];

try {
    foreach ($tables as $table) {
        if ($table === 'Payments') {
            $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM Payments");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $counts[$table] = $row['total'] ? $row['total'] : 0;
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $counts[$table] = $row['count'];
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$cardInfo = [
    'Users' => ['icon' => 'bi-people', 'color' => 'primary'],
    'WebsiteCustomers' => ['icon' => 'bi-person-badge', 'color' => 'info'],
    'Products' => ['icon' => 'bi-box-seam', 'color' => 'success'],
    'WebsiteOrders' => ['icon' => 'bi-cart', 'color' => 'warning'],
    'Payments' => ['icon' => 'bi-cash-stack', 'color' => 'success'],
    'Returns' => ['icon' => 'bi-arrow-counterclockwise', 'color' => 'danger'],
    'ProductReviews' => ['icon' => 'bi-star', 'color' => 'secondary'],
];
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
.dashboard-area {
    max-width: 1200px;
    margin: 0 auto;
}
.dashboard-card {
    transition: box-shadow 0.2s;
    border: none;
    border-radius: 1rem;
    min-width: 220px;
    min-height: 180px;
    background: #f8fafd;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.dashboard-card:hover {
    box-shadow: 0 0 20px rgba(0,0,0,0.12);
}
.dashboard-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}
@media (max-width: 767px) {
    .dashboard-card {
        min-width: 100%;
    }
}
</style>
<div class="container mt-4 dashboard-area">
    <h1 class="mb-4 fw-bold text-center">Admin Dashboard</h1>
    <div class="row g-4 justify-content-center">
        <?php foreach ($counts as $table => $count): ?>
            <?php
                $icon = $cardInfo[$table]['icon'];
                $color = $cardInfo[$table]['color'];
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch">
                <div class="card dashboard-card text-center border-0 shadow-sm w-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="dashboard-icon text-<?= $color ?>">
                            <i class="bi <?= $icon ?>"></i>
                        </div>
                        <h5 class="card-title mb-2"><?= htmlspecialchars($table === 'WebsiteCustomers' ? 'Customers' : $table) ?></h5>
                        <?php if ($table === 'Payments'): ?>
                            <p class="card-text fs-4 fw-semibold text-success">
                                $<?= number_format($count, 2) ?>
                            </p>
                            <small class="text-muted">Total Payment Received</small>
                        <?php else: ?>
                            <p class="card-text fs-4 fw-semibold"><?= htmlspecialchars($count) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>