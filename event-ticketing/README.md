# 🎫 Flowtix - Event Ticketing Management Platform

Flowtix adalah platform manajemen event dan penjualan tiket berbasis web yang dirancang untuk memberikan pengalaman pembelian yang aman, cepat, dan handal. Project ini adalah solusi *end-to-end* yang menangani segalanya, mulai dari pembuatan event oleh Admin hingga verifikasi tiket di lokasi oleh Organizer.

---

## Fitur Utama

### 1. **Sistem Antrean & Waiting Room**
- Mencegah *race condition* dan *over-selling* dengan sistem antrean yang cerdas.
- Pengguna diberikan slot waktu khusus untuk menyelesaikan pembayaran.

### 2. **Manajemen Tiket & Stok**
- Dukungan berbagai jenis tiket (VIP, Regular, dll) per event.
- Manajemen stok *real-time* dengan *pessimistic locking* pada database.

### 3. **E-Ticket dengan QR Code**
- Tiket otomatis dikirim via email dalam format PDF setelah pembayaran sukses.
- Setiap tiket memiliki QR Code unik untuk validasi masuk.

### 4. **Dashboard & Analitik**
- Statistik penjualan *real-time* untuk Organizer.
- Laporan finansial dan performa event untuk Admin.
- Fitur ekspor laporan ke format **Excel** dan **PDF**.

### 5. **Integrasi Pembayaran**
- Integrasi dengan **Midtrans** (Snap Redirect) untuk transaksi yang aman.
- Mendukung fitur *Mock Payment* untuk kebutuhan pengembangan.

---

## Tech Used

- **Backend:** Laravel 11
- **UI & Frontend:** Tailwind CSS, Alpine.js, Vite
- **Database:** SQLite / MySQL (via XAMPP)
- **Payment Gateway:** Midtrans SDK
- **Reporting:** DomPDF & Laravel Excel

---

## Cara Menjalankan Project

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- XAMPP (untuk environment database)

### Langkah Instalasi

1. **Masuk ke direktori project:**
   ```bash
   cd event-ticketing-project
   ```

2. **Instal dependensi PHP & JS:**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment:**
   - Salin `.env.example` menjadi `.env`.
   - Konfigurasi database dan sertakan `MIDTRANS_SERVER_KEY` Anda.

4. **Migrasi & Seed Database:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Vite (Terminal 1):**
   ```bash
   npm run dev
   ```

6. **Jalankan Server Laravel (Terminal 2):**
   ```bash
   php artisan serve
   ```

7. **Akses via Browser:**
   Buka `http://127.0.0.1:8000`

---

## Akses Akun (Default Seed)

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | GavMS | password |
| **Organizer** | sourrossie | password |
| **User** | yosua | password |

---

## Catatan Penting
- Untuk menguji **Webhook Midtrans** secara lokal, pastikan Anda menjalankan **Ngrok** di port 8000 dan memperbarui `NGROK_URL` di file `.env`. dengan command `ngrok http 8000`
- Semua e-tiket yang dikirim dapat dipantau melalui aplikasi *Mailtrap* atau log lokal.

---
© 2026 Flowtix Project - Project Capstone 1 PWL