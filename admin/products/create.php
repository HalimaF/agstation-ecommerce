<?php 
require_once '../../config/db.php';
require_once '../../includes/session.php';
require_once '../../includes/image-helper.php';

checkRole(['admin', 'Admin', 1]);

// Fetch brands and distributors for dropdowns
$brands = $pdo->query("SELECT brand_id, name FROM Brands WHERE status='Active'")->fetchAll(PDO::FETCH_ASSOC);
$distributors = $pdo->query("SELECT distributor_id, name FROM Distributors WHERE status='Active'")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asin = trim($_POST['asin']);
    $name = trim($_POST['name']);
    $upc = trim($_POST['upc']);
    $brand_id = !empty($_POST['brand_id']) ? $_POST['brand_id'] : null;
    $distributor_id = !empty($_POST['distributor_id']) ? $_POST['distributor_id'] : null;
    $category = trim($_POST['category']);
    $cost_price = trim($_POST['cost_price']);
    $retail_price = trim($_POST['retail_price']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);

    // Handle image selection (local image or file upload)
    $imageName = null;
    
    // Check if local image was selected
    if (!empty($_POST['local_image']) && $_POST['local_image'] !== 'upload') {
        $imageName = $_POST['local_image'];
    } elseif (!empty($_FILES['image']['name'])) {
        // Handle file upload
        $uploadDir = __DIR__ . '/../../uploads/product_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = uniqid('prod_') . '_' . basename($_FILES['image']['name']);
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imagePath = $uploadDir . $imageName;
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($imageTmpName);
        
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Invalid file type. Please upload JPEG, PNG, GIF, or WebP images only.";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $error = "File size too large. Maximum 5MB allowed.";
        } elseif (!move_uploaded_file($imageTmpName, $imagePath)) {
            $error = "Failed to upload the product image.";
        }
    }

    if (empty($asin) || empty($name)) {
        $error = "ASIN and Product Name are required.";
    } elseif (!isset($error)) {
        try {
            $query = "INSERT INTO Products 
                (asin, name, upc, brand_id, distributor_id, category, cost_price, retail_price, description, image_url, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $asin,
                $name,
                $upc,
                $brand_id ?: null,
                $distributor_id ?: null,
                $category,
                $cost_price,
                $retail_price,
                $description,
                $imageName,
                $status
            ]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<?php include '../../includes/header.php'; ?>
<div class="container mt-4">
    <h2>Add Product</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">ASIN*</label>
            <input type="text" name="asin" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Product Name*</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">UPC</label>
            <input type="text" name="upc" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Brand</label>
            <select name="brand_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Distributor</label>
            <select name="distributor_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($distributors as $distributor): ?>
                    <option value="<?= $distributor['distributor_id'] ?>"><?= htmlspecialchars($distributor['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Cost Price</label>
            <input type="number" step="0.01" name="cost_price" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Retail Price</label>
            <input type="number" step="0.01" name="retail_price" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label small">Select from Available Images:</label>
                    <select name="local_image" class="form-select" id="imageSelect" onchange="toggleImageUpload()">
                        <option value="">Choose local image...</option>
                        <?php
                        $availableImages = getAvailableProductImages();
                        foreach ($availableImages as $image):
                        ?>
                            <option value="<?= htmlspecialchars($image) ?>"><?= htmlspecialchars($image) ?></option>
                        <?php endforeach; ?>
                        <option value="upload">Upload new image...</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Or Upload New Image:</label>
                    <input type="file" name="image" class="form-control" id="imageUpload" accept="image/*">
                    <small class="text-muted">JPEG, PNG, GIF, WebP - Max 5MB</small>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
function toggleImageUpload() {
    const select = document.getElementById('imageSelect');
    const upload = document.getElementById('imageUpload');
    
    if (select.value === 'upload') {
        upload.style.display = 'block';
        upload.required = true;
    } else if (select.value !== '') {
        upload.style.display = 'none';
        upload.required = false;
        upload.value = '';
    } else {
        upload.style.display = 'block';
        upload.required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleImageUpload();
});
</script>

<?php include '../../includes/footer.php'; ?>
