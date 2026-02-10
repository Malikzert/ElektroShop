
<p align="center">
  <img src="https://img.shields.io/badge/PHP_Native-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" />
</p>

<p align="center">
<img src="https://img.shields.io/github/languages/top/Malikzert/ElektroShop?color=blue" alt="Top Language">
<img src="https://img.shields.io/github/last-commit/Malikzert/ElektroShop" alt="Last Commit">
</p>

## ⚡ Tentang ElektroShop

**ElektroShop** adalah platform e-commerce sederhana namun fungsional yang dikembangkan menggunakan **PHP Native**. Aplikasi ini dirancang untuk mengelola penjualan produk elektronik, mulai dari katalog produk, sistem keranjang belanja, hingga manajemen transaksi.

Proyek ini menunjukkan implementasi dasar dari sistem CRUD (Create, Read, Update, Delete), manajemen session untuk login, dan integrasi database relasional.

---

## 🛠 Tech Stack & Tools

### Backend & Database
<p align="left">
  <img src="https://img.shields.io/badge/PHP_Native-777BB4?style=for-the-badge&logo=php&logoColor=white" title="PHP" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" title="MySQL" />
</p>

### Frontend
<p align="left">
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" />
  <img src="https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" />
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" />
</p>

### Development Tools
<p align="left">
  <img src="https://img.shields.io/badge/Visual_Studio_Code-007ACC?style=for-the-badge&logo=visual-studio-code&logoColor=white" />
  <img src="https://img.shields.io/badge/XAMPP-FB8C00?style=for-the-badge&logo=xampp&logoColor=white" />
  <img src="https://img.shields.io/badge/Git-F05032?style=for-the-badge&logo=git&logoColor=white" />
  <img src="https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white" />
</p>

---

## ✨ Fitur Utama

- **Katalog Produk**: Menampilkan daftar barang elektronik dengan kategori.
- **Sistem Keranjang**: Pengguna dapat menambah, menghapus, dan memperbarui jumlah pesanan.
- **Manajemen User**: Fitur Registrasi dan Login (Authentication).
- **Checkout & Transaksi**: Simulasi pemesanan barang hingga masuk ke database.
- **Admin Panel**: Mengelola data stok barang dan melihat riwayat transaksi.

---

## 🚀 Cara Instalasi Lokal

Karena proyek ini menggunakan PHP Native, pastikan Anda sudah menginstal **XAMPP** atau server lokal sejenis.

1. **Clone repository**:
   ```bash
   git clone [https://github.com/Malikzert/ElektroShop.git](https://github.com/Malikzert/ElektroShop.git)

```

2. **Pindahkan folder**:
Pindahkan folder `ElektroShop` ke direktori server lokal Anda (contoh: `C:/xampp/htdocs/`).
3. **Konfigurasi Database**:
* Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
* Buat database baru dengan nama `db_elektroshop` (atau sesuaikan dengan file SQL yang tersedia).
* Import file `.sql` yang ada di dalam folder proyek ke database tersebut.


4. **Sesuaikan Koneksi**:
Buka file koneksi database (biasanya `koneksi.php` atau `config.php`) dan sesuaikan:
```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_elektroshop";

```


5. **Jalankan**:
Akses di browser melalui: `http://localhost/ElektroShop`

---

## 📄 Lisensi

Proyek ini bersifat open-source dan berada di bawah lisensi [MIT](https://opensource.org/licenses/MIT).

---

<p align="center">Dikembangkan dengan ❤️ oleh <a href="https://www.google.com/search?q=https://github.com/Malikzert">Malikzert</a></p>

```
**Apakah ada file SQL khusus di dalam repo tersebut?** Jika namanya bukan `db_elektroshop`, silakan ubah bagian "Konfigurasi Database" di atas sesuai nama file `.sql` kamu ya\!

```
