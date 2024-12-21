<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['user_id'], $data['produk_id'])) {
    // Ambil data dari request
    $user_id = $data['user_id'];
    $produk_id = $data['produk_id'];

    // Konfigurasi database
    $host = "localhost";
    $port = "5432";
    $dbname = "Web-Ecommerce";
    $dbUser   = "postgres";
    $dbPassword = "456287";

    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser  , $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Hapus produk dari keranjang
        $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = :user_id AND produk_id = :produk_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':produk_id', $produk_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Produk berhasil dihapus dari keranjang']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus produk dari keranjang']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>