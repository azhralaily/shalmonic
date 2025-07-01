# BACKEND_README.md

## Struktur Backend

Project ini menggunakan Laravel sebagai framework backend. Berikut penjelasan struktur dan folder penting:

### Struktur Utama:
- `app/Http/Controllers/` : Berisi semua controller (logika request/response)
- `app/Models/` : Berisi model Eloquent (representasi tabel database)
- `database/migrations/` : Berisi file migrasi untuk struktur database
- `database/seeders/` : Berisi seeder untuk data awal
- `routes/web.php` : Definisi route web (akses browser)
- `routes/api.php` : Definisi route API (jika ada)
- `config/` : Konfigurasi aplikasi (database, mail, dsb)
- `resources/views/` : Blade template (frontend, tapi sering terkait backend)

## Cara Kerja Backend
- Request masuk ke route (`routes/web.php`)
- Route mengarah ke Controller
- Controller memproses data, validasi, akses Model/database
- Controller mengembalikan response (view atau JSON)

## Perintah Umum
- `php artisan migrate` : Menjalankan migrasi database
- `php artisan db:seed` : Menjalankan seeder
- `php artisan serve` : Menjalankan server lokal
- `php artisan route:list` : Melihat semua route

## Catatan
- Semua logic utama aplikasi ada di Controller dan Model
- Untuk menambah fitur, buat Controller dan Model baru, lalu daftarkan di route
- Konfigurasi environment di file `.env` 