<?php
    session_start();

    if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
        // Jika belum login, tetap dapat mengakses halaman
        $isLoggedIn = false;
    } else {
        $isLoggedIn = true;
        // Menyimpan nama user untuk ditampilkan di pop-up
        $user_name = $_SESSION['user_name'];
    }

    // Konfigurasi database
    $host = "localhost";
    $port = "5432";
    $dbname = "Web-Ecommerce";
    $dbUser = "postgres";
    $dbPassword = "postgres";

    try {
        // Koneksi ke database
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk mengambil produk yang ditambahkan oleh Penjual
        $stmt = $conn->prepare("SELECT p.produk_id, p.nama_produk, p.merk_produk, p.kategori, p.kondisi_barang, p.harga, p.jumlah_stock, p.deskripsi, p.gambar_produk, u.nama AS penjual_name 
                                FROM produk p
                                JOIN users u ON p.penjual_id = u.user_id
                                WHERE p.jumlah_stock > 0");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../main/dashboard.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">ReTech</div>
        <div class="navbar-links">
            <a href="../main/halaman-user.php">Home</a>
            <a href="#">Kategori</a>
            <a href="../Produk/produk-Main.php">Produk</a>
            <!-- Icon Shopping Cart -->
            <a href="cart.php">
                <i class="fas fa-shopping-cart" title="Shopping Cart"></i>
            </a>
            <!-- Icon Profile -->
            <a href="../main/halaman-profile.php">
                <i class="fas fa-user" title="Profile"></i>
            </a>
            <!-- Icon Logout -->
            <a href="logout.php">
                <i class="fas fa-sign-out-alt" title="Logout"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Our Website</h1>
            <p>Your one-stop solution for gadgets and technology</p>
        </div>
    </section>

    <!-- Pop-up -->
    <?php if ($isLoggedIn): ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container">
        <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Selamat datang, <strong><?php echo htmlspecialchars($user_name); ?></strong>!
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Produk Section -->
    <section class="products mt-5">
        <h2>Produk Tersedia</h2>
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($product['gambar_produk']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['deskripsi']); ?></p>
                                <p><strong>Merk:</strong> <?php echo htmlspecialchars($product['merk_produk']); ?></p>
                                <p><strong>Kondisi:</strong> <?php echo htmlspecialchars($product['kondisi_barang']); ?></p>
                                <p><strong>Harga:</strong> Rp <?php echo number_format($product['harga'], 2, ',', '.'); ?></p>
                                <p><strong>Stok:</strong> <?php echo htmlspecialchars($product['jumlah_stock']); ?> unit</p>
                                <p><strong>Penjual:</strong> <?php echo htmlspecialchars($product['penjual_name']); ?></p>

                                <!-- Form Tombol Masukkan ke Keranjang -->
                                <form action="cart.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['produk_id']); ?>">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                                    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['harga']); ?>">
                                    <input type="hidden" name="product_quantity" value="1"> <!-- Default jumlah = 1 -->
                                    <button type="submit" class="btn btn-primary">Masukkan ke Keranjang</button>
                                </form>

                                <!-- Tombol Beli Sekarang -->
                                <a href="#" class="btn btn-success">Beli Sekarang</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada produk yang tersedia.</p>
            <?php endif; ?>
        </div>
    </section>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastElement = document.querySelector('.toast');
            const toast = new bootstrap.Toast(toastElement, { delay: 5000 }); 
            toast.show();
        });
    </script>
</body>
</html>
