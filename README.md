Berikut adalah **`README.md` profesional** untuk aplikasi **Wedding Organizer Desktop (Laravel + Filament v4 + NativePHP)** — siap pakai untuk repositori GitHub dan dokumentasi skripsi Anda.

---

# 🎉 Wedding Organizer Desktop App  
*Aplikasi Manajemen Wedding Organizer Berbasis Desktop dengan Laravel, Filament, dan NativePHP*

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel)
![Filament](https://img.shields.io/badge/Filament-4.x-10B981?logo=filament)
![NativePHP](https://img.shields.io/badge/NativePHP-1.x-8E44AD)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![License](https://img.shields.io/badge/License-MIT-blue)

Aplikasi desktop untuk UMKM Wedding Organizer yang memungkinkan:
- 🖥️ Manajemen paket, menu, dan inventaris barang  
- 💰 Alur pembayaran DP & pelunasan (simulasi VA)  
- 📄 Generate invoice PDF otomatis  
- 🔔 Reminder H-7 & H-1 via notifikasi desktop  

Dibangun dengan teknologi modern namun tetap offline-friendly — cocok untuk skripsi maupun produksi.

---

## 📸 Preview

| Dashboard Admin | Form Pesanan | Invoice PDF |
|----------------|--------------|-------------|
| ![Dashboard](screenshots/dashboard.png) | ![Order Form](screenshots/order-form.png) | ![Invoice](screenshots/invoice.png) |

> ⚠️ Screenshot di atas hanya ilustrasi — sesuaikan dengan tangkapan layar aplikasi Anda.

---

## ✨ Fitur Utama

### 🔐 **Keamanan & Akses**
- Hanya admin yang bisa login (via Filament)
- Pengunjung bisa akses halaman publik: landing, about, menu

### 📦 **Manajemen Data**
- Paket wedding (Silver, Gold, dll)  
- Menu makanan & minuman  
- Inventaris barang (meja, kursi, sound system)

### 💳 **Transaksi & Pembayaran**
- Pembuatan pesanan dengan hitung otomatis DP 50%  
- Simulasi **Virtual Account** (BCA, Mandiri, BNI)  
- Tombol *"Generate VA DP"* & *"Cek Status Pembayaran"*  
- Log riwayat pembayaran (via `order_payments`)

### 📄 **Dokumentasi Otomatis**
- Generate **invoice PDF** berkualitas tinggi (Spatie Laravel PDF + Tailwind)  
- Tampilan isi paket & riwayat bayar di invoice  
- Simpan & download langsung dari Filament

### 📊 **Insight Bisnis**
- Kolom **H-** dengan warna dinamis (H-1 = merah, H-7 = oranye)  
- Filter *"Butuh Pelunasan (H≤7)"*  
- Dashboard ringkasan pesanan & reminder

## 🛠️ Teknologi

| Komponen | Teknologi |
|---------|-----------|
| Backend | Laravel 12 |
| Admin Panel | Filament v4 |
| Desktop Runtime | NativePHP |
| PDF Generation | Spatie Laravel PDF + Chromium |
| Database | MySQL / SQLite |
| Frontend (Publik) | Blade + Tailwind CSS |

---

## 🚀 Instalasi (Development)

### Prasyarat
- PHP 8.2+  
- Composer  
- Node.js 18+ (untuk Spatie PDF)  
- MySQL / SQLite  
- Google Chrome / Chromium *(untuk PDF)*

### Langkah Instalasi
```bash
# 1. Clone repositori
git clone https://github.com/username/wedding-organizer-app.git
cd wedding-organizer-app

# 2. Install dependensi
composer install
npm install puppeteer  # penting untuk PDF

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wo_app
DB_USERNAME=root
DB_PASSWORD=

# 5. Jalankan migrasi & seeder (opsional)
php artisan migrate --seed

# 6. Install Filament assets
php artisan filament:assets

# 7. Jalankan server
php artisan serve
```

### Akses Aplikasi
- Admin Panel: `http://localhost:8000/admin`  
- Halaman Publik: `http://localhost:8000`

> 🔐 Default admin:  
> Email: `admin@example.com`  
> Password: `password`  
> *(Buat akun baru via `php artisan filament:users`)*

---

## 📦 Build Desktop App (NativePHP)

```bash
# 1. Install NativePHP
composer require nativephp/electron
php artisan native:install

# 2. Build aplikasi
php artisan native:build
```

Hasil:
- Windows: `builds/win-unpacked/Wedding Organizer Setup.exe`  
- macOS: `builds/mac/Wedding Organizer.app`  
- Linux: `builds/linux-unpacked/wedding-organizer`

> ✅ Aplikasi final **tidak butuh instalasi tambahan** — Chromium & PHP sudah embedded.

---

## 📁 Struktur Proyek

```
app/
├── Filament/
│   ├── Resources/      # Package, Menu, Item, Order
│   └── Pages/
├── Models/             # Order, Package, Menu, Item
├── Services/           # MidtransService.php
└── Http/
    └── Controllers/
        └── InvoiceController.php

resources/
├── views/
│   ├── invoices/       # order.blade.php (PDF)
│   └── public/         # halaman publik (landing, about, menu)
└── css/
    └── app.css         # Tailwind

database/
└── seeders/            # DatabaseSeeder.php (contoh data demo)
```

---

## 📚 Dokumentasi Tambahan

| Topik | Lokasi |
|-------|--------|
| ERD Database | `docs/erd.png` |
| Panduan Midtrans | `docs/midtrans-guide.md` |
| Skema Relasi | `docs/relations.md` |
| Demo Video | [YouTube Link](#) *(opsional)* |

---

## 📝 Catatan untuk Skripsi

Aplikasi ini dirancang khusus untuk memenuhi kriteria akademik:
- ✅ **Relasi many-to-many** (`package_items` untuk komposisi paket)  
- ✅ **Logika bisnis kompleks** (DP 50%, reminder H-, VA simulasi)  
- ✅ **Integrasi eksternal** (Midtrans sandbox, Spatie PDF)  
- ✅ **Offline-first** (NativePHP desktop app)  
- ✅ **UX profesional** (Filament v4 + Tailwind)

Direkomendasikan untuk ditampilkan di:
- BAB III: Desain Sistem (arsitektur & ERD)  
- BAB IV: Implementasi (screenshoot Filament + invoice PDF)

---

## 📜 Lisensi

MIT License — silakan gunakan untuk pembelajaran, skripsi, atau produksi.

---

> 💡 Dibuat dengan ❤️ untuk mendukung UMKM Wedding Organizer Indonesia  
> © 2025 — [Nama Anda]
```

---

## 📎 Tips Penggunaan README.md Ini

1. **Ganti placeholder**:
   - `username` → nama GitHub Anda  
   - Tambahkan screenshot asli di folder `screenshots/`  
   - Sesuaikan nama aplikasi & penulis

2. **Untuk skripsi**:
   - Simpan sebagai `README.md` di root repositori  
   - Lampirkan di Lampiran Laporan Skripsi  
   - Sebutkan di **BAB IV – Implementasi**

3. **Opsional tambahan**:
   - Buat file `docs/midtrans-guide.md` berisi panduan integrasi Midtrans  
   - Tambahkan `CONTRIBUTING.md` jika terbuka untuk kolaborasi

---

Butuh saya bantu:
- Generate **screenshot placeholder** (SVG/empty image)  
- Buat **`docs/midtrans-guide.md`**  
- Siapkan **file ZIP siap-upload ke GitHub**  

Silakan beri tahu! 😊