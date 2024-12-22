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
            SELECT p.produk_id, p.nama_produk, p.harga, p.gambar_produk, c.quantity, p.jumlah_stock 
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


    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-y7Pr6iRjBrwjlkGf"></script>



    <style>
        /* Modal */
        .modal {
            display: none; /* Sembunyikan modal secara default */
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Latar belakang hitam transparan */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Lebar modal */
            max-width: 500px; /* Lebar maksimum modal */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .modal-cart-item img {
            max-width: 50px;
            max-height: 50px;
        }
        
        .cart-sidebars {
            display: none;
        }

    </style>
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
            <li><a href="../app/halaman-user.php">Home</a></li>
            <li><a href="#">Kategori</a></li>
            <li><a href="#">Promo</a></li>
        </ul>
        <div class="nav-icons">
            <a href=""><i data-feather="shopping-cart"></i></a>
            <a href="#" id="open-form-btn"><i data-feather="user"></i></a>
        </div>
      </div>
    </nav>

<div class="container">
    <div class="cart-items">
        <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?= htmlspecialchars($item['gambar_produk']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                    <div class="item-details">
                        <h3><?= htmlspecialchars($item['nama_produk']) ?></h3>

                    </div>
                    <div class="item-quantity">
                        <label for="qty<?= $item['produk_id'] ?>">Jumlah:</label>
                        <select id="qty<?= $item['produk_id'] ?>" onchange="changeQuantity(<?= $item['produk_id'] ?>, this.value, <?= $item['jumlah_stock'] ?>)">
                            <?php 
                            // Ambil jumlah maksimum yang bisa dipilih
                            $maxQuantity = min(10, $item['jumlah_stock']); // Pastikan tidak lebih dari 10 atau stok yang tersedia
                            for ($i = 1; $i <= $maxQuantity; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == $item['quantity'] ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <p class="item-price">Rp <?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?></p>
                    <div class="remove-button-container">
                        <button onclick="removeFromCart(<?= $item['produk_id'] ?>)">
                            <i class="fas fa-trash"></i> <!-- Ikon sampah -->
                        </button> <!-- Tombol hapus -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Keranjang belanja kosong.</p>
        <?php endif; ?>
    </div>

    <div class="order-summary">
        <h3>Order Summary</h3>
        <?php
        $subtotal = array_sum(array_map(fn($item) => $item['harga'] * $item['quantity'], $cartItems));
        $shipping = 0;
        $total = $subtotal + $shipping;
        ?>
        <div class="summary-item">
            <span>Subtotal</span>
            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
        </div>
        <div class="summary-item">
            <span>Biaya Pengiriman</span>
            <span>Rp <?= number_format($shipping, 0, ',', '.') ?></span>
        </div>
        <div class="summary-item order-total">
            <span>Total Harga</span>
            <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
        </div>
        <button class="checkout-btn">Checkout</button>
    </div>
</div>

    <div class="cart-sidebars">
            <h2>Keranjang Belanja</h2>
            <div class="cart-list">
                <?php if (!empty($cartItems)): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item" data-id="<?= htmlspecialchars($item['produk_id']) ?>">
                            <div class="cart-item-content">
                                <img src="<?= htmlspecialchars($item['gambar_produk']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="cart-item-image">
                                <div class="cart-item-details">
                                    <p class="cart-item-name"><?= htmlspecialchars($item['nama_produk']) ?> x <span class="item-quantity"><?= htmlspecialchars($item['quantity']) ?></span></p>
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


    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Konfirmasi Pesanan</h2>
            <div id="modalCartList"></div> <!-- Kontainer daftar barang -->
            <p>Total: <span class="total-price-modal"></span> IDR</p>
            <button onclick="payWithMidtrans()">Confirm</button>
            <button onclick="closeModal()">Batal</button>
        </div>
    </div>


<script>
    document.querySelector('.checkout-btn').addEventListener('click', openModal);
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

    function changeQuantity(productId, newQuantity, stock) {
        if (newQuantity < 0 || newQuantity > stock) {
            alert(`Jumlah tidak boleh lebih dari ${stock} atau kurang dari 0.`);
            return; // Jangan izinkan jumlah yang tidak valid
        }

        // Update jumlah di array keranjang
        const item = cart.find(i => i.produk_id === productId);
        if (item) {
            item.quantity = newQuantity;

            // Kirim permintaan untuk memperbarui database
            const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
            fetch('../app/update_cart.php', {
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
                <span>${item.nama_produk} x ${item.quantity} unit</span>
                <span style="float: right;"> : ${(item.harga * item.quantity).toLocaleString()} IDR</span>
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



    function removeFromCart(productId) {
        const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

        fetch('../app/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId,
                produk_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Produk berhasil dihapus dari keranjang');
                // Hapus item dari array keranjang
                cart = cart.filter(item => item.produk_id !== productId);
                updateCart(); // Perbarui tampilan keranjang
            } else {
                console.error('Gagal menghapus produk dari keranjang:', data.message);
    }
        })
        .catch(error => console.error('Error:', error));
    }

// document.querySelector('.checkout-btn').addEventListener('click', openModal);
</script>

<script>
    function clearCart() {
        const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

        fetch('../app/clear_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Keranjang berhasil dikosongkan');
                cart = []; // Kosongkan array keranjang di sisi klien
                updateCart(); // Perbarui tampilan keranjang
            } else {
                console.error('Gagal mengosongkan keranjang:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
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

                    // Ambil data item keranjang dari server untuk menyimpan di order dan order_items
                    fetch('../app/save_order.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            total_price: totalPrice,
                            user_id: <?php echo $_SESSION['user_id']; ?> // Pastikan user_id dari session
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Pesanan berhasil disimpan dengan ID: ${data.order_id}`);
                            clearCart(); // Hapus semua item dari keranjang setelah pembayaran berhasil
                            location.reload();
                        } else {
                            console.error('Gagal menyimpan pesanan:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan pesanan.');
                    });
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

</script>



</body>
</html>