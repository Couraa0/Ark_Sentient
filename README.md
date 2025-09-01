# ARK Sentient

Marketplace Ternak Terpercaya - Menghubungkan peternak dengan pembeli secara aman dan efisien.

---

## Daftar Isi

- [Fitur](#fitur)
- [Requirement](#requirement)
- [Instalasi & Setup](#instalasi--setup)
- [Struktur Folder](#struktur-folder)
- [Demo Login](#demo-login)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Tim Pengembang](#tim-pengembang)
- [Kontribusi](#kontribusi)


---

## Fitur Utama

- **Marketplace Terintegrasi Midtrans**  
  Platform jual beli ternak yang aman dan transparan, dilengkapi dengan sistem pembayaran otomatis menggunakan Midtrans. Pengguna dapat mencari, membeli, dan menjual hewan ternak dengan proses transaksi yang mudah dan terjamin.

- **Pemeriksaan Ternak Berbasis AI**  
  Pengguna dapat mengunggah foto hewan ternak untuk dilakukan analisis kesehatan secara otomatis menggunakan teknologi kecerdasan buatan. Hasil analisis akan memberikan insight kondisi fisik dan kesehatan ternak secara instan.

- **Smart Assistant Ternak (AI Assistant)**  
  Asisten cerdas yang siap membantu menjawab berbagai pertanyaan seputar peternakan, mulai dari perawatan, kesehatan, hingga manajemen ternak. Selain itu, AI Assistant dapat membuatkan jadwal pakan otomatis yang optimal sesuai kebutuhan ternak.

- **Keranjang Belanja (Cart)**  
  Fitur keranjang belanja memudahkan pembeli untuk mengelola dan meninjau produk ternak yang akan dibeli sebelum melakukan pembayaran. Pengguna dapat menambah, mengurangi, atau menghapus produk dari keranjang dengan mudah.

---

## Requirement

- PHP >= 7.4
- Composer
- MySQL/MariaDB
- Web server (XAMPP/Laragon/Apache)
- Ekstensi PHP: PDO, cURL, OpenSSL, JSON

---

## Instalasi & Setup

1. **Clone repository**
   ```bash
   git clone https://github.com/Couraa0/Ark_Sentient.git
   cd Ark_Sentient/Prototype
   ```

2. **Install dependency PHP**
   ```bash
   composer install
   ```

3. **Setup File .env**
   - Ubah file `.env.example` menjadi `.env`, atau buat file `.env` baru di root folder proyek.
   - Isi variabel berikut di `.env`:
     ```
     GEMINI_API_KEY="Your API Key Here"
     MIDTRANS_SERVER_KEY=Your Server Key Here
     MIDTRANS_CLIENT_KEY=Your Client Key Here
     MIDTRANS_IS_PRODUCTION=your Environment Here (true/false)
     ```
   - Pastikan file `.env` **tidak di-commit** ke repository publik.
   - Aplikasi akan membaca variabel ini secara otomatis jika menggunakan library `vlucas/phpdotenv`.

4. **Setup Database**
   - Buat database baru, misal: `ark_sentient`
   - Import file SQL ke database tersebut.
   - Edit file `Config/config.php` dan sesuaikan:
     ```php
     // Contoh:
     $host = 'localhost';
     $db   = 'ark_sentient';
     $user = 'root';
     $pass = '';
     ```

5. **Jalankan di Web Server**
   - Pastikan folder ini berada di dalam `htdocs` (XAMPP) atau public folder web server Anda.
   - Akses melalui browser:  
     `http://localhost/Ark_Sentient/Prototype/index.php`

---

## Struktur Folder

```
Prototype/
│
├── Asset/              # CSS, gambar, icon, JS
├── Config/             # Konfigurasi database & environment
├── Views/              # Halaman aplikasi (home, dashboard, produk, dsb)
│   └── Auth/           # Halaman login & register
├── vendor/             # Dependency Composer
├── index.php           # Halaman login utama
├── README.md           # Dokumentasi ini
└── composer.json       # Dependency PHP
```

---

## Demo Login

| Username   | Password  | Role      |
|------------|-----------|-----------|
| admin      | password  | Admin     |

---

## Teknologi yang Digunakan

- **PHP** (Native, OOP)
- **Bootstrap 5** (UI)
- **FontAwesome** (Icon)
- **Midtrans** (Payment Gateway)
- **Composer** (Dependency Management)
- **PDO** (Database)
- **Guzzle** (HTTP Client, opsional)

---

## Tim Pengembang

| Nama                        | GitHub                                      | Email                           |
|-----------------------------|----------------------------------------------|----------------------------------|
| Muhammad Rakha Syamputra    | [couraa0](https://github.com/couraa0)        | muhammadrakhasyamputra@gmail.com |
| Muhammad Rafly Dwi Gunawan  | [MuhammadRafly23100](https://github.com/MuhammadRafly23100) | mraflyyydwiii@gmail.com          |
| Arya Chakra Ramadhan        | [ayraa34](https://github.com/ayraa34)        | aryachakra@gmail.com            |

---

## Kontribusi

Kontribusi sangat terbuka!  
Silakan fork repo ini, buat branch baru, dan ajukan pull request.
