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
    <link rel="stylesheet" href="../app/halaman-pembayaran.css">
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



    

    <!-- TRANSACTION LIST MODIFIED -->
    <div class="container">
        <div class="header">
            <h1>History Transaksi</h1>
        </div>

        <div class="filter-bar">
            <select>
                <option>Order ID</option>
            </select>
            <input type="text" placeholder="Cari Order ID...">
            <input type="date">
            <input type="date">
            <button>Terapkan</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Order ID</th>
                        <th>Tipe Transaksi</th>
                        <th>Metode Pembayaran</th>
                        <th>Status</th>
                        <th>Total Harga</th>
                        <th>Email Anda</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>22 Des 2024, 01:10</td>
                        <td>ORDER-6767046d56b1</td>
                        <td>Pembayaran</td>
                        <td>Kartu Kredit</td>
                        <td class="status-success">Berhasil</td>
                        <td>Rp10,015,000</td>
                        <td>pelanggan@example.com</td>
                    </tr>
                    <tr>
                        <td>22 Des 2024, 01:08</td>
                        <td>ORDER-67670394ddc4</td>
                        <td>Pembayaran</td>
                        <td>Kartu Kredit</td>
                        <td class="status-pending">Pending</td>
                        <td>Rp10,015,000</td>
                        <td>pelanggan@example.com</td>
                    </tr>
                    <tr>
                        <td>20 Des 2024, 15:26</td>
                        <td>ORDER-676529f511cf</td>
                        <td>Pembayaran</td>
                        <td>Kartu Kredit</td>
                        <td class="status-success">Berhasil</td>
                        <td>Rp23,000,000</td>
                        <td>pelanggan@example.com</td>
                    </tr>
                    <tr>
                        <td>20 Des 2024, 08:55</td>
                        <td>ORDER-6764ce9e7295</td>
                        <td>Pembayaran</td>
                        <td>Bank Transfer</td>
                        <td class="status-cancelled">Dibatalkan</td>
                        <td>Rp310,060,000</td>
                        <td>pelanggan@example.com</td>
                    </tr>
                    <tr>
                        <td>20 Des 2024, 07:26</td>
                        <td>ORDER-6764aba75513</td>
                        <td>Pembayaran</td>
                        <td>QRIS</td>
                        <td class="status-expired">Tidak Berlaku</td>
                        <td>Rp15,000</td>
                        <td>pelanggan@example.com</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>






    <!-- PRODUK FIXED MODIFIED -->
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
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <?php else: ?>
        <p>Produk belum tersedia.</p>
    <?php endif; ?>


    


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
                    fetch('../configuration/add_to_cart.php', {
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