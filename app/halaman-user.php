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
    $dbPassword = "456287";

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
    <link rel="stylesheet" href="../app/halaman-default.css">
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
            <li><a href="#">Kategori</a></li>
            <li><a href="#">Promo</a></li>
        </ul>
        <div class="nav-icons">
            <a href=""><i data-feather="shopping-cart"></i></a>
            <a href="../app/halaman-profile.php" id="open-form-btn"><i data-feather="user"></i></a>
            <a href="../app/logout.php">
                <i class="fas fa-sign-out-alt" title="Logout"></i>
            </a>
        </div>
      </div>
    </nav>

    <!--=====HERO MODIFIED FIXED=====-->
    <section class="carousel">
      <div class="carousel-track" id="carouselTrack">
        <div class="carousel-slide">
          <img src="https://via.placeholder.com/1200x400?text=Slide+1" alt="Slide 1" />
        </div>
        <div class="carousel-slide">
          <img src="https://via.placeholder.com/1200x400?text=Slide+2" alt="Slide 2" />
        </div>
        <div class="carousel-slide">
          <img src="https://via.placeholder.com/1200x400?text=Slide+3" alt="Slide 3" />
        </div>
      </div>
      <div class="carousel-nav">
        <button class="carousel-btn" id="prevBtn">&#10094;</button>
        <button class="carousel-btn" id="nextBtn">&#10095;</button>
      </div>
    </section>

    <!-- Produk Section -->
    <?php if (!empty($produkData)): ?>
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


    <!-- Pop-up -->
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

</body>
</html>
