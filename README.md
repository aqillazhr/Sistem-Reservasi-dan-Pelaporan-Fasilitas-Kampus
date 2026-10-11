# Sistem Reservasi & Pelaporan Fasilitas Kampus

Aplikasi web untuk mengelola penggunaan fasilitas kampus (ruang kelas, aula, laboratorium, alat, dan lapangan). Pengguna dapat mengecek ketersediaan dan mengajukan reservasi, serta melaporkan kerusakan fasilitas. Petugas dan admin memproses reservasi dan laporan secara terpusat.

**Repository:** [github.com/aqillazhr/Sistem-Reservasi-dan-Pelaporan-Fasilitas-Kampus](https://github.com/aqillazhr/Sistem-Reservasi-dan-Pelaporan-Fasilitas-Kampus)

---

## Prasyarat

Pastikan sudah terinstal di komputer:

- **PHP >= 8.3** dengan ekstensi `mbstring`, `pdo_mysql`, `gd` (untuk ekspor PDF dan upload foto)
- **Composer** (https://getcomposer.org)
- **MySQL >= 8.0.16** (atau MariaDB 10.4+) — versi ini ke atas karena skema memakai CHECK constraint
- **Git**
- **Node.js & npm** (opsional, hanya kalau ingin build asset Vite/Tailwind)

### Mengaktifkan Ekstensi GD (Wajib untuk Ekspor PDF & Upload Foto)

Ekstensi `gd` diperlukan oleh `dompdf` (ekspor PDF) dan pemrosesan gambar. Cek apakah sudah aktif:

```bash
php -m | grep gd
```

Kalau belum muncul:

- **XAMPP:** Buka `C:\xampp\php\php.ini`, cari `;extension=gd`, hapus titik koma `;` di depannya, restart Apache.
- **Laragon:** Biasanya sudah aktif secara default.
- **Linux:** `sudo apt install php8.3-gd && sudo systemctl restart apache2`

---

## Instalasi Lengkap (dari Nol)

```bash
# 1. Clone repository
git clone git@github.com:aqillazhr/Sistem-Reservasi-dan-Pelaporan-Fasilitas-Kampus.git
cd Sistem-Reservasi-dan-Pelaporan-Fasilitas-Kampus

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
#    Kalau .env.example tidak ada, buat file .env manual (lihat bagian "Konfigurasi .env")
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Buat database secara manual (lewat MySQL CLI, phpMyAdmin, atau HeidiSQL)
#    CREATE DATABASE ppk_reservasi_fasilitas
#    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 6. Jalankan migration
php artisan migrate

# 7. Jalankan seeder (mengisi data awal: akun, fasilitas, foto, reservasi, laporan)
php artisan db:seed

# 8. Buat symlink storage (WAJIB agar foto fasilitas, laporan, dan profil bisa tampil)
php artisan storage:link

# 9. Jalankan server
php artisan serve
```

Buka browser di **http://127.0.0.1:8000**.

---

## Konfigurasi .env

Buka file `.env` dan sesuaikan bagian database. Referensi lengkap juga ada di `database/migrations/env-database-example.txt`.

```env
APP_NAME="SI Reservasi"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppk_reservasi_fasilitas
DB_USERNAME=root
DB_PASSWORD=
```

---

## Informasi Login (Setelah Seed)

Semua akun seed menggunakan password yang sama: **`password`**

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@kampus.ac.id` | `password` |
| Petugas | `petugas1@kampus.ac.id` | `password` |
| Petugas | `petugas2@kampus.ac.id` | `password` |
| Pengguna (Mahasiswa) | `budi@mahasiswa.ac.id` | `password` |
| Pengguna (Dosen) | `dewi@dosen.ac.id` | `password` |
| Pengguna (Staf) | `andi@staf.ac.id` | `password` |

Selain akun di atas, seeder juga membuat 20 pengguna terverifikasi acak, 5 pengguna pending verifikasi, 3 pengguna ditolak, dan 2 pengguna nonaktif untuk keperluan demo fitur manajemen akun admin.

Akun `Sistem` (`sistem@internal.local`) adalah akun internal untuk pencatatan perubahan status otomatis. Akun ini tidak untuk login.

---

## Fitur per Role

### Pengunjung (Tanpa Login)
- Melihat daftar fasilitas beserta status ketersediaan per slot waktu
- Mencari fasilitas berdasarkan tipe/lokasi/kapasitas
- Memfilter hasil pencarian dan fasilitas tiap fakultas berdasarkan tipe fasilitas, rentang kapasitas minimum dan maksimum, status fasilitas, serta ketersediaan. Hasil yang menggunakan filter kapasitas diurutkan secara ascending berdasarkan kapasitas (dari terkecil ke terbesar)

### Pengguna (Mahasiswa / Dosen / Staf)
- Melihat daftar fasilitas beserta status ketersediaan per slot waktu
- Mencari fasilitas berdasarkan tipe/lokasi/kapasitas
- Memfilter hasil pencarian dan fasilitas tiap fakultas berdasarkan tipe fasilitas, rentang kapasitas minimum dan maksimum, status fasilitas, serta ketersediaan. Hasil yang menggunakan filter kapasitas diurutkan secara ascending berdasarkan kapasitas (dari terkecil ke terbesar)
- Mengajukan reservasi pada rentang waktu tertentu dengan tujuan penggunaan
- Membatalkan reservasi sendiri sebelum batas waktu (H-1)
- Melihat riwayat dan status reservasi (filter: Semua / Menunggu / Aktif / Selesai / Ditolak / Dibatalkan)
- Melaporkan kerusakan fasilitas (kategori, deskripsi, foto) dengan sistem draft
- Melihat status laporan yang sudah dikirim
- Mengubah foto profil, nama, email, dan password

### Petugas
- Dashboard antrian reservasi dan laporan yang menunggu diproses
- Menyetujui/menolak reservasi (sistem mencegah bentrok jadwal otomatis)
- Membatalkan reservasi yang sudah disetujui dalam kondisi mendesak
- Mengubah status laporan kerusakan (baru/diproses/selesai/ditolak) beserta catatan resolusi. Perubahan ini juga otomatis menandai fasilitas terkait jadi "dalam perbaikan" atau mengembalikannya ke "aktif"
- Mengubah foto profil

### Admin
- Mengelola data fasilitas (tambah/edit/nonaktifkan/hapus (soft delete))
- Mendaftarkan akun petugas dan pengguna secara langsung
- Memverifikasi atau menolak akun pengguna hasil registrasi mandiri
- Mengaktifkan/menonaktifkan akun
- Melihat dan mengekspor rekap okupansi fasilitas dan frekuensi kerusakan (CSV / Excel / PDF)
- Mengubah foto profil

---

## Fitur Ekspor (CSV / Excel / PDF)

Tersedia di halaman **Rekap dan Ekspor** pada role Admin (`/admin/rekap`).

- **CSV** dan **Excel** langsung bisa dipakai tanpa konfigurasi tambahan.
- **PDF** menggunakan library `dompdf`. Pastikan ekstensi PHP `gd` aktif (lihat bagian [Prasyarat](#prasyarat)).

---

## Symlink Storage

Perintah `php artisan storage:link` membuat shortcut `public/storage` → `storage/app/public`. Tanpa ini:
- Foto fasilitas tidak muncul
- Foto laporan kerusakan tidak muncul
- Foto profil tidak muncul

Perintah ini cukup dijalankan **satu kali** per environment (per laptop/komputer). Kalau pindah komputer atau clone ulang repo, jalankan lagi.

---

## Aturan Reservasi

Semua konfigurasi aturan bisnis ada di `config/reservation.php`:

| Aturan | Nilai |
|--------|-------|
| Jam operasional | 07.00 - 20.00 |
| Durasi slot | 30 menit (07.00-07.30, 07.30-08.00, dst.) |
| Reservasi maksimal | 7 hari ke depan |
| Pengajuan minimal | 24 jam sebelum jam mulai |
| Pembatalan oleh pengguna | Minimal 24 jam sebelum jam mulai |
| Maksimal reservasi pending | 10 per pengguna |

Validasi slot 30 menit dilakukan di sisi server, bukan hanya di tampilan kalender.

---

## Struktur Folder Utama

```
├── app/
│   ├── Http/Controllers/       # Controller per fitur
│   │   ├── Admin/              #   Rekap & ekspor (AdminReportController)
│   │   ├── Auth/                #   Login, register, manajemen akun
│   │   ├── Facility/            #   CRUD fasilitas
│   │   ├── Report/              #   Laporan kerusakan
│   │   └── Reservation/         #   Reservasi
│   ├── Models/                  # Eloquent model
│   └── Services/                # Business logic (ReservationService)
├── config/
│   └── reservation.php          # Aturan bisnis reservasi
├── database/
│   ├── migrations/              # Skema tabel
│   ├── seeders/                 # Data awal
│   └── factories/               # Factory untuk data acak
├── resources/views/             # Blade template per fitur
├── routes/web.php               # Definisi semua route
├── storage/app/public/
│   ├── avatars/                 # Foto profil (diupload user)
│   ├── facilities/              # Foto fasilitas (sudah termasuk di repo)
│   └── report-photos/           # Foto laporan kerusakan (diupload user)
└── public/                      # Entry point web server
```

---

## Reset Database

Kalau ingin mengulang dari awal (hapus semua data dan seed ulang):

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

---

## Troubleshooting

**Foto fasilitas / profil / laporan tidak muncul (broken image)**

```bash
# Windows PowerShell:
Remove-Item -Recurse -Force public/storage
php artisan storage:link

# Linux/Mac:
rm -rf public/storage
php artisan storage:link
```

**Ekspor PDF error atau halaman putih**
Pastikan ekstensi `gd` aktif: `php -m | grep gd`. Lihat bagian [Mengaktifkan Ekstensi GD](#mengaktifkan-ekstensi-gd-wajib-untuk-ekspor-pdf--upload-foto) di atas.

**Error "SQLSTATE[HY000] table doesn't exist"**
Jalankan `php artisan migrate` sebelum `php artisan db:seed`.

**Error "Specified key was too long"**
Pastikan MySQL >= 8.0.16 dan database menggunakan charset `utf8mb4`.

**Error saat upload foto profil**
Pastikan folder `storage/app/public/avatars` bisa ditulis. Kalau belum ada, Laravel akan membuatnya otomatis saat upload pertama selama permission folder `storage` benar.

**Tombol pagination muncul sangat besar (panah raksasa)**
Jalankan `php artisan view:clear` untuk menghapus cache view lama.
