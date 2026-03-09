# E-Commerce API Service (Laravel 12)

API E-commerce yang dirancang dengan fokus pada **keamanan transaksi**, **integritas data**, dan **skalabilitas sistem**. Proyek ini menerapkan arsitektur _Service Pattern_ dan pengelolaan database modern menggunakan ULID.

---

## Dokumentasi API

Dokumentasi endpoint lengkap, termasuk skenario _Success_ dan _Error Handling_, dapat diakses melalui link di bawah ini:
**[Live Postman Documentation](https://documenter.getpostman.com/view/52993295/2sBXcLgdVa)**

---

## ✨ Fitur Unggulan

### 1. Arsitektur & Keamanan

- **Service Pattern**: Logika bisnis (seperti `ProductService`) dipisahkan dari Controller untuk menjaga kode tetap _clean_ dan mudah dirawat.
- **Database Transactions**: Menjamin atomisitas data pada proses krusial seperti Checkout dan CRUD Produk untuk mencegah data korup.
- **Modern Identifiers (ULID)**: Menggunakan ULID sebagai primary key untuk keamanan ID yang tidak dapat ditebak dan performa pengurutan yang lebih baik.
- **Sanctum Authentication**: Sistem otentikasi berbasis token yang aman untuk Customer dan Admin.

### 2. Logika E-Commerce & Stok

- **Smart Stock Management**: Validasi stok dilakukan per ukuran (`product_sizes`). Sistem otomatis memotong stok saat pesanan dibuat dan mengembalikan stok jika pesanan dibatalkan.
- **Automated File Management**: Menggunakan Trait `HasFile` untuk manajemen upload gambar produk dengan fitur pembersihan otomatis file lama atau file sampah jika transaksi gagal.
- **Eager Loading**: Implementasi `with(['category', 'sizes'])` untuk mencegah masalah _N+1 Query_ dan mengoptimalkan performa API.

### 3. Kualitas Kode & Testing

- **Automated Testing**: Didukung oleh **13+ Feature Tests** menggunakan **Pest PHP** untuk memastikan setiap fitur berjalan sesuai ekspektasi sebelum dideploy.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12
- **Testing**: Pest PHP
- **Database**: MySQL
- **Payment**: Midtrans Ready (Snap Token)

---

## 🚀 Instalasi & Persiapan

1. **Clone & Install**:
   git clone [https://github.com/Luqman89/api-e-commers-learn]

    ```bash
    cd api-e-commers-learn
    composer install
    ```

2. **Environment Setup**:
   Salin file .env.example, sesuaikan konfigurasi database Anda, lalu generate key:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3. **Migrasi & Seeder**:
   Jalankan migrasi untuk membuat tabel dengan struktur ULID dan data awal:

    ```bash
    php artisan migrate --seed
    ```

4. **Jalankan Server**:

    ```bash
    php artisan serve
    ```
