<?php
session_start();

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: dashboard-Default.php");
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
                header("Location: dashboard-Default.php");
                exit();
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }

        if (isset($_POST['logout'])) {
            // Logout
            session_destroy();
            header("Location: dashboard-Default.php");
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

<style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Poppins", sans-serif;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
  background-color: white;
}

/* Navbar */
.navbar {
  position: fixed;
  top: 0;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 5%;
  background: rgba(0, 0, 0, 0.5);
  color: white;
  z-index: 100;
  transition: all 0.3s ease;
}

.navbar a {
  color: white;
  text-decoration: none;
  margin: 0 1rem;
  font-size: 1rem;
  transition: color 0.3s;
}

.navbar a:hover {
  color: #f0c040;
}

.navbar-brand {
  font-weight: 600;
  font-size: 1.5rem;
  color: white;
}

.navbar-links {
  display: flex;
  align-items: center;
}

/* Hero Section */
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-image: url("../img/bg1.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  position: relative;
  filter: grayscale(100%);
}

.hero::after {
  content: "";
  display: block;
  position: absolute;
  width: 100%;
  height: 30%;
  bottom: 0;
  background: linear-gradient(
    0deg,
    rgba(1, 1, 3, 1) 8%,
    rgba(255, 255, 255, 0) 50%
  );
}

.hero-content {
  color: white;
  text-align: center;
  z-index: 1;
}

.hero-content h1 {
  font-size: 4rem;
  font-weight: 700;
  letter-spacing: 2px;
}

.hero-content p {
  font-size: 1.5rem;
  margin-top: 1rem;
}

.hero::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 0;
}

.spacer {
  height: 3000px;
}

/* Form Section */
.form-container {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(255, 255, 255, 1);
  padding: 30px;
  border-radius: 10px;
  width: 350px; /* Lebih lebar */
  max-width: 100%;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  z-index: 9999;
  display: none;
}

.form-container h2 {
  text-align: center;
  margin-bottom: 20px;
}

/* Tab styles */
.tabs {
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
}

.tabs div {
  font-size: 1.2rem;
  margin: 0 1rem;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s;
}

.tabs .active {
  background-color: black;
  color: white;
}

/* Form fields styles */
.form-group {
  margin-bottom: 1.5rem;
}

.form-container input {
  width: 100%;
  padding: 1rem;
  font-size: 1rem;
  border: 2px solid #ddd;
  border-radius: 5px;
  outline: none;
  transition: border 0.3s;
}

.form-container input:focus {
  border-color: black;
}

.form-container button {
  background-color: rgba(0, 0, 0, 0.1);
  color: white;
  padding: 12px;
  font-size: 1.2rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
  width: 100%;
}

.form-container button:hover {
  background-color: black;
}

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  z-index: 100;
  display: none;
}

.heading {
  text-align: center;
  font-size: 4rem;
  color: rgba(255, 255, 255, 0.462);
  padding: 1rem;
  margin: 2rem 0;
}
.heading span {
  color: white;
}

.about {
  background: linear-gradient(
    180deg,
    /* Gradien dari atas ke bawah */ rgba(0, 0, 0, 1) 0%,
    /* Hitam solid di bagian atas */ rgba(0, 0, 0, 0.8) 20%,
    /* Hitam dengan sedikit transparansi */ rgba(255, 255, 255, 0.5) 70%,
    /* Transisi ke putih */ rgba(255, 255, 255, 1) 100%
      /* Putih penuh di bagian bawah */
  );
  padding: 40px 20px;
  color: #fff; /* Warna teks agar tetap terlihat */
}

.about .content {
  max-width: 800px;
  margin: 0 auto;
  text-align: justify;
  line-height: 1.8;
  color: #000; /* Warna teks dalam konten menjadi hitam agar kontras */
}

.about .content h3 {
  text-align: center;
  margin-bottom: 20px;
  color: white;
}

.about .content {
  max-width: 1024px;
  margin: 0 auto;
  text-align: justify;
  line-height: 1.5;
}

.about .content h3 {
  text-align: left;
  margin-bottom: 20px;
}

.about .content p {
  margin: 15px 0;
}

    body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to bottom, #000, #333);
    color: #f5f5f5;
}

.profile {
    max-width: 1240px;
    margin: 50px auto;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5);
    padding: 20px 30px;
    text-align: center;
}

.profile h2 {
    font-size: 2rem;
    color: rgb(255, 255, 255);
    margin-bottom: 20px;
    display: inline-block;
    padding-bottom: 5px;
}

.profile form div {
    margin-bottom: 15px;
    text-align: left;
}

.profile label {
    display: block;
    font-weight: bold;
    color: gold;
}

.profile input,
.profile textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid rgb(255, 255, 255);
    border-radius: 5px;
    background: #111;
    color: #f5f5f5;
}

.profile button {
    background: gold;
    color: #111;
    font-size: 1rem;
    font-weight: bold;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 10px 0;
}

.profile button:hover {
    background: #f1c40f;
    transform: scale(1.05);
}

.profile p {
    margin-top: 20px;
    color: #f5f5f5;
    font-style: italic;
}
</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="/Main/dashboard.css">
    <link rel="stylesheet" href="/Main/profile.css"> -->
</head>
<body>
<nav class="navbar">
    <div class="navbar-brand">ReTech</div>
    <div class="navbar-links">
        <a href="../Main/dasboard-User.php">Home</a>
        <a href="#">Kategori</a>
        <a href="#">Produk</a>
        <a href="cart.html">
            <i class="fas fa-shopping-cart" title="Shopping Cart"></i>
        </a>
        <a href="../Main/profile.php">
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