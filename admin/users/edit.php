<?php
require_once '../../config/db.php';
require_once '../../includes/session.php';
checkRole(['admin', 'Admin', 1]);

// Validate and sanitize the ID parameter
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo "User ID is missing or invalid.";
    exit;
}

try {
    // Fetch the user details
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit;
    }

    // Fetch roles for the dropdown
    $roles_query = "SELECT * FROM roles";
    $roles_stmt = $pdo->prepare($roles_query);
    $roles_stmt->execute();
    $roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $role_id = $_POST['role_id'];

        // Update the user in the database
        $update_query = "UPDATE users SET name = ?, email = ?, role_id = ? WHERE user_id = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$name, $email, $role_id, $id]);
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role_id" class="form-control" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= htmlspecialchars($role['role_id']) ?>" <?= $role['role_id'] == $user['role_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['role_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
