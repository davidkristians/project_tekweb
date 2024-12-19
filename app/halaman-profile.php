<?php
session_start();

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: ../app/halaman-default.php");
    exit();
}

// Konfigurasi database
$host = "localhost";
$port = "5432";
$dbname = "Web-Ecommerce";
$dbUser = "postgres";
$dbPassword = "456287";

$message = "";

try {
    // Koneksi ke database
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_SESSION['user_id']; // Ambil user_id dari session

    // Fungsi untuk mendapatkan data pengguna
    function getUserData($conn, $userId) {
        $stmt = $conn->prepare("SELECT nama, email, nomor_telepon, alamat, role FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $user = getUserData($conn, $userId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update'])) {
            // Update data pengguna
            $newPhone = $_POST['phone'] ?? $user['nomor_telepon'];
            $newAddress = $_POST['address'] ?? $user['alamat'];

            try {
                $stmt = $conn->prepare("UPDATE users SET nomor_telepon = :phone, alamat = :address WHERE user_id = :user_id");
                $stmt->execute([
                    ':phone' => $newPhone,
                    ':address' => $newAddress,
                    ':user_id' => $userId
                ]);
                $message = "Data berhasil diperbarui.";
                $user = getUserData($conn, $userId);
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }

        if (isset($_POST['delete_account'])) {
            // Hapus akun
            try {
                $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $userId]);
                session_destroy();
                header("Location: ../app/halaman-default.php");
                exit();
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }

        if (isset($_POST['logout'])) {
            // Logout
            session_destroy();
            header("Location: ../app/halaman-default");
            exit();
        }

        if (isset($_POST['add_product']) && $user['role'] === 'Penjual') {
            // Tambahkan barang dagangan
            $productName = $_POST['product_name'];
            $productBrand = $_POST['product_brand'];
            $productCategory = $_POST['product_category'];
            $productCondition = $_POST['product_condition'];
            $productPrice = $_POST['product_price'];
            $productStock = $_POST['product_stock'];
            $productDescription = $_POST['product_description'];
            $productImageUrl = $_POST['product_image_url'];

            // Validasi URL
            if (!filter_var($productImageUrl, FILTER_VALIDATE_URL)) {
                $message = "URL foto produk tidak valid.";
            } else {
                try {
                    $stmt = $conn->prepare("INSERT INTO produk (penjual_id, nama_produk, merk_produk, kategori, kondisi_barang, harga, jumlah_stock, deskripsi, gambar_produk) 
                                            VALUES (:penjual_id, :nama_produk, :merk_produk, :kategori, :kondisi_barang, :harga, :jumlah_stock, :deskripsi, :gambar_produk)");
                    $stmt->execute([
                        ':penjual_id' => $userId,
                        ':nama_produk' => $productName,
                        ':merk_produk' => $productBrand,
                        ':kategori' => $productCategory,
                        ':kondisi_barang' => $productCondition,
                        ':harga' => $productPrice,
                        ':jumlah_stock' => $productStock,
                        ':deskripsi' => $productDescription,
                        ':gambar_produk' => $productImageUrl
                    ]);
                    $message = "Produk berhasil ditambahkan.";
                } catch (PDOException $e) {
                    $message = "Error: " . $e->getMessage();
                }
            }
        }
    }

    // Ambil data produk pengguna
    $stmt = $conn->prepare("SELECT nama_produk, merk_produk, kategori, kondisi_barang, harga, jumlah_stock, deskripsi, gambar_produk FROM produk WHERE penjual_id = :penjual_id");
    $stmt->execute([':penjual_id' => $userId]);
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
    <title>Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../app/halaman-default.css">
    <link rel="stylesheet" href="../app/profile.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-brand">ReTech</div>
    <div class="navbar-links">
        <a href="../main/halaman-user.php">Home</a>
        <a href="#">Kategori</a>
        <a href="#">Produk</a>
        <a href="cart.html"> <!--Ubah sesuai dengan produk.php -->
            <i class="fas fa-shopping-cart" title="Shopping Cart"></i>
        </a>
        <a href="../main/halaman-profile.css">
            <i class="fas fa-user" title="Profile"></i>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt" title="Logout"></i>
        </a>
    </div>
</nav>

<section class="profile">
    <h2>Profile</h2>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Form Update Profile -->
    <form action="profile.php" method="POST">
        <div>
            <label for="name">Nama:</label>
            <input type="text" id="name" value="<?php echo htmlspecialchars($user['nama']); ?>" disabled>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>
        <div>
            <label for="role">Role:</label>
            <input type="text" id="role" value="<?php echo htmlspecialchars($user['role']); ?>" disabled>
        </div>
        <div>
            <label for="phone">Nomor Telepon:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['nomor_telepon']); ?>">
        </div>
        <div>
            <label for="address">Alamat:</label>
            <textarea id="address" name="address"><?php echo htmlspecialchars($user['alamat']); ?></textarea>
        </div>
        <button type="submit" name="update">Update</button>
    </form>

<!-- Form untuk Menambahkan Produk -->
    <?php if ($user['role'] === 'Penjual'): ?>
        <h3>Tambah Produk</h3>
        <form action="profile.php" method="POST">
            <!-- Nama Produk -->
            <label for="product_name">Nama Produk:</label>
            <input type="text" id="product_name" name="product_name" required>
            <br>

            <!-- Merek Produk -->
            <label for="product_brand">Merk Produk:</label>
            <input type="text" id="product_brand" name="product_brand" required>
            <br>

            <!-- Kategori Produk -->
            <label for="product_category">Kategori:</label>
            <select id="product_category" name="product_category" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="HP">HP</option>
                <option value="Laptop">Laptop</option>
                <option value="Tablet">Tablet</option>
                <option value="TV">TV</option>
            </select>
            <br>

            <!-- Kondisi Barang -->
            <label for="product_condition">Kondisi Barang:</label>
            <select id="product_condition" name="product_condition" required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="70%">70%</option>
                <option value="80%">80%</option>
                <option value="90%">90%</option>
                <option value="Baru">Baru</option>
            </select>
            <br>

            <!-- Harga Produk -->
            <label for="product_price">Harga (dalam Rp):</label>
            <input type="number" id="product_price" name="product_price" min="1" step="0.01" required>
            <br>

            <!-- Stok Produk -->
            <label for="product_stock">Jumlah Stok:</label>
            <input type="number" id="product_stock" name="product_stock" min="1" required>
            <br>

            <!-- Deskripsi Produk -->
            <label for="product_description">Deskripsi:</label>
            <textarea id="product_description" name="product_description" rows="5" required></textarea>
            <br>

            <!-- URL Gambar Produk -->
            <label for="product_image_url">URL Foto Produk:</label>
            <input type="url" id="product_image_url" name="product_image_url" required>
            <br>

            <!-- Tombol Submit -->
            <button type="submit" name="add_product">Tambah Produk</button>
        </form>
        <a href="edit-product.php">
            <button type="button">Edit Produk</button>
        </a>
    <?php endif; ?>





    <form action="profile.php" method="POST">
        <button type="submit" name="delete_account" onclick="return confirm('Apakah Anda yakin ingin menghapus akun Anda?')">Hapus Akun</button>
    </form>

    <form action="profile.php" method="POST">
        <button type="submit" name="logout">Logout</button>
    </form>
</section>
</body>
</html>