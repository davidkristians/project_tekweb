<?php
session_start();
$isLoggedIn = isset($_SESSION['user_email']) && isset($_SESSION['user_name']);
if (!$isLoggedIn) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$host = "localhost";
$port = "5432";
$dbname = "Web-Ecommerce";
$dbUser = "postgres";
$dbPassword = "postgres";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser  , $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
    exit;
}

// Ambil riwayat pembelian pengguna dari tabel orders dan order_items
$stmt_history = $conn->prepare("
    SELECT o.order_id, o.total_harga, o.status_order, oi.produk_id, oi.quantity, oi.harga_per_item, oi.total_harga_item, p.nama_produk
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN produk p ON oi.produk_id = p.produk_id
    WHERE o.user_id = :user_id
    ORDER BY o.order_id DESC
");

$stmt_history->bindParam(':user_id', $_SESSION['user_id']);
$stmt_history->execute();
$history = $stmt_history->fetchAll(PDO::FETCH_ASSOC);



// Ambil transaction_id dari input pencarian
$search_transaction_id = isset($_POST['search_transaction_id']) ? $_POST['search_transaction_id'] : '';
$transaction = null;

// Jika transaction_id tidak kosong, ambil data transaksi
if (!empty($search_transaction_id)) {
    $stmt = $conn->prepare("
        SELECT t.transaction_id, t.total_price, t.status
        FROM transactions t
        WHERE t.transaction_id = :transaction_id AND t.user_id = :user_id
    ");
    $stmt->bindParam(':transaction_id', $search_transaction_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!-- GOOGLE FONTS -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <!-- BOOTSTRAP ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSS UNTUK SEMUA HALAMAN -->
    <link rel="stylesheet" href="../public/css/style.css">
    <!-- CSS KHUSUS UNTUK HALAMAN INI -->
    <link rel="stylesheet" href="../app/halaman-default-baru-2.css">
    <link rel="stylesheet" href="../Produk/produk.css">
    <link rel="stylesheet" href="../app/halaman-pembayaran.css">
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
            <li><a href="../app/halaman-user.php">Home</a></li>
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
    <h3 class="mt-4">Daftar Transaksi</h3>
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Nama Produk</th> <!-- Kolom baru untuk nama produk -->
            <th>Quantity</th>
            <th>Harga</th>
            <th>Total Harga</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($history)): ?>
        <?php 
            $currentOrderId = null; // Inisialisasi ID order
            foreach ($history as $item): 
                if ($currentOrderId !== $item['order_id']): 
                    // Cetak header untuk order baru
                    if ($currentOrderId !== null) {
                        echo '<tr><td colspan="6"></td></tr>'; // Baris kosong pemisah antar order
                    }
                    $currentOrderId = $item['order_id'];
                    echo '<tr><td colspan="6"><b>Order #' . htmlspecialchars($item['order_id']) . ' - Status: ' . htmlspecialchars($item['status_order']) . '</b></td></tr>';
                endif;

                // Cetak item pesanan
                echo '<tr>';
                echo '<td>' . htmlspecialchars($item['produk_id']) . '</td>';
                echo '<td>' . htmlspecialchars($item['nama_produk']) . '</td>';
                echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
                echo '<td>Rp ' . number_format($item['harga_per_item'], 0, ',', '.') . '</td>';
                echo '<td>Rp ' . number_format($item['total_harga_item'], 0, ',', '.') . '</td>';
                echo '<td>' . htmlspecialchars($item['status_order']) . '</td>';
                echo '</tr>';
            endforeach;
        ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="text-center">Tidak ada riwayat pembelian.</td>
        </tr>
    <?php endif; ?>
</tbody>

</table>



</div>

<script>
  feather.replace();
</script>
</body>
</html>
