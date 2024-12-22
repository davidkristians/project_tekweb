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
$dbUser    = "postgres";
$dbPassword = "456287";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser  , $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
    exit;
}

// Ambil riwayat pembelian pengguna dari tabel orders dan order_items
$stmt_history = $conn->prepare("
    SELECT o.order_id, o.total_harga, o.status_order, oi.produk_id, oi.quantity, oi.harga_per_item, oi.total_harga_item
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
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
</head>
<body>
<div class="container mt-5">
    <h2>Riwayat Pembelian</h2>

    <h3 class="mt-4">Daftar Transaksi</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Quantity</th>
                <th>Harga</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
    <?php if (!empty($history)): ?>
        <?php 
            $currentOrderId = null;
            $currentOrderItems = [];
            foreach ($history as $item): 
                if ($currentOrderId != $item['order_id']) {
                    // Jika order_id berbeda, tampilkan order baru
                    if ($currentOrderId !== null) {
                        // Tampilkan detail order sebelumnya
                        echo '<tr><td colspan="5"><b>Order #' . $currentOrderId . ' - Status: ' . htmlspecialchars($currentOrderItems[0]['status_order']) . '</b></td></tr>';
                        foreach ($currentOrderItems as $orderItem) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($orderItem['produk_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($orderItem['quantity']) . '</td>';
                            echo '<td>Rp ' . number_format($orderItem['harga_per_item'], 0, ',', '.') . '</td>';
                            echo '<td>Rp ' . number_format($orderItem['total_harga_item'], 0, ',', '.') . '</td>';
                            echo '<td>' . htmlspecialchars($orderItem['status_order']) . '</td>'; 
                            echo '</tr>';
                        }
                    }
                    
                    // Reset untuk order baru
                    $currentOrderId = $item['order_id'];
                    $currentOrderItems = [];
                }
                
                // Masukkan item baru ke dalam list item order
                $item['status_order'] = $item['status_order'];  // Menambahkan status_order ke item
                $currentOrderItems[] = $item;
            endforeach;
        ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">Tidak ada riwayat pembelian.</td>
        </tr>
    <?php endif; ?>
</tbody>

    </table>


</div>
</body>
</html>
