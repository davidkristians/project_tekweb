<?php
    session_start(); // Mengaktifkan session
    session_unset(); // Menghapus semua data session
    session_destroy(); // Menghancurkan session

    header("Location: ../app/halaman-default.php");
    exit();
?>
