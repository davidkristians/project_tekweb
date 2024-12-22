<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Koneksi ke database
$host = "localhost";
$port = "5432";
$dbname = "Web-Ecommerce";
$dbUser  = "postgres";
$dbPassword = "456287";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser , $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hapus semua item dari keranjang
    $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>