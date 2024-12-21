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
    <title>Profil Saya | Redget.</title>

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
    

     <!-- CSS UNTUK SEMUA HALAMAN -->
     <link rel="stylesheet" href="../public/css/style.css">
    <!-- CSS KHUSUS UNTUK HALAMAN INI -->
    <link rel="stylesheet" href="../app/halaman-profile-baru.css">
</head>

<!-- FEATHER ICON -->
<script src="https://unpkg.com/feather-icons"></script>

<body>
<!--=====NAVBAR MODIFIED FIXED=====-->
<nav class="navbar">
      <div class="navbar_contents">
        <div class="logo">
          <img src="../public/img/logo/redget_logo.png" alt="Redget">
        </div>
        <ul class="nav-links" style=
        "padding-left: 0;
        margin-bottom: 0;
        ">
            <li><a href="../app/halaman-user.php">Home</a></li>
            <li><a href="#">Kategori</a></li>
            <li><a href="#">Promo</a></li>
        </ul>
        <div class="nav-icons">
            <a href="../app/shopping_cart.php"><i data-feather="shopping-cart"></i></a>
            <a href="../app/halaman-profile.php" id="open-form-btn"><i data-feather="user"></i></a>
            <a href="../app/logout.php">
                <i data-feather="log-out"></i>
            </a>
        </div>
      </div>
    </nav>

<!-- <nav class="navbar">
    <div class="navbar-brand">ReTech</div>
    <div class="navbar-links">
        <a href="../main/halaman-user.php">Home</a>
        <a href="#">Kategori</a>
        <a href="#">Produk</a>
        <a href="cart.html"> Ubah sesuai dengan produk.php
            <i class="fas fa-shopping-cart" title="Shopping Cart"></i>
        </a>
        <a href="../main/halaman-profile.css">
            <i class="fas fa-user" title="Profile"></i>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt" title="Logout"></i>
        </a>
    </div>
</nav> -->

<section class="profile">
    <h2>Profil Saya</h2>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Form Update Profile -->
    <form action="halaman-profile.php" method="POST">
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
        <button class="change-detail acc" type="submit" name="update">Ubah Detail Akun Anda</button>
    </form>

<!-- Form untuk Menambahkan Produk -->
    <?php if ($user['role'] === 'Penjual'): ?>
        <h3>Tambah Produk</h3>
        <form action="halaman-profile.php" method="POST">
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
            <button class="add-btn" type="submit" name="add_product">Tambah Produk</button>
        </form>
        <a href="edit-product.php">
            <button class="edit-btn"type="button">Edit Produk</button>
        </a>
    <?php endif; ?>


    <!-- HAPUS AKUN atau LOG OUT -->
     <div class="del-or-log-out">
         <form action="halaman-profile.php" method="POST">
             <button class="logout-btn" type="submit" name="logout">Logout</button>
         </form>
         <form action="halaman-profile.php" method="POST">
             <button class="delete-acc-btn" type="submit" name="delete_account" onclick="return confirm('Apakah Anda yakin ingin menghapus akun Anda?')">Hapus Akun</button>
         </form>
     </div>
</section>

<!--=====FOOTER MODIFIED FIXED=====-->
<footer>
  <div class="footer-container">
    <div class="footer-left">
      <div class="logo">
        <img src="../public/img/logo/redget_logo.png" alt="">
      </div>
    </div>

    <div class="deskripsi">
    <h1>
  <a href="" class="typewrite" data-period="2000" data-type='[ "adalah Platform Jual Beli Barang Bekas Berkualitas Tinggi.", "adalah tempat menemukan gadget impian dengan harga murah!", "adalah Platform Jual Beli Barang Bekas Berkualitas Tinggi.", "adalah tempat menemukan gadget impian dengan harga murah!" ]'>
    <span class="wrap"></span>
  </a>
</h1>
    </div>
  </div>
  <div class="footer-bottom">
    <p><i class="bi bi-c-circle"></i> Redget 2024 | Hak Cipta Dilindungi</p>
  </div>
</footer>




<!-- FEATHER ICONS SCRIPT -->
<script>
  feather.replace();
</script>

<!-- TYPEWRITER EFFECT FOOTER -->
<script>
var TxtType = function(el, toRotate, period) {
        this.toRotate = toRotate;
        this.el = el;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 2000;
        this.txt = '';
        this.tick();
        this.isDeleting = false;
    };

    TxtType.prototype.tick = function() {
        var i = this.loopNum % this.toRotate.length;
        var fullTxt = this.toRotate[i];

        if (this.isDeleting) {
        this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
        this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

        var that = this;
        // UNTUK MEMPERCEPAT ANIMASI KETIK
        // DEFAULT VALUE : delta = 200 - Math.random() * 100
        var delta = 100 - Math.random() * 100;

        if (this.isDeleting) { delta /= 2; }

        if (!this.isDeleting && this.txt === fullTxt) {
        delta = this.period;
        this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
        this.isDeleting = false;
        this.loopNum++;
        delta = 500;
        }

        setTimeout(function() {
        that.tick();
        }, delta);
    };

    window.onload = function() {
        var elements = document.getElementsByClassName('typewrite');
        for (var i=0; i<elements.length; i++) {
            var toRotate = elements[i].getAttribute('data-type');
            var period = elements[i].getAttribute('data-period');
            if (toRotate) {
              new TxtType(elements[i], JSON.parse(toRotate), period);
            }
        }
        var css = document.createElement("style");
        css.type = "text/css";
        css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #000}";
        document.body.appendChild(css);
    };
    </script>

</body>
</html>