<?php
session_start();

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: ../main/halaman-default.php");
    exit();
}

// Konfigurasi database
$host = "localhost";
$port = "5432";
$dbname = "Web-Ecommerce";
$dbUser = "postgres";
$dbPassword = "postgres";

$message = "";

try {
    // Koneksi ke database
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_SESSION['user_id']; // Ambil user_id dari session

    // Ambil data produk pengguna
    $stmt = $conn->prepare("SELECT produk_id, nama_produk, merk_produk, kategori, kondisi_barang, harga, jumlah_stock, deskripsi, gambar_produk FROM produk WHERE penjual_id = :penjual_id");
    $stmt->execute([':penjual_id' => $userId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['edit_product'])) {
            // Ambil data yang dikirimkan melalui form edit produk
            $productId = $_POST['product_id'];
            $namaProduk = $_POST['nama_produk'];
            $merkProduk = $_POST['merk_produk'];
            $kategori = $_POST['kategori'];
            $kondisiBarang = $_POST['kondisi_barang'];
            $harga = $_POST['harga'];
            $jumlahStock = $_POST['jumlah_stock'];
            $deskripsi = $_POST['deskripsi'];
            $gambarProduk = $_POST['gambar_produk'];

            try {
                // Update data produk ke database
                $stmt = $conn->prepare("UPDATE produk SET nama_produk = :nama_produk, merk_produk = :merk_produk, kategori = :kategori, kondisi_barang = :kondisi_barang, harga = :harga, jumlah_stock = :jumlah_stock, deskripsi = :deskripsi, gambar_produk = :gambar_produk WHERE produk_id = :produk_id");
                $stmt->execute([
                    ':nama_produk' => $namaProduk,
                    ':merk_produk' => $merkProduk,
                    ':kategori' => $kategori,
                    ':kondisi_barang' => $kondisiBarang,
                    ':harga' => $harga,
                    ':jumlah_stock' => $jumlahStock,
                    ':deskripsi' => $deskripsi,
                    ':gambar_produk' => $gambarProduk,
                    ':produk_id' => $productId
                ]);
                $message = "Produk berhasil diperbarui.";
                // Ambil data produk setelah update
                $stmt = $conn->prepare("SELECT produk_id, nama_produk, merk_produk, kategori, kondisi_barang, harga, jumlah_stock, deskripsi, gambar_produk FROM produk WHERE penjual_id = :penjual_id");
                $stmt->execute([':penjual_id' => $userId]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        } elseif (isset($_POST['delete_product'])) {
            // Hapus produk
            $productId = $_POST['product_id'];

            try {
                $stmt = $conn->prepare("DELETE FROM produk WHERE produk_id = :produk_id");
                $stmt->execute([':produk_id' => $productId]);
                $message = "Produk berhasil dihapus.";
                // Ambil data produk setelah dihapus
                $stmt = $conn->prepare("SELECT produk_id, nama_produk, merk_produk, kategori, kondisi_barang, harga, jumlah_stock, deskripsi, gambar_produk FROM produk WHERE penjual_id = :penjual_id");
                $stmt->execute([':penjual_id' => $userId]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    }

} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../main/dashboard.css">
    <link rel="stylesheet" href="../main/profile.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../app/halaman-default-baru.css">
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
    <style>
        .sticky-note {
            background-color: black;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 10px;
        }
        .sticky-note h4 {
            margin-bottom: 15px;
        }
        .sticky-note .btn {
            margin-top: 10px;
        }
    </style>
</head>
<script src="https://unpkg.com/feather-icons"></script>
<body>
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
            <li><a href="#">Kategori</a></li>
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

<section class="profile">
    <h2>Edit Produk</h2>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <div class="container">
        <div class="row">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3">
                        <div class="sticky-note">
                            <h4><?php echo htmlspecialchars($product['nama_produk']); ?></h4>
                            <p><strong>Merk:</strong> <?php echo htmlspecialchars($product['merk_produk']); ?></p>
                            <p><strong>Kategori:</strong> <?php echo htmlspecialchars($product['kategori']); ?></p>
                            <p><strong>Kondisi:</strong> <?php echo htmlspecialchars($product['kondisi_barang']); ?></p>
                            <p><strong>Harga:</strong> Rp <?php echo htmlspecialchars($product['harga']); ?></p>
                            <p><strong>Stok:</strong> <?php echo htmlspecialchars($product['jumlah_stock']); ?></p>
                            <button 
                                class="btn btn-primary" 
                                data-toggle="modal" 
                                data-target="#editModal<?php echo $product['produk_id']; ?>">Edit</button>
                            <form action="edit-product.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['produk_id']); ?>">
                                <button type="submit" name="delete_product" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Edit Produk -->
                    <div class="modal fade" id="editModal<?php echo $product['produk_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $product['produk_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel<?php echo $product['produk_id']; ?>">Edit Produk</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="edit-product.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['produk_id']); ?>">
                                        <div class="form-group">
                                            <label for="nama_produk">Nama Produk:</label>
                                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="merk_produk">Merk Produk:</label>
                                            <input type="text" class="form-control" id="merk_produk" name="merk_produk" value="<?php echo htmlspecialchars($product['merk_produk']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="kategori">Kategori:</label>
                                            <input type="text" class="form-control" id="kategori" name="kategori" value="<?php echo htmlspecialchars($product['kategori']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="kondisi_barang">Kondisi Barang:</label>
                                            <input type="text" class="form-control" id="kondisi_barang" name="kondisi_barang" value="<?php echo htmlspecialchars($product['kondisi_barang']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="harga">Harga:</label>
                                            <input type="number" class="form-control" id="harga" name="harga" value="<?php echo htmlspecialchars($product['harga']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="jumlah_stock">Jumlah Stok:</label>
                                            <input type="number" class="form-control" id="jumlah_stock" name="jumlah_stock" value="<?php echo htmlspecialchars($product['jumlah_stock']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi:</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($product['deskripsi']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="gambar_produk">URL Gambar Produk:</label>
                                            <input type="text" class="form-control" id="gambar_produk" name="gambar_produk" value="<?php echo htmlspecialchars($product['gambar_produk']); ?>">
                                        </div>
                                        <button type="submit" name="edit_product" class="btn btn-success">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada produk yang ditambahkan.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
  feather.replace();
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

