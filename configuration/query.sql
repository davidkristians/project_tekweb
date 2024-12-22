CREATE TABLE users (
	user_id SERIAL PRIMARY KEY,
	nama VARCHAR(100) NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	password VARCHAR(255) NOT NULL,
	nomor_telepon VARCHAR(15),
	alamat TEXT,
	role VARCHAR(10) DEFAULT 'Pembeli' CHECK (role IN ('Penjual', 'Pembeli', 'admin')) NOT NULL
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
	quantity INT DEFAULT 1,
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    produk_id INT REFERENCES produk(produk_id) ON DELETE CASCADE
);

CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY, -- ID unik lokal untuk setiap transaksi
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE, -- Referensi ke pengguna
    tanggal_order TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Waktu order
    status_order VARCHAR(20) DEFAULT 'Pending' CHECK (status_order IN ('Pending', 'Success', 'Failed')), -- Status transaksi
    total_harga NUMERIC(15, 2) NOT NULL -- Total harga transaksi
);


CREATE TABLE order_items (
    order_item_id SERIAL PRIMARY KEY, -- ID unik untuk setiap item dalam order
    order_id INT REFERENCES orders(order_id) ON DELETE CASCADE, -- Referensi ke tabel orders
    produk_id INT REFERENCES produk(produk_id) ON DELETE CASCADE, -- Produk yang dibeli
    quantity INT NOT NULL CHECK (quantity > 0), -- Jumlah produk
    harga_per_item NUMERIC(15, 2) NOT NULL, -- Harga per unit produk
    total_harga_item NUMERIC(15, 2) NOT NULL, -- Total harga untuk produk tersebut (quantity * harga_per_item)
    FOREIGN KEY (order_id) REFERENCES orders(order_id) -- Foreign key untuk memastikan integritas data
);






INSERT INTO produk (nama_produk, merk_produk, kategori, deskripsi, kondisi_barang, harga, jumlah_stock, gambar_produk, penjual_id)
VALUES
    ('Iphone 13 Pro Max', 'Apple', 'HP', 'Kondisi mulus 100%, charger lengkap. Warna biru.', '90%', 10000000.00, 5, 'https://i.pinimg.com/736x/26/be/56/26be56634ad9773c9d8f6315cac2cba7.jpg', 1),
    ('Samsung Z-Flip', 'Samsung', 'HP', 'Kondisi mulus 100%, tidak ada lecet. Warna biru.', '90%', 13000000.00, 3, 'https://i.pinimg.com/736x/10/af/7d/10af7dacc86d19a1ec8e50e8f8038ac6.jpg', 1),
    ('Laptop ROG Zephyrus Duo', 'Asus', 'Laptop', 'Kondisi mulus, masih bisa untuk gaming, charger lengkap, ada screen protector. Warna hitam.', '80%', 15000000.00, 2, 'https://i.pinimg.com/736x/36/7b/21/367b211afca91a2026b33b827364cc43.jpg', 1)


