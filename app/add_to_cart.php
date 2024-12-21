<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['user_id'], $data['produk_id'], $data['quantity'])) {
    // Ambil data dari request
    $user_id = $data['user_id'];
    $produk_id = $data['produk_id'];
    $quantity = $data['quantity'];

    // Konfigurasi database
    $host = "localhost";
    $port = "5432";
    $dbname = "Web-Ecommerce";
    $dbUser  = "postgres";
    $dbPassword = "456287";

    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser , $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ambil stok produk dari tabel produk
        $stmt = $conn->prepare("SELECT jumlah_stock FROM produk WHERE produk_id = :produk_id");
        $stmt->bindParam(':produk_id', $produk_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            exit();
        }

        $stok = $product['jumlah_stock'];

        // Validasi jumlah yang dimasukkan
        if ($quantity > $stok) {
            echo json_encode(['success' => false, 'message' => 'Jumlah melebihi stok yang tersedia']);
            exit();
        }

        // Cek apakah produk sudah ada dalam keranjang
        $stmt = $conn->prepare("SELECT * FROM shopping_cart WHERE user_id = :user_id AND produk_id = :produk_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':produk_id', $produk_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Produk sudah ada dalam keranjang, update quantity
            $stmt = $conn->prepare("UPDATE shopping_cart SET quantity = quantity + :quantity 
                                    WHERE user_id = :user_id AND produk_id = :produk_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':produk_id', $produk_id);
            $stmt->bindParam(':quantity', $quantity);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Produk berhasil diperbarui dalam keranjang']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui produk dalam keranjang']);
            }
        } else {
            // Produk belum ada dalam keranjang, insert ke dalam tabel shopping_cart
            $stmt = $conn->prepare("INSERT INTO shopping_cart (user_id, produk_id, quantity) 
                                    VALUES (:user_id, :produk_id, :quantity)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':produk_id', $produk_id);
            $stmt->bindParam(':quantity', $quantity);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan produk ke keranjang']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>