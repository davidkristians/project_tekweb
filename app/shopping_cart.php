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
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $dbUser , $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil item keranjang untuk pengguna yang login
    $cartItems = [];
    if ($isLoggedIn) {
        $stmt = $conn->prepare("SELECT p.produk_id, p.nama_produk, p.harga, p.gambar_produk, c.quantity 
                                 FROM shopping_cart c
                                 JOIN produk p ON c.produk_id = p.produk_id
                                 WHERE c.user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
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
</head>
<body>

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
</script>

</body>
</html>