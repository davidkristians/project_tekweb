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

    // Mengambil Data Produk
    $produkData = [];
    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
        $sql = "SELECT * FROM produk";
        $stmt = $conn->query($sql);
        $produkData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }

    $cartItems = [];

    if ($isLoggedIn) {
        try {
            $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
            $stmt = $conn->prepare("SELECT p.produk_id, p.nama_produk, p.harga, p.gambar_produk, c.quantity 
                                    FROM shopping_cart c
                                    JOIN produk p ON c.produk_id = p.produk_id
                                    WHERE c.user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redget | Jual Beli Barang Bekas Berkualitas</title>

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
    <link rel="stylesheet" href="../app/halaman-default-baru-2.css">
    <link rel="stylesheet" href="../Produk/produk.css">
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
            <li><a href="#">Home</a></li>
            <li><a href="#products">Kategori</a></li>
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

    <!--=====HERO MODIFIED FIXED=====-->
    <section class="carousel">
      <div class="carousel-track" id="carouselTrack">
        <div class="carousel-slide">
          <img src="../public/img/banner_hero/banner1.png" alt="banner1" />
        </div>
        <div class="carousel-slide">
          <img src="../public/img/banner_hero/banner2.png" alt="banner2" />
        </div>
        <div class="carousel-slide">
          <img src="../public/img/banner_hero/banner3.png" alt="banner3" />
        </div>
      </div>
      <div class="carousel-nav">
        <button class="carousel-btn" id="prevBtn">&#10094;</button>
        <button class="carousel-btn" id="nextBtn">&#10095;</button>
      </div>
    </section>

    <!-- SHOPPING CART SIDEBAR -->
    <div class="cart-sidebar">
        <h2>Keranjang Belanja</h2>
        <div class="cart-list">
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-content">
                            <img src="<?= htmlspecialchars($item['gambar_produk']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="cart-item-image">
                        <div class="cart-item-details">
                            <p class="cart-item-name"><?= htmlspecialchars($item['nama_produk']) ?> x <?= htmlspecialchars($item['quantity']) ?></p>
                            <p class="cart-item-price"><?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?> IDR</p>
                        </div>
                        <div class="cart-item-actions">
                            <button onclick="changeQuantity(<?= $item['produk_id'] ?>, <?= $item['quantity'] - 1 ?>)">-</button>
                            <button onclick="changeQuantity(<?= $item['produk_id'] ?>, <?= $item['quantity'] + 1 ?>)">+</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Keranjang belanja kosong.</p>
        <?php endif; ?>
    </div>
    <div class="cart-total">
        <p>Total: <span class="total-price"><?= number_format(array_sum(array_column($cartItems, 'harga')), 0, ',', '.') ?></span> IDR</p>
        <button class="checkout-btn">Checkout</button>
    </div>
</div>

<!-- PRODUCT FIXED MODIFIED -->
<?php if (!empty($produkData)): ?>
<section class="produk_kami">
    <h1>Belanja Sekarang</h1>
</section>
<section class="products">
    <?php foreach ($produkData as $produk): ?>
        <div class="card">
            <img src="<?= htmlspecialchars($produk['gambar_produk']) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>" />
            <div class="judul_deskripsi_harga">
                <h3><?= htmlspecialchars($produk['nama_produk']) ?></h3>
                <p><?= htmlspecialchars($produk['deskripsi']) ?></p>
                <p><em><?= htmlspecialchars($produk['kondisi_barang']) ?></em></p>
                <div class="price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div>
                <div class="stock">Stok Tersedia: <?= htmlspecialchars($produk['jumlah_stock']) ?></div> <!-- Menampilkan stok produk -->
                <button class="tambah-keranjang" onclick="addToCart(<?= $produk['produk_id']; ?>)">Tambahkan ke Keranjang</button>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Produk belum tersedia.</p>
<?php endif; ?>
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

    



    <!-- TOAST SELAMAT DATANG -->
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



    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastElement = document.querySelector('.toast');
            const toast = new bootstrap.Toast(toastElement, { delay: 5000 }); 
            toast.show();
        });
    </script>

<!-- FEATHER ICONS SCRIPT -->
<script>
  feather.replace();
</script>

<!-- HERO SECTION CAROUSEL SCRIPT -->
<script>
      const track = document.getElementById("carouselTrack");
      const prevBtn = document.getElementById("prevBtn");
      const nextBtn = document.getElementById("nextBtn");
      const slides = Array.from(track.children);
      const slideWidth = slides[0].getBoundingClientRect().width;
      let currentIndex = 0;

      function moveToSlide(index) {
        track.style.transform = `translateX(-${index * slideWidth}px)`;
        currentIndex = index;
      }

      prevBtn.addEventListener("click", () => {
        const newIndex = currentIndex === 0 ? slides.length - 1 : currentIndex - 1;
        moveToSlide(newIndex);
      });

      nextBtn.addEventListener("click", () => {
        const newIndex = currentIndex === slides.length - 1 ? 0 : currentIndex + 1;
        moveToSlide(newIndex);
      });
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

                // Kirim data ke server untuk disimpan di database
                const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
                if (userId) {
                    fetch('../app/add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            produk_id: id,
                            quantity: 1
                        })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Produk berhasil ditambahkan ke keranjang');
                        } else {
                            console.error('Gagal menambahkan produk ke keranjang');
                        }
                    }).catch(error => console.error('Error:', error));
                }
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
        total += item.harga * item.quantity; // INCREMENT TOTAL HARGA
        count += item.quantity; // INCREMENT JUMLAH KERANJANG

            const cartItem = document.createElement('div');
            cartItem.classList.add('cart-item');
            cartItem.innerHTML = `
                <div class="cart-item-content">
                    <img src="${item.gambar_produk}" alt="${item.nama_produk}" class="cart-item-image">
                    <div class="cart-item-details">
                        <p class="cart-item-name">${item.nama_produk} x ${item.quantity}</p>
                        <p class="cart-item-price">${(item.harga * item.quantity).toLocaleString()} IDR</p>
                    </div>
                    <div class="cart-item-actions">
                        <button onclick="changeQuantity(${item.produk_id}, ${item.quantity - 1})">-</button>
                        <button onclick="changeQuantity(${item.produk_id}, ${item.quantity + 1})">+</button>
                    </div>
                </div>
            `;
            cartList.appendChild(cartItem);
        });

    totalPrice.textContent = total.toLocaleString(); // UPDATE TOTAL HARGA
    cartCount.textContent = count; // UPDATE JUMLAH KERANJANG
}



        function changeQuantity(id, quantity) {
            let cartItem = cart.find(c => c.produk_id == id);
            if (cartItem) {
                if (quantity <= 0) {
                    // HAPUS BARANG JIKA JUMLAH 0
                    cart = cart.filter(c => c.produk_id != id);
                } else {
                    // UPDATE JUMLAH KERANJANG
                    cartItem.quantity = quantity;
                }
                updateCart();
            }
        }
    </script>
</body>
</html>
