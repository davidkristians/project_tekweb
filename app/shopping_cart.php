<?php
session_start();

$isLoggedIn = isset($_SESSION['user_email']) && isset($_SESSION['user_name']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Koneksi ke database
$host = "localhost";
$port = "5432";
$dbname = "Web-Ecommerce";
$dbUser = "postgres";
$dbPassword = "postgres";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil item keranjang untuk pengguna yang login
    $cartItems = [];
    if ($isLoggedIn) {
        $stmt = $conn->prepare("
            SELECT p.produk_id, p.nama_produk, p.harga, p.gambar_produk, c.quantity 
            FROM shopping_cart c
            JOIN produk p ON c.produk_id = p.produk_id
            WHERE c.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="../Produk/produk.css">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-y7Pr6iRjBrwjlkGf"></script>
    <link rel="stylesheet" href="../public/css/style.css">
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
    <link rel="stylesheet" href="../app/shopping_cart_baru_2.css">
    <!-- <link rel="stylesheet" href="../Produk/produk.css"> -->
</head>
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
            <li><a href="#">Kategori</a></li>
            <li><a href="#">Promo</a></li>
        </ul>
        <div class="nav-icons">
            <a href=""><i data-feather="shopping-cart"></i></a>
            <a href="#" id="open-form-btn"><i data-feather="user"></i></a>
        </div>
      </div>
    </nav>





    <!-- BAGIAN BODY KERANJANG SAYA -->
    <h2 class="judul_keranjang_saya">Keranjang Saya</h2>
    <div class="container">
        <div class="cart-items">
            <div class="cart-item">
                <img src="../public/img/barang/jpg/iphone_13_pro_max.jpg" alt="Basic Tee Sienna">
                <div class="item-details">
                    <h3>iPhone 14</h3>
                    <p>Bagus</p>
                    <p class="status in-stock">Baru</p>
                </div>
                <div class="item-quantity">
                    <label for="qty1">Jumlah</label>
                    <select id="qty1">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <p class="item-price">Rp 14.000.000</p>
            </div>
            <div class="cart-item">
                <img src="../public/img/barang/jpg/iphone_13_pro_max.jpg" alt="Basic Tee Black">
                <div class="item-details">
                    <h3>Iphone 13 Pro Max</h3>
                    <p>Kondisi mulus 100%, charger lengkap. Warna biru.</p>
                    <p class="status delayed">90%</p>
                </div>
                <div class="item-quantity">
                    <label for="qty2">Jumlah</label>
                    <select id="qty2">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <p class="item-price">Rp 10.000.000</p>
            </div>
            <div class="cart-item">
                <img src="../public/img/barang/jpg/samsung_z_flip.jpg" alt="Nomad Tumbler">
                <div class="item-details">
                    <h3>Samsung Z-Flip</h3>
                    <p>Kondisi mulus 100%, tidak ada lecet. Warna biru.</p>
                    <p class="status in-stock">90%</p>
                </div>
                <div class="item-quantity">
                    <label for="qty3">Jumlah</label>
                    <select id="qty3">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <p class="item-price">Rp 13.000.000</p>
            </div>
        </div>

        <div class="order-summary">
            <h3>Total Harga</h3>
            <div class="summary-item">
                <span>Subtotal</span>
                <span>Rp 37.000.000</span>
            </div>
            <div class="summary-item">
                <span>Biaya Pengiriman</span>
                <span>Rp 200.000</span>
            </div>
            <div class="summary-item order-total">
                <span>Order total</span>
                <span>Rp 37.200.000</span>
            </div>
            <button class="checkout-btn">Checkout</button>
        </div>
    </div>





<div class="container">
    <div class="cart-items">
        <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item" data-id="<?= htmlspecialchars($item['produk_id']) ?>">
                    <div class="cart-item-content">
                        <img src="<?= htmlspecialchars($item['gambar_produk']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="cart-item-image">
                        <div class="cart-item-details">
                            <h3 class="cart-item-name"><?= htmlspecialchars($item['nama_produk']) ?> x <span class="item-quantity"><?= htmlspecialchars($item['quantity']) ?></span></h3>
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
  <p class="typewrite" data-period="2000" data-type='[ "adalah Platform Jual Beli Barang Bekas Berkualitas Tinggi.", "adalah tempat menemukan gadget impian dengan harga murah!", "adalah Platform Jual Beli Barang Bekas Berkualitas Tinggi.", "adalah tempat menemukan gadget impian dengan harga murah!" ]'>
    <span class="wrap"></span>
  </p>
</h1>
    </div>
  </div>
  <div class="footer-bottom">
    <p><i class="bi bi-c-circle"></i> Redget 2024 | Hak Cipta Dilindungi</p>
  </div>
</footer>

<script>
// Inisialisasi keranjang dari PHP
let cart = <?php echo json_encode($cartItems, JSON_HEX_TAG); ?>;

// Fungsi untuk memperbarui tampilan keranjang
function updateCart() {
    const cartList = document.querySelector('.cart-list');
    const totalPrice = document.querySelector('.total-price');

    cartList.innerHTML = ''; // Kosongkan daftar keranjang
    let total = 0;

    cart.forEach(item => {
        total += item.harga * item.quantity; // Hitung total harga

        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.setAttribute('data-id', item.produk_id);
        cartItem.innerHTML = `
            <div class="cart-item-content">
                <img src="${item.gambar_produk}" alt ="${item.nama_produk}" class="cart-item-image">
                <div class="cart-item-details">
                    <p class="cart-item-name">${item.nama_produk} x <span class="item-quantity">${item.quantity}</span></p>
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

    totalPrice.textContent = total.toLocaleString(); // Update total harga
}

function changeQuantity(productId, newQuantity) {
    if (newQuantity < 0) return; // Jangan izinkan jumlah kurang dari 0

    // Update jumlah di array keranjang
    const item = cart.find(i => i.produk_id === productId);
    if (item) {
        item.quantity = newQuantity;

        // Kirim permintaan untuk memperbarui database
        const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        fetch('../configuration/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId,
                produk_id: productId,
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Quantity updated in the database');
            } else {
                console.error('Failed to update quantity in the database:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    updateCart(); // Perbarui tampilan keranjang
}

// Panggil fungsi untuk memperbarui tampilan keranjang saat halaman dimuat
updateCart();

// Fungsi untuk membuka modal
function openModal() {
    const modal = document.getElementById("checkoutModal");
    const totalPrice = document.querySelector('.total-price').textContent;

    // Update total harga di modal
    document.querySelector('.total-price-modal').textContent = totalPrice;

    // Ambil elemen daftar barang dalam modal
    const modalCartList = document.getElementById("modalCartList");
    modalCartList.innerHTML = ''; // Kosongkan daftar sebelumnya

    // Tambahkan barang ke daftar modal
    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.classList.add('modal-cart-item');
        itemElement.innerHTML = `
            <div>
                <img src="${item.gambar_produk}" alt="${item.nama_produk}" style="width: 50px; height: 50px; margin-right: 10px;">
                <span>${item.nama_produk} x ${item.quantity}</span>
                <span style="float: right;">${(item.harga * item.quantity).toLocaleString()} IDR</span>
            </div>
        `;
        modalCartList.appendChild(itemElement);
    });

    modal.style.display = "block"; // Tampilkan modal
}


// Fungsi untuk menutup modal
function closeModal() {
 const modal = document.getElementById("checkoutModal");
    modal.style.display = "none";
}

function payWithMidtrans() {
        const totalPriceElement = document.querySelector('.total-price');
        const totalPrice = parseInt(totalPriceElement.textContent.replace(/[^0-9]/g, ''));

        // Buat permintaan ke server untuk mendapatkan token
        fetch('../configuration/midtrans_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ gross_amount: totalPrice })
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                window.snap.pay(data.token, {
                    onSuccess: function(result) {
                        alert('Pembayaran berhasil!');
                        console.log(result);
                        location.reload(); 
                    },
                    onPending: function(result) {
                        alert('Pembayaran tertunda. Silakan selesaikan pembayaran.');
                        console.log(result);
                    },
                    onError: function(result) {
                        alert('Terjadi kesalahan dalam pembayaran.');
                        console.error(result);
                    },
                    onClose: function() {
                        alert('Anda menutup popup pembayaran tanpa menyelesaikannya.');
                    }
                });
            } else {
                alert('Gagal mendapatkan token pembayaran. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran.');
        });
    }

document.querySelector('.checkout-btn').addEventListener('click', openModal);
</script>



</body>
</html>