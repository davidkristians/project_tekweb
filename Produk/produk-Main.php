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

    $host = "localhost";
    $port = "5432";
    $dbname = "Web-Ecommerce";
    $dbUser = "postgres";
    $dbPassword = "postgres";

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../main/dashboard.css">
    <link rel="stylesheet" href="../Produk/produk.css">
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

    <!-- Shopping Cart Sidebar -->
    <div class="cart-sidebar">
        <h2>Keranjang Belanja</h2>
        <div class="cart-list"></div>
        <div class="cart-total">
            <p>Total: <span class="total-price">0</span> IDR</p>
            <button class="checkout-btn">Checkout</button>
        </div>
    </div>


    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">ReTech</div>
        <div class="navbar-links">
            <a href="/Main/dasboard-User.php">Home</a>
            <a href="#">Kategori</a>
            <a href="/Produk/produk.php ">Produk</a>
            <!-- Icon Shopping Cart -->
            <a class="cart-icon" onclick="toggleCart()">
                <i class="fas fa-shopping-cart" title="Shopping Cart"></i><span class="cart-count">0</span>
            </a>
            <!-- Icon Profile -->
            <a href="/Main/profile.php">
                <i class="fas fa-user" title="Profile"></i>
            </a>
            <!-- Icon Logout -->
            <a href="/Main/logout.php">
                <i class="fas fa-sign-out-alt" title="Logout"></i>
            </a>
        </div>
    </nav>

    <!-- Produk -->
    <div class="product-container">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <img src="<?= htmlspecialchars($product['gambar_produk']); ?>" alt="<?= htmlspecialchars($product['nama_produk']); ?>">
                <h3><?= htmlspecialchars($product['merk_produk']); ?> - <?= htmlspecialchars($product['nama_produk']); ?></h3>
                <p>Harga: <?= number_format($product['harga'], 0, ',', '.'); ?> IDR</p>
                <button onclick="addToCart(<?= $product['produk_id']; ?>)">Tambahkan ke Keranjang</button>
            </div>
        <?php endforeach; ?>
    </div>


    <script>
        // Produk dari database
        let products = <?php echo json_encode($products, JSON_HEX_TAG); ?>;

        // Keranjang belanja
        let cart = [];

        function toggleCart() {
            document.querySelector('.cart-sidebar').classList.toggle('active');
        }

        function addToCart(id) {
            let product = products.find(p => p.produk_id == id);
            if (product) {
                let cartItem = cart.find(c => c.produk_id == id);
                if (cartItem) {
                    cartItem.quantity++;
                } else {
                    cart.push({ ...product, quantity: 1 });
                }
                updateCart();
            }
        }

        function updateCart() {
            const cartList = document.querySelector('.cart-list');
            const totalPrice = document.querySelector('.total-price');
            const cartCount = document.querySelector('.cart-count');

            cartList.innerHTML = '';
            let total = 0;
            let count = 0;

            cart.forEach(item => {
                total += item.harga * item.quantity;
                count += item.quantity;

                const cartItem = document.createElement('div');
                cartItem.classList.add('cart-item');
                cartItem.innerHTML = `
                    <p>${item.nama_produk} x ${item.quantity}</p>
                    <p>${(item.harga * item.quantity).toLocaleString()} IDR</p>
                    <button onclick="changeQuantity(${item.produk_id}, ${item.quantity - 1})">-</button>
                    <button onclick="changeQuantity(${item.produk_id}, ${item.quantity + 1})">+</button>
                `;
                cartList.appendChild(cartItem);
            });

            totalPrice.textContent = total.toLocaleString();
            cartCount.textContent = count;
        }

        function changeQuantity(id, quantity) {
            let cartItem = cart.find(c => c.produk_id == id);
            if (cartItem) {
                if (quantity === 0) {
                    cart = cart.filter(c => c.produk_id != id);
                } else {
                    cartItem.quantity = quantity;
                }
                updateCart();
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
