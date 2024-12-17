<?php
    // Koneksi ke database
    $host = "localhost";
    $port = "5432";
    $dbname = "Web-Ecommerce";
    $dbUser = "postgres";
    $dbPassword = "postgres";


    $message = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["nama"] ?? "";
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";
        $phone = $_POST["nomor_telepon"] ?? "";
        $address = $_POST["alamat"] ?? "";
        $role = $_POST["role"] ?? "";

        // Cek Password
        if ($password !== $_POST['confirm_password']) {
          $message = "Password dan konfirmasi password tidak cocok!";
          exit(); // Berhenti eksekusi jika password tidak cocok
        }
      

        // Role
        if ($role === "buyer") {
            $role = "Pembeli";
        } elseif ($role === "seller") {
            $role = "Penjual";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO users (nama, email, password, nomor_telepon, alamat, role) 
                    VALUES (:name, :email, :password, :phone, :address, :role)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":name" => $name,
                ":email" => $email,
                ":password" => $hashedPassword,
                ":phone" => $phone,
                ":address" => $address,
                ":role" => $role
            ]);

            $message = "Registrasi berhasil!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }

    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk mengambil produk yang ditambahkan oleh penjual
        $stmt = $conn->prepare("SELECT p.produk_id, p.nama_produk, p.merk_produk, p.kondisi_barang, p.harga, p.jumlah_stock, p.deskripsi, p.gambar_produk, u.nama AS penjual_name 
                                FROM produk p
                                JOIN users u ON p.penjual_id = u.user_id
                                WHERE p.jumlah_stock > 0"); // Hanya produk dengan stok > 0
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Tersedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Main/dashboard.css">
    <style>
        body {
            background-color: #f8f9fa; 
        }

        .container {
            margin-top: 2rem;
            padding: 4rem;
        }

        .product-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
    <div class="navbar-brand">ReTech</div>
        <div class="navbar-links">
        <a href="/Main/dashboard-Default.php">Home</a>
        <a href="#">Kategori</a>
        <a href="#">Produk</a>
        <a href="#" id="open-form-btn">Masuk/Daftar</a>
        </div>
    </nav>

    <div id="overlay" class="overlay"></div>

    <!-- Form Section (Pendaftaran/Login) -->
    <div id="form-container" class="form-container">
    <h2 id="form-title">Create Account</h2>
    <div class="tabs">
        <div id="register-tab" class="active" onclick="toggleForm('register')">Register</div>
        <div id="login-tab" onclick="toggleForm('login')">Login</div>
    </div>
    <!-- Register Form -->
        <form id="register-form" class="form-content" action="#" method="POST">
        <div class="form-group">
            <input type="text" placeholder="Full Name" name="nama" required>
        </div>
        <div class="form-group">
            <input type="email" placeholder="Email Address" name="email" required>
        </div>
        <div class="form-group">
            <input type="password" placeholder="Password" name="password" required>
        </div>
        <div class="form-group">
            <input type="password" placeholder="Confirm Password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <input type="text" placeholder="Phone Number" name="nomor_telepon">
        </div>
        <div class="form-group">
            <textarea placeholder="Address" name="alamat"></textarea>
        </div>
        <div class="input-box">
            <select name="role" class="input-field" required>
                <option value="" disabled selected>Pilih Role</option>
                <option value="Penjual">Penjual</option>
                <option value="Pembeli">Pembeli</option>
            </select>
        </div>
        <button type="submit" id="submit">Register</button>
    </form>

    <!-- Login Form -->
    <form id="login-form" class="form-content" action="/Main/login.php" method="POST" style="display: none;">
        <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit">Login</button>
    </form> 
    </div>

    <!-- Produk Section -->
    <div class="container">
        <h2 class="text-center mb-4">Produk Tersedia</h2>
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <img src="<?php echo htmlspecialchars($product['gambar_produk']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['deskripsi']); ?></p>
                                <p><strong>Merk:</strong> <?php echo htmlspecialchars($product['merk_produk']); ?></p>
                                <p><strong>Kondisi:</strong> <?php echo htmlspecialchars($product['kondisi_barang']); ?></p>
                                <p><strong>Harga:</strong> Rp <?php echo number_format($product['harga'], 2, ',', '.'); ?></p>
                                <p><strong>Stok:</strong> <?php echo htmlspecialchars($product['jumlah_stock']); ?> unit</p>
                                <p><strong>Penjual:</strong> <?php echo htmlspecialchars($product['penjual_name']); ?></p>
                                <a href="#" class="btn btn-primary w-100">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada produk yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>


    <script>
    // Menampilkan form dan overlay ketika tombol Daftar/Masuk ditekan
        const openFormButton = document.getElementById('open-form-btn');
        const formContainer = document.getElementById('form-container');
        const overlay = document.getElementById('overlay');

        openFormButton.addEventListener('click', function() {
        formContainer.style.display = 'block';
        overlay.style.display = 'block';
        });

        // Menyembunyikan form dan overlay ketika overlay ditekan
        overlay.addEventListener('click', function() {
        formContainer.style.display = 'none';
        overlay.style.display = 'none';
        });

        function toggleForm(formType) {
            const registerForm = document.getElementById('register-form');
            const loginForm = document.getElementById('login-form');
            const registerTab = document.getElementById('register-tab');
            const loginTab = document.getElementById('login-tab');
            const formTitle = document.getElementById('form-title');

            if (formType === 'register') {
                registerForm.style.display = 'block';
                loginForm.style.display = 'none';
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                formTitle.textContent = 'Create Account';
            } else {
                registerForm.style.display = 'none';
                loginForm.style.display = 'block';
                registerTab.classList.remove('active');
                loginTab.classList.add('active');
                formTitle.textContent = 'Login to Your Account';
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
