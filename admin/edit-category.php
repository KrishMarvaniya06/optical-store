<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if (!$type || !$id) {
    die("Invalid request.");
}

$err = '';
$ok = '';

// Fetch main categories for select dropdown
$main_categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch existing category
switch ($type) {
    case 'main':
        $stmt = $mysqli->prepare("SELECT * FROM main_categories WHERE id=?");
        break;
    case 'brand':
        $stmt = $mysqli->prepare("SELECT * FROM brands WHERE id=?");
        break;
    case 'gender':
        $stmt = $mysqli->prepare("SELECT * FROM genders WHERE id=?");
        break;
    case 'price':
        $stmt = $mysqli->prepare("SELECT * FROM price_categories WHERE id=?");
        break;
    default:
        die("Unknown category type.");
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) die("Category not found.");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $min_price = $_POST['min_price'] ?? null;
    $max_price = $_POST['max_price'] ?? null;
    $main_id = $_POST['main_category_id'] ?? null;

    if (empty($name)) {
        $err = "Name/Label cannot be empty.";
    } else {
        switch ($type) {
            case 'main':
                $slug = strtolower(preg_replace('/\s+/', '-', $name));
                $stmt = $mysqli->prepare("UPDATE main_categories SET name=?, slug=? WHERE id=?");
                $stmt->bind_param('ssi', $name, $slug, $id);
                break;
            case 'brand':
                $stmt = $mysqli->prepare("UPDATE brands SET name=?, main_category_id=? WHERE id=?");
                $stmt->bind_param('sii', $name, $main_id, $id);
                break;
            case 'gender':
                $stmt = $mysqli->prepare("UPDATE genders SET name=?, main_category_id=? WHERE id=?");
                $stmt->bind_param('sii', $name, $main_id, $id);
                break;
            case 'price':
                if ($min_price === null || $max_price === null) {
                    $err = "Min and Max price are required.";
                    break;
                }
                $stmt = $mysqli->prepare("UPDATE price_categories SET label=?, min_price=?, max_price=?, main_category_id=? WHERE id=?");
                $stmt->bind_param('sddii', $name, $min_price, $max_price, $main_id, $id);
                break;
        }

        if (!$err) {
            if ($stmt->execute()) {
                $ok = "Category updated successfully!";
                $data = array_merge($data, $_POST); // refresh form
            } else {
                $err = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit <?= ucfirst($type) ?> Category</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;background:#f4f6f9;margin:0;}
form{background:#fff;padding:30px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.08);width:400px;}
input, button, select{width:100%;padding:10px;margin:8px 0;border-radius:8px;border:1px solid #ccc;}
button{background:#D4AF37;color:#000;font-weight:700;border:none;cursor:pointer;}
.success{color:green;margin-bottom:12px;}
.error{color:red;margin-bottom:12px;}
a{display:block;text-align:center;margin-top:10px;text-decoration:none;color:#046307;}
</style>
</head>
<body>

<form method="post">
    <h2>Edit <?= ucfirst($type) ?></h2>
    <?php if ($err) echo "<div class='error'>$err</div>"; ?>
    <?php if ($ok) echo "<div class='success'>$ok</div>"; ?>

    <input type="text" name="name" placeholder="Name/Label" value="<?= htmlspecialchars($data['name'] ?? $data['label']) ?>" required>

    <?php if ($type === 'price'): ?>
        <input type="number" step="0.01" name="min_price" placeholder="Min Price" value="<?= htmlspecialchars($data['min_price']) ?>" required>
        <input type="number" step="0.01" name="max_price" placeholder="Max Price" value="<?= htmlspecialchars($data['max_price']) ?>" required>
    <?php endif; ?>

    <?php if ($type !== 'main'): ?>
        <select name="main_category_id" required>
            <option value="">Select Main Category</option>
            <?php foreach($main_categories as $m): ?>
                <option value="<?= $m['id'] ?>" <?= ($data['main_category_id'] == $m['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <button type="submit">Save Changes</button>
    <a href="manage-categories.php">← Back to Categories</a>
</form>

</body>
</html>
