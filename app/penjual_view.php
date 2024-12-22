<?php
session_start();

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: ../app/halaman-default.php");
    exit();
}

// Koneksi ke database
$host = "localhost";
$port = "5432";
$dbname = "Web-Ecommerce";
$dbUser    = "postgres";
$dbPassword = "456287";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
    exit;
}

// Ambil semua pesanan untuk penjual berdasarkan produk yang dijual oleh penjual
$stmt_orders = $conn->prepare("
    SELECT o.order_id, o.user_id, o.status_order, o.total_harga, oi.produk_id, oi.quantity, oi.harga_per_item, oi.total_harga_item
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN produk p ON oi.produk_id = p.produk_id
    WHERE p.penjual_id = :penjual_id  -- Mengambil pesanan yang melibatkan produk milik penjual
    ORDER BY o.order_id DESC
");
$stmt_orders->bindParam(':penjual_id', $_SESSION['user_id']);  // penjual_id adalah user_id dari penjual
$stmt_orders->execute();
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Update status_order jika ada form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status_order'])) {
    $orderId = $_POST['order_id'];
    $statusOrder = $_POST['status_order'];
    
    // Perbarui status_order untuk pesanan ini
    $stmt_update = $conn->prepare("
        UPDATE orders
        SET status_order = :status_order
        WHERE order_id = :order_id
    ");
    $stmt_update->bindParam(':status_order', $statusOrder);
    $stmt_update->bindParam(':order_id', $orderId);
    $stmt_update->execute();

    // Redirect untuk menghindari resubmit form
    header("Location: ../app/penjual_view.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Penjual</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<script src="https://unpkg.com/feather-icons"></script>
<body>


    <!--=====NAVBAR MODIFIED FIXED=====-->
    <nav class="navbar">
      <div class="navbar_contents">
        <div class="logo">
          <img src="../public/img/logo/redget_logo.png" alt="Redget">
        </div>
        <ul class="nav-links" style=
        "padding-left: 0;
        margin-bottom: 0;
        ">
            <li><a href="#">Home</a></li>
            <li><a href="#products">Kategori</a></li>
            <li><a href="#">Promo</a></li>
        </ul>
        <div class="nav-icons">
            <a class="cart-icon" href="../app/shopping_cart.php">
                <i data-feather="shopping-cart"></i><span class="cart-count">0</span>
            </a>
            <a href="../app/halaman-profile.php" id="open-form-btn"><i data-feather="user"></i></a>
            <a href="../app/logout.php">
                <i data-feather="log-out"></i>
            </a>
        </div>
      </div>
    </nav>


<div class="container mt-5">
    <h2>Pemesanan untuk Penjual</h2>

    <h3 class="mt-4">Daftar Pemesanan</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Produk ID</th>
                <th>Quantity</th>
                <th>Harga per Item</th>
                <th>Total Harga Item</th>
                <th>Status Order</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['produk_id']) ?></td>
                        <td><?= htmlspecialchars($order['quantity']) ?></td>
                        <td>Rp <?= number_format($order['harga_per_item'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($order['total_harga_item'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($order['status_order']) ?></td>
                        <td>
                            <!-- Form untuk merubah status_order -->
                            <form method="POST" action="">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                                <select name="status_order" class="form-control">
                                    <option value="Pending" <?= $order['status_order'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="On-Progress" <?= $order['status_order'] === 'On-Progress' ? 'selected' : '' ?>>On-Progress</option>
                                    <option value="Success" <?= $order['status_order'] === 'Success' ? 'selected' : '' ?>>Success</option>
                                    <option value="Failed" <?= $order['status_order'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                                    <option value="In-Delivery" <?= $order['status_order'] === 'In-Delivery' ? 'selected' : '' ?>>In-Delivery</option>
                                    <option value="Shipped" <?= $order['status_order'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                </select>
                                <button type="submit" class="btn btn-warning mt-2">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada pesanan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
