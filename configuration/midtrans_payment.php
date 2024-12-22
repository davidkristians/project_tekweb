<?php
require_once dirname(__FILE__) . '/../midtrans-php-master/Midtrans.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-P11mkeGvZu31MyjqavtbOIO2';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Ambil data dari permintaan
$requestPayload = file_get_contents('php://input');
$requestData = json_decode($requestPayload, true);

if (!isset($requestData['gross_amount'])) {
    echo json_encode(['error' => 'Jumlah total tidak ditemukan']);
    exit;
}

// Buat parameter transaksi
$order_id = uniqid('ORDER-'); // ID unik untuk transaksi
$params = [
    'transaction_details' => [
        'order_id' => $order_id, // Gunakan order_id yang dihasilkan
        'gross_amount' => $requestData['gross_amount'],
    ],
    'customer_details' => [
        'first_name' => 'Pelanggan',
        'email' => 'pelanggan@example.com',
        'phone' => '081234567890',
    ],
];

try {
    // Dapatkan Snap token
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    echo json_encode(['token' => $snapToken, 'order_id' => $order_id]); // Kembalikan order_id
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>