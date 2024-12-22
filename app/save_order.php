<?php
session_start();

// Periksa apakah pengguna login
$isLoggedIn = isset($_SESSION['user_email']) && isset($_SESSION['user_name']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

if (!$isLoggedIn || !$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$totalPrice = $input['total_price'];

// Koneksi ke database
$host = "localhost";
$port = "5432";
$dbname = "Web-Ecommerce";
$dbUser = "postgres";
$dbPassword = "postgres";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mulai transaksi database
    $conn->beginTransaction();

    // 1. Buat entri baru di tabel `orders`
    $stmtOrder = $conn->prepare("
        INSERT INTO orders (user_id, total_harga, status_order) 
        VALUES (:user_id, :total_harga, 'Pending') RETURNING order_id
    ");
    $stmtOrder->execute([
        ':user_id' => $userId,
        ':total_harga' => $totalPrice
    ]);
    $orderId = $stmtOrder->fetchColumn();

    // 2. Ambil item dari `shopping_cart` untuk user_id
    $stmtCart = $conn->prepare("
        SELECT c.produk_id, c.quantity, p.harga 
        FROM shopping_cart c
        JOIN produk p ON c.produk_id = p.produk_id
        WHERE c.user_id = :user_id
    ");
    $stmtCart->execute([':user_id' => $userId]);
    $cartItems = $stmtCart->fetchAll(PDO::FETCH_ASSOC);

    // 3. Simpan setiap item ke tabel `order_items`
    $stmtOrderItem = $conn->prepare("
        INSERT INTO order_items (order_id, produk_id, quantity, harga_per_item, total_harga_item) 
        VALUES (:order_id, :produk_id, :quantity, :harga_per_item, :total_harga_item)
    ");
    foreach ($cartItems as $item) {
        $totalHargaItem = $item['harga'] * $item['quantity'];

        // Simpan item ke order_items
        $stmtOrderItem->execute([
            ':order_id' => $orderId,
            ':produk_id' => $item['produk_id'],
            ':quantity' => $item['quantity'],
            ':harga_per_item' => $item['harga'],
            ':total_harga_item' => $totalHargaItem
        ]);

        // 4. Update stok produk di tabel `produk`
        $stmtUpdateStock = $conn->prepare("
            UPDATE produk
            SET jumlah_stock = jumlah_stock - :quantity
            WHERE produk_id = :produk_id AND jumlah_stock >= :quantity
        ");
        $stmtUpdateStock->execute([
            ':quantity' => $item['quantity'],
            ':produk_id' => $item['produk_id']
        ]);

        // Periksa apakah update stok berhasil
        if ($stmtUpdateStock->rowCount() === 0) {
            // Jika stok tidak cukup, rollback transaksi dan beri respons error
            $conn->rollBack();
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient stock for product ID ' . $item['produk_id']]);
            exit();
        }
    }

    // 5. Hapus item dari `shopping_cart`
    $stmtClearCart = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = :user_id");
    $stmtClearCart->execute([':user_id' => $userId]);

    // Commit transaksi
    $conn->commit();

    // Kirim respons ke JavaScript
    echo json_encode(['success' => true, 'order_id' => $orderId]);
} catch (PDOException $e) {
    $conn->rollBack(); // Rollback jika terjadi kesalahan
    http_response_code(500);
    echo json_encode(['error' => 'Database connection error.', 'message' => $e->getMessage()]);
}
?>
