# MKQuizz

MKQuizz adalah aplikasi pengelolaan materi dan quiz berbasis web. Aplikasi dibangun menggunakan CodeIgniter 4, MySQL, Tailwind CSS, dan JavaScript serta dirancang untuk membantu administrator dan presenter mengelola quiz, sesi, dan hasil peserta.

## Status Pengembangan

Modul yang sudah tersedia:

- Login dan logout administrator serta presenter melalui satu portal
- Proteksi halaman berdasarkan session dan role pengguna
- Akses presenter untuk dashboard, quiz, sesi, dan peserta
- Pembatasan materi dan bank pertanyaan khusus administrator
- Dashboard statistik quiz
- Daftar quiz terbaru dan sesi yang sedang berlangsung
- Pengelolaan material: daftar, pencarian, filter, tambah, edit, status, dan hapus
- Pencegahan penghapusan material yang masih digunakan
- Bank pertanyaan: filter, tambah, edit, status, dan hapus aman
- Pengelolaan 2–5 pilihan jawaban beserta satu kunci jawaban benar
- Dukungan tipe pilihan ganda dan benar/salah
- Daftar quiz dengan pencarian, filter, statistik konten, sesi, dan pengerjaan
- Pembuatan quiz dengan konfigurasi dan pemilihan pertanyaan secara transaksional
- Detail quiz dengan konfigurasi, pertanyaan, jawaban benar, sesi, dan performa peserta
- Daftar dan detail sesi quiz dengan PIN, token, peserta, serta performa sesi
- Pembuatan sesi terjadwal dengan PIN dan token akses otomatis
- Halaman masuk peserta per sesi dengan nama lengkap, PIN, dan validasi kapasitas
- Halaman pengerjaan quiz bergaya Google Form dengan timer, progress jawaban, dan submit otomatis
- Penilaian jawaban di server serta halaman hasil berisi nilai, kelulusan, ringkasan jawaban, dan durasi
- Review jawaban peserta bergaya Google Form dengan penanda jawaban peserta, jawaban benar, dan penjelasan
- Link dan QR Code peserta pada detail sesi, lengkap dengan salin link dan unduh QR
- Halaman presentasi QR ukuran besar untuk ditampilkan dan dipindai peserta
- Leaderboard sesi dengan podium, statistik, akurasi, durasi, dan peringkat lengkap
- Report evaluasi sesi dengan chart jawaban benar, salah, tidak dijawab, dan soal prioritas evaluasi
- Report evaluasi quiz yang menggabungkan hasil dari seluruh sesi terkait
- Daftar dan detail peserta dengan filter aktivitas, riwayat pengerjaan, nilai, dan informasi perangkat
- Tampilan responsif bertema putih dan oranye

Skema database juga sudah menyediakan tabel untuk pertanyaan, pilihan jawaban, quiz, sesi, peserta, pengerjaan quiz, jawaban peserta, serta audit log. Antarmuka untuk modul-modul tersebut akan dikembangkan secara bertahap.

## Teknologi

- PHP 8.2+
- CodeIgniter 4.7
- MySQL atau MariaDB
- Tailwind CSS 4 melalui `@tailwindcss/cli`
- JavaScript
- npm
- Apache/XAMPP

## Kebutuhan Sistem

Pastikan perangkat memiliki:

- PHP 8.2 atau lebih baru
- MySQL/MariaDB
- Composer
- Node.js dan npm
- Ekstensi PHP `intl`, `mbstring`, dan `mysqli`
- Apache dengan modul `rewrite` aktif jika menggunakan XAMPP

Untuk memeriksa versi yang terpasang:

```bash
php --version
composer --version
node --version
npm --version
```

## Instalasi dengan XAMPP

### 1. Letakkan project

Letakkan project di dalam folder `htdocs` XAMPP. Contoh:

```text
C:\xampp\htdocs\mkquizz
```

### 2. Instal dependency

Jalankan perintah berikut dari direktori project:

```bash
composer install
npm install
```

### 3. Siapkan database

1. Jalankan Apache dan MySQL dari XAMPP Control Panel.
2. Buka phpMyAdmin.
3. Buat database bernama `mkquizz`.
4. Import file `mkquizz.sql` yang tersedia di root project.

Import juga dapat dilakukan melalui command line:

```bash
mysql -u root -p mkquizz < mkquizz.sql
```

### 4. Konfigurasi environment

Buat file `.env` di root project, lalu sesuaikan konfigurasi berikut:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/mkquizz/public/'
app.indexPage = ''

database.default.hostname = localhost
database.default.database = mkquizz
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

Jangan menyimpan file `.env` ke repository karena dapat berisi kredensial lokal atau production.

### 5. Buat akun administrator

Jalankan seeder administrator:

```bash
php spark db:seed AdminSeeder
```

Seeder dapat dijalankan kembali untuk mengatur ulang password akun development.

### 6. Build stylesheet

Build Tailwind CSS untuk penggunaan normal:

```bash
npm run build
```

Gunakan mode watch ketika mengembangkan tampilan:

```bash
npm run dev
```

Source stylesheet berada di `resources/css/app.css`. Hasil build tersedia di `public/assets/css/app.css`.

### 7. Buka aplikasi

Dengan Apache XAMPP, buka:

```text
http://localhost/mkquizz/public/admin/login
```

Untuk konfigurasi production, arahkan document root web server langsung ke direktori `public` agar folder aplikasi lainnya tidak dapat diakses dari web.

## Akun Development

Gunakan akun berikut untuk mencoba aplikasi secara lokal:

```text
Email    : admin@mkquizz.edu
Password : admin123
```

Akun presenter development:

```text
Email    : presenter@mkquizz.edu
Password : presenter123
```

> Password tersebut hanya ditujukan untuk development lokal. Ganti dengan password yang kuat sebelum aplikasi digunakan di production.

## Menjalankan Development Server

Selain Apache XAMPP, aplikasi dapat dijalankan menggunakan server bawaan CodeIgniter:

```bash
php spark serve
```

Jika menggunakan cara ini, sesuaikan konfigurasi URL pada `.env`:

```ini
app.baseURL = 'http://localhost:8080/'
```

Kemudian buka `http://localhost:8080/admin/login`.

## Struktur Direktori Utama

```text
mkquizz/
├── app/
│   ├── Config/             # Route, filter, database, dan konfigurasi aplikasi
│   ├── Controllers/Admin/  # Controller autentikasi, dashboard, dan material
│   ├── Database/Seeds/     # Seeder akun administrator
│   ├── Filters/            # Filter autentikasi administrator
│   ├── Models/             # Model user, dashboard, material, pertanyaan, quiz, sesi, dan peserta
│   ├── Services/           # Transaksi penyimpanan pertanyaan dan opsi jawaban
│   └── Views/admin/        # Layout dan halaman admin
├── public/
│   └── assets/             # CSS hasil build dan JavaScript browser
├── resources/css/          # Source Tailwind dan custom stylesheet
├── writable/               # Cache, log, session, dan upload runtime
├── mkquizz.sql             # Struktur dan data awal database
├── package.json            # Script dan dependency frontend
└── spark                   # CLI CodeIgniter
```

## Route Admin

| Method | URL | Keterangan |
| --- | --- | --- |
| `GET` | `/admin/login` | Form login administrator |
| `POST` | `/admin/login` | Proses autentikasi |
| `POST` | `/admin/logout` | Mengakhiri session administrator |
| `GET` | `/admin/dashboard` | Dashboard administrator |
| `GET` | `/admin/materials` | Daftar dan filter material |
| `GET` | `/admin/materials/create` | Form tambah material |
| `POST` | `/admin/materials` | Menyimpan material baru |
| `GET` | `/admin/materials/{id}/edit` | Form edit material |
| `POST` | `/admin/materials/{id}` | Memperbarui material |
| `POST` | `/admin/materials/{id}/toggle` | Mengubah status material |
| `POST` | `/admin/materials/{id}/delete` | Menghapus material |
| `GET` | `/admin/questions` | Daftar dan filter bank pertanyaan |
| `GET` | `/admin/questions/create` | Form pertanyaan dan pilihan jawaban |
| `POST` | `/admin/questions` | Menyimpan pertanyaan beserta opsi |
| `GET` | `/admin/questions/{id}/edit` | Form edit pertanyaan dan opsi |
| `POST` | `/admin/questions/{id}` | Memperbarui pertanyaan dan opsi |
| `POST` | `/admin/questions/{id}/toggle` | Mengubah status pertanyaan |
| `POST` | `/admin/questions/{id}/delete` | Menghapus pertanyaan dan opsi |
| `GET` | `/admin/quizzes` | Daftar, pencarian, dan filter quiz |
| `GET` | `/admin/quizzes/create` | Form tambah quiz dan pemilihan pertanyaan |
| `POST` | `/admin/quizzes` | Menyimpan quiz beserta pertanyaan |
| `GET` | `/admin/quizzes/{id}` | Detail, pertanyaan, sesi, dan performa quiz |
| `GET` | `/admin/quizzes/{id}/report` | Report gabungan seluruh sesi yang menggunakan quiz |
| `GET` | `/admin/sessions` | Daftar, pencarian, dan filter sesi quiz |
| `GET` | `/admin/sessions/create` | Form tambah dan penjadwalan sesi quiz |
| `POST` | `/admin/sessions` | Menyimpan sesi serta membuat PIN dan token |
| `POST` | `/admin/sessions/{id}/extend-pin` | Menambah atau mengaktifkan kembali masa berlaku PIN |
| `GET` | `/admin/sessions/{id}` | Detail akses, peserta, dan performa sesi |
| `GET` | `/admin/sessions/{id}/share` | Tampilan QR dan link peserta berukuran besar |
| `GET` | `/admin/sessions/{id}/leaderboard` | Podium dan daftar hasil peserta per sesi |
| `GET` | `/admin/sessions/{id}/report` | Chart dan analisis jawaban benar/salah per soal |
| `GET` | `/admin/participants` | Daftar, pencarian, dan filter peserta |
| `GET` | `/admin/participants/{id}` | Detail peserta dan riwayat pengerjaan quiz |
| `GET` | `/quiz/{session_token}` | Halaman masuk peserta berdasarkan sesi quiz |
| `POST` | `/quiz/{session_token}/join` | Validasi PIN dan pendaftaran peserta |
| `GET` | `/quiz/{session_token}/lobby` | Konfirmasi peserta berhasil bergabung |
| `POST` | `/quiz/{session_token}/start` | Membuat atau melanjutkan attempt peserta |
| `GET` | `/quiz/{session_token}/attempt/{id}` | Halaman pengerjaan soal peserta |
| `POST` | `/quiz/{session_token}/attempt/{id}/submit` | Menyimpan jawaban dan menghitung hasil quiz |
| `GET` | `/quiz/{session_token}/result/{id}` | Halaman hasil quiz peserta |
| `GET` | `/quiz/{session_token}/result/{id}/review` | Review soal dan jawaban peserta |
| `GET` | `/quiz/{session_token}/leaderboard` | Leaderboard publik hasil peserta per sesi |

Seluruh halaman panel dilindungi oleh filter `adminAuth`. Route material dan pertanyaan juga memakai filter `adminRole`, sehingga hanya `ADMIN` dan `SUPERADMIN` yang dapat mengaksesnya. Seluruh request yang mengubah data dilindungi oleh CSRF.

## Keamanan Autentikasi

Modul autentikasi menerapkan:

- Penyimpanan password menggunakan hash PHP
- Verifikasi password menggunakan `password_verify`
- Regenerasi session ID setelah login dan logout
- Pembatasan percobaan login
- Validasi role `ADMIN`, `SUPERADMIN`, dan `PRESENTER`
- Pembatasan akses berbasis role untuk halaman pengelolaan materi dan pertanyaan
- Proteksi CSRF pada request `POST`
- Escaping data ketika ditampilkan pada view

## Perintah Berguna

```bash
# Melihat seluruh route
php spark routes

# Memeriksa konfigurasi
php spark config:check

# Menjalankan seeder administrator
php spark db:seed AdminSeeder

# Build CSS untuk production
npm run build

# Pantau perubahan CSS saat development
npm run dev
```

## Troubleshooting

### Tampilan CSS belum berubah

Jalankan kembali build Tailwind kemudian lakukan hard refresh di browser:

```bash
npm run build
```

Pada Windows/Linux gunakan `Ctrl + F5`, sedangkan pada macOS gunakan `Cmd + Shift + R`.

### Aplikasi gagal terhubung ke database

Pastikan MySQL berjalan, database `mkquizz` sudah dibuat, dan konfigurasi `database.default` di `.env` sesuai dengan lingkungan lokal.

### Error permission pada runtime

Pastikan direktori `writable` dapat ditulis oleh proses web server. Isi cache, log, debugbar, session, dan upload tidak perlu dimasukkan ke Git.

## Lisensi

Framework CodeIgniter tersedia di bawah lisensi MIT. Ketentuan lisensi dapat dilihat pada file `LICENSE`.
