<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

$err = [];
$ok  = '';

/* ================= DELETE ================= */
if (isset($_GET['delete'], $_GET['type'])) {
    $id   = (int)$_GET['delete'];
    $type = $_GET['type'];

    switch ($type) {
        case 'main':
            $stmt = $mysqli->prepare("DELETE FROM main_categories WHERE id=?");
            break;
        case 'brand':
            $stmt = $mysqli->prepare("DELETE FROM brands WHERE id=?");
            break;
        case 'gender':
            $stmt = $mysqli->prepare("DELETE FROM genders WHERE id=?");
            break;
        case 'price':
            $stmt = $mysqli->prepare("DELETE FROM price_categories WHERE id=?");
            break;
        default:
            $stmt = null;
    }

    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = ucfirst($type) . " deleted successfully!";
    }
}

/* ================= ADD / UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = $_POST['type'] ?? '';
    $id   = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    $min_price = $_POST['min_price'] ?? null;
    $max_price = $_POST['max_price'] ?? null;

    if (!$name) $err[] = "Name/Label cannot be empty.";

    if (!$err) {
        switch ($type) {

            case 'main':
                $slug = strtolower(preg_replace('/\s+/', '-', $name));
                if ($id) {
                    $stmt = $mysqli->prepare(
                        "UPDATE main_categories SET name=?, slug=? WHERE id=?"
                    );
                    $stmt->bind_param('ssi', $name, $slug, $id);
                    $ok = "Main category updated!";
                } else {
                    $stmt = $mysqli->prepare(
                        "INSERT INTO main_categories (name, slug) VALUES (?, ?)"
                    );
                    $stmt->bind_param('ss', $name, $slug);
                    $ok = "Main category added!";
                }
                break;

            case 'brand':
                if ($id) {
                    $stmt = $mysqli->prepare("UPDATE brands SET name=? WHERE id=?");
                    $stmt->bind_param('si', $name, $id);
                    $ok = "Brand updated!";
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO brands (name) VALUES (?)");
                    $stmt->bind_param('s', $name);
                    $ok = "Brand added!";
                }
                break;

            case 'gender':
                if ($id) {
                    $stmt = $mysqli->prepare("UPDATE genders SET name=? WHERE id=?");
                    $stmt->bind_param('si', $name, $id);
                    $ok = "Gender updated!";
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO genders (name) VALUES (?)");
                    $stmt->bind_param('s', $name);
                    $ok = "Gender added!";
                }
                break;

            case 'price':
                if ($min_price === null || $max_price === null) {
                    $err[] = "Min and Max price required.";
                    break;
                }
                if ($id) {
                    $stmt = $mysqli->prepare(
                        "UPDATE price_categories SET label=?, min_price=?, max_price=? WHERE id=?"
                    );
                    $stmt->bind_param('sddi', $name, $min_price, $max_price, $id);
                    $ok = "Price category updated!";
                } else {
                    $stmt = $mysqli->prepare(
                        "INSERT INTO price_categories (label, min_price, max_price)
                         VALUES (?, ?, ?)"
                    );
                    $stmt->bind_param('sdd', $name, $min_price, $max_price);
                    $ok = "Price category added!";
                }
                break;
        }

        if (isset($stmt) && !$stmt->execute()) {
            $err[] = $stmt->error;
        }
    }
}

/* ================= FETCH ================= */
$main_categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$brands  = $mysqli->query("SELECT * FROM brands ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$genders = $mysqli->query("SELECT * FROM genders ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$prices  = $mysqli->query("SELECT * FROM price_categories ORDER BY min_price ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Categories</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --primary:#046307;
    --secondary:#D4AF37;
    --muted:#f4f6f9;
    --text-dark:#333;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;display:flex;min-height:100vh;background:var(--muted);}
/* Sidebar */
.sidebar {
    width: 240px;
    background: darkgray;
    color: black;
    display: flex;
    flex-direction: column;
    padding-top: 25px;
    position: fixed;
    height: 100vh;
    box-shadow: 8px 0 20px rgba(0,0,0,0.08);
}
.sidebar-title {
    font-size: 20px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 35px;
}
.sidebar a {
    display: flex;
    align-items: center;
    padding: 14px 25px;
    color: deeppink;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}
.sidebar a:hover {
    background: rgba(255,255,255,0.15);
    padding-left: 30px;
}

.main-content{margin-left:240px;padding:30px;width:100%;}
.card{background:#fff;padding:20px;border-radius:12px;margin-bottom:30px;}
form{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:15px;}
input{padding:8px;border-radius:6px;border:1px solid #ccc;flex:1;}
button{padding:8px 14px;border-radius:6px;border:none;background:var(--secondary);color:#fff;}
table{width:100%;border-collapse:collapse;}
th,td{padding:12px;border-bottom:1px solid #ddd;}
th{background:gray;color:black;}
.success{color:green;}
.error{color:red;}

td a{
    padding:5px 10px;
    border-radius:6px;
    color:#fff;
    text-decoration:none;
    font-size:0.85rem;
    margin-right:5px;
    display:inline-block;
}
.btn-edit{background:var(--primary);}
.btn-edit:hover{background:#034d05;}
.btn-delete{background:#dc3545;}
.btn-delete:hover{background:#a71d2a;}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-title">Admin Panel</div>
    <a href="admin-dashboard.php">🏠 Dashboard</a>
    <a href="manage-product.php">📦 Manage Products</a>
    <a href="manage-orders.php">🛒 Orders</a>
    <a href="admin-payments.php">💳 Payments</a>
    <a href="admin-contact-messages.php">✉️ Contact Messages</a>
    <a href="admin-users.php">👤 Users</a>
    <a href="admin-feedback.php">⭐ Feedback</a>
    <a href="manage-categories.php">🗂 Categories</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
<h1>Manage Categories</h1>

<?php if($ok) echo "<p class='success'>$ok</p>"; ?>
<?php foreach($err as $e) echo "<p class='error'>$e</p>"; ?>

<!-- MAIN -->
<div class="card">
<h2>Add Main Category</h2>
<form method="post">
<input type="text" name="name" placeholder="Category Name" required>
<input type="hidden" name="type" value="main">
<button>Add</button>
</form>

<table>
<tr><th>#</th><th>Name</th><!-- <th>Slug</th> --><th>Action</th></tr>
<?php $i=1; foreach($main_categories as $m): ?>
<tr>
<td><?= $i++ ?></td>
<td>
<form method="post">
<input type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>">
<input type="hidden" name="id" value="<?= $m['id'] ?>">
<input type="hidden" name="type" value="main">
<button class="btn-edit">Update</button>
</form>
</td>
<!-- <td><?= htmlspecialchars($m['slug']) ?></td> -->
<td>
<a class="btn-delete" href="?delete=<?= $m['id'] ?>&type=main" onclick="return confirm('Delete this category?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<!-- BRAND -->
<div class="card">
<h2>Add Brand</h2>
<form method="post">
<input type="text" name="name" placeholder="Brand Name" required>
<input type="hidden" name="type" value="brand">
<button>Add</button>
</form>

<table>
<tr><th>#</th><th>Name</th><th>Action</th></tr>
<?php $i=1; foreach($brands as $b): ?>
<tr>
<td><?= $i++ ?></td>
<td>
<form method="post">
<input type="text" name="name" value="<?= htmlspecialchars($b['name']) ?>">
<input type="hidden" name="id" value="<?= $b['id'] ?>">


<input type="hidden" name="type" value="brand">
<button class="btn-edit">Update</button>
</form>
</td>
<td>
<a class="btn-delete" href="?delete=<?= $b['id'] ?>&type=brand" onclick="return confirm('Delete this brand?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<!-- GENDER -->
<div class="card">
<h2>Add Gender</h2>
<form method="post">
<input type="text" name="name" placeholder="Gender Name" required>
<input type="hidden" name="type" value="gender">
<button>Add</button>
</form>

<table>
<tr><th>#</th><th>Name</th><th>Action</th></tr>
<?php $i=1; foreach($genders as $g): ?>
<tr>
<td><?= $i++ ?></td>
<td>
<form method="post">
<input type="text" name="name" value="<?= htmlspecialchars($g['name']) ?>">
<input type="hidden" name="id" value="<?= $g['id'] ?>">
<input type="hidden" name="type" value="gender">
<button class="btn-edit">Update</button>
</form>
</td>
<td>
<a class="btn-delete" href="?delete=<?= $g['id'] ?>&type=gender" onclick="return confirm('Delete this gender?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<!-- PRICE -->
<div class="card">
<h2>Add Price Category</h2>
<form method="post">
<input type="text" name="name" placeholder="Label" required>
<input type="number" name="min_price" placeholder="Min" required>
<input type="number" name="max_price" placeholder="Max" required>
<input type="hidden" name="type" value="price">
<button>Add</button>
</form>

<table>
<tr><th>#</th><th>Label</th><th>Min</th><th>Max</th><th>Action</th></tr>
<?php $i=1; foreach($prices as $p): ?>
<tr>
<td><?= $i++ ?></td>
<td>
<form method="post">
<input type="text" name="name" value="<?= htmlspecialchars($p['label']) ?>">
<input type="number" name="min_price" value="<?= $p['min_price'] ?>">
<input type="number" name="max_price" value="<?= $p['max_price'] ?>">
<input type="hidden" name="id" value="<?= $p['id'] ?>">
<input type="hidden" name="type" value="price">
<button class="btn-edit">Update</button>
</form>
</td>
<td><?= $p['min_price'] ?></td>
<td><?= $p['max_price'] ?></td>
<td>
<a class="btn-delete" href="?delete=<?= $p['id'] ?>&type=price" onclick="return confirm('Delete this price range?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

</div>
</body>
</html>
