<?php
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
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Section with Navbar</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../Main/dashboard.css">
  </head>


  <body>
    <!-- Navbar -->
    <nav class="navbar">
    <div class="navbar-brand">ReTech</div>
        <div class="navbar-links">
        <a href="../main/halaman-default.php">Home</a>
        <a href="#">Kategori</a>
        <a href="../Produk/produk-Default.php">Produk</a>
        <a href="#" id="open-form-btn">Masuk/Daftar</a>
        </div>
    </nav>

    <section class="hero">
      <div class="hero-content">
        <h1>Welcome to Our Website</h1>
        <p>Your one-stop solution for gadgets and technology</p>
      </div>
    </section>

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

    <section class="about" id="about">
      <h1 class="heading"> <span>About</span> us </h1>
      <div class="content">
        <h3>Mengapa memilih ReTech?</h3>
        <p>Kami hadir untuk memenuhi kebutuhan belanja alat elektronik Anda dengan menyediakan berbagai macam produk berkualitas tinggi. Mulai dari HP dengan fitur canggih, 
          laptop yang tangguh untuk produktivitas kerja dan belajar, 
          tablet yang praktis untuk hiburan dan mobilitas, hingga beragam perangkat elektronik lainnya yang dirancang 
          untuk memudahkan kehidupan modern Anda. Kami memahami bahwa teknologi bukan hanya sekadar alat, 
          tetapi juga menjadi bagian penting dalam mendukung aktivitas sehari-hari, mulai dari belajar, bekerja, hingga
          menikmati hiburan. Dengan koleksi produk yang selalu diperbarui, kami berusaha memastikan bahwa Anda selalu memiliki akses 
          ke teknologi terkini sesuai kebutuhan dan keinginan Anda.</p>
        <p>Kami juga menyadari bahwa setiap individu, terutama mahasiswa, sering kali menghadapi tantangan dalam mengelola anggaran mereka. Oleh karena itu, 
          kami memberikan penawaran harga yang sangat terjangkau dan bersahabat, tanpa mengurangi kualitas dari produk yang ditawarkan. Harga yang kami tawarkan 
          dirancang dengan mempertimbangkan kebutuhan pelanggan, sehingga siapa pun, termasuk mahasiswa dengan anggaran terbatas, 
          tetap bisa mendapatkan perangkat elektronik berkualitas untuk menunjang aktivitas mereka.</p>
        <p>Selain produk dan harga yang menarik, kami juga berkomitmen untuk memberikan pengalaman belanja yang nyaman dan memuaskan. Dengan layanan pelanggan 
            yang ramah dan profesional, kami siap membantu Anda menemukan produk yang paling sesuai dengan kebutuhan Anda. Tidak hanya itu, kami juga menyediakan 
            berbagai promo dan diskon menarik agar Anda bisa mendapatkan produk impian Anda dengan harga yang lebih hemat.</p>
        <p>Dengan berbelanja di tempat kami, Anda tidak hanya mendapatkan produk berkualitas, tetapi juga solusi yang dapat membantu Anda menjalani kehidupan yang lebih 
          praktis, efisien, dan menyenangkan. Kami percaya bahwa dengan dukungan perangkat elektronik yang tepat, Anda dapat mencapai lebih banyak hal, baik dalam hal 
          pendidikan, karier, maupun kegiatan sehari-hari. Temukan segala kebutuhan elektronik Anda bersama kami dan jadilah bagian dari pelanggan yang puas dengan pelayanan 
          terbaik yang kami tawarkan.</p>
      </div>
    </section>

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
  </body>
</html>