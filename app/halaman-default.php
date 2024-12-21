<?php
    $host = "localhost";  
    $port = "5432";          
    $dbname = "Web-Ecommerce"; 
    $dbUser = "postgres";    
    $dbPassword = "456287";  

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
    <link rel="stylesheet" href="../app/halaman-default-baru-2.css">
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
            <a href=""><i data-feather="shopping-cart"></i></a>
            <a href="#" id="open-form-btn"><i data-feather="user"></i></a>
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



<!-- FORM SECTION (REGISTER/LOGIN) -->
<div id="overlay" class="overlay"></div>
    <div id="form-container" class="form-container">
      <h2 id="form-title">Buat Akun</h2>
      <div class="tabs">
        <div id="register-tab" class="active" onclick="toggleForm('register')">Daftar</div>
        <div id="login-tab" onclick="toggleForm('login')">Login</div>
      </div>

      <!-- REGISTER FORM -->
        <form id="register-form" class="form-content" action="#" method="POST">
          <div class="form-group">
            <input type="text" placeholder="Nama Lengkap" name="nama" required>
          </div>
          <div class="form-group">
            <input type="email" placeholder="Alamat Email" name="email" required>
          </div>
          <div class="form-group">
            <input type="password" placeholder="Password" name="password" required>
          </div>
          <div class="form-group">
            <input type="password" placeholder="Confirm Password" name="confirm_password" required>
          </div>
          <div class="form-group">
            <input type="text" placeholder="Nomor Telepon" name="nomor_telepon">
          </div>
          <div class="form-group">
            <input type="text" placeholder="Alamat" name="alamat">
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

    <!-- LOGIN FORM -->
      <form id="login-form" class="form-content" action="login.php" method="POST" style="display: none;">
          <div class="form-group">
              <input type="email" name="email" placeholder="Email Address" required>
          </div>
          <div class="form-group">
              <input type="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit">Login</button>
      </form> 
    </div>



    <div class="spacer"></div>
    <!-- JavaScript for toggle between register and login -->
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