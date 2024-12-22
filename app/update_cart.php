<?php
session_start();

$isLoggedIn = isset($_SESSION['user_email']) && isset($_SESSION['user_name']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $produk_id = $data['produk_id'];
    $quantity = $data['quantity'];

    // Koneksi ke database
    $host = "localhost";
    $port = "5432";
    $dbname = "Web-Ecommerce";
    $dbUser = "postgres";
    $dbPassword = "456287";

    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser  , $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Update jumlah di keranjang
        if ($quantity <= 0) {
            // Jika quantity <= 0, hapus item dari keranjang
            $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = :user_id AND produk_id = :produk_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':produk_id', $produk_id);
            $stmt->execute();
        } else {
            // Update quantity
            $stmt = $conn->prepare("UPDATE shopping_cart SET quantity = :quantity WHERE user_id = :user_id AND produk_id = :produk_id");
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':produk_id', $produk_id);
            $stmt->execute();
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User  not logged in or invalid request']);
}
?>