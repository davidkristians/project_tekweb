<?php
session_start();

// Cek apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $email = $_POST['email'];
    $password = $_POST['password'];

    $servername = "localhost";
    $username = "postgres";
    $db_password = "postgres";
    $dbname = "Web-Ecommerce";
    $port = "5432";

    try {
        // Membuat koneksi ke database
        $conn = new PDO("pgsql:host=$servername;port=$port;dbname=$dbname", $username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk mencari user berdasarkan email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Ambil data user
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Cek apakah user ditemukan dan password cocok
        if ($user && password_verify($password, $user["password"])) {
            // Simpan data user ke session
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_name"] = $user["nama"];
            $_SESSION["user_id"] = $user["user_id"];

            header("Location: dasboard-User.php");
            exit(); 
        } else {
            echo "Email atau password salah.";
        }

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Tutup koneksi
    $conn = null;
}
?>
