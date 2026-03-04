# E-Commerce API Service (Laravel 11)

API E-commerce yang fokus pada keamanan transaksi dan akurasi stok.

### Fitur Utama:

- **Checkout System**: Menggunakan DB Transaction untuk keamanan data.
- **Stock Management**: Otomatis potong stok saat beli & restore stok saat cancel.
- **Payment**: Integrasi Midtrans (Snap Token).
- **Testing**: 13+ Feature Tests menggunakan **Pest PHP**.

### Cara Menjalankan Test:

Untuk memastikan semua fungsi berjalan normal, jalankan:
`php artisan test`
