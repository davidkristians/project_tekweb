CREATE TABLE users (
	user_id SERIAL PRIMARY KEY,
	nama VARCHAR(100) NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	password VARCHAR(255) NOT NULL,
	nomor_telepon VARCHAR(15),
	alamat TEXT,
	role VARCHAR(10) DEFAULT 'Pembeli' CHECK (role IN ('Penjual', 'Pembeli')) NOT NULL
);

CREATE TABLE produk (
	produk_id SERIAL PRIMARY KEY,
	nama_produk VARCHAR(100) NOT NULL,
	merk_produk VARCHAR(50),
	kategori VARCHAR(20) CHECK (kategori IN ('HP', 'Laptop', 'Tablet', 'TV')) NOT NULL,
	deskripsi TEXT,
    kondisi_barang VARCHAR(10) CHECK (kondisi_barang IN ('70%', '80%', '90%', 'Baru')) NOT NULL,
    harga NUMERIC(15, 2) NOT NULL,
    jumlah_stock INT NOT NULL,
    gambar_produk TEXT,
    penjual_id INT REFERENCES users(user_id) ON DELETE CASCADE -- Jika user dengan id sekian dihapus maka data-data terkait dengan user tersebut akan ikut dihapus
);

CREATE TABLE shopping_cart (
    shopping_cart_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    produk_id INT REFERENCES produk(produk_id) ON DELETE CASCADE
);

CREATE TABLE transactions (
    transaction_id SERIAL PRIMARY KEY,
    order_id SERIAL NOT NULL,
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    total_harga NUMERIC(15, 2) NOT NULL,
    alamat_pengiriman TEXT NOT NULL,
    metode_pembayaran VARCHAR(10) CHECK (metode_pembayaran IN ('Cash', 'Cashless')) NOT NULL,
    status_pembayaran VARCHAR(10) CHECK (status_pembayaran IN ('Pending', 'Sukses', 'Gagal')) NOT NULL,
    status_pengiriman VARCHAR(15) CHECK (status_pengiriman IN ('Dikirim', 'Selesai', 'Dikembalikan')) NOT NULL
);