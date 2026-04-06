# KBC Kotabaru

Website kompetisi basket **Kotabaru Basketball Competition (KBC)** berbasis:

- Laravel 13
- Tailwind CSS 4
- Firebase Realtime Database (database utama)

Fitur utama:

- Halaman user: beranda liga, pertandingan, jadwal, klub, turnamen.
- Panel admin: CRUD turnamen, klub, jadwal, pertandingan.
- Portal klub: login Google role klub, onboarding data klub (penanggung jawab, coach + KTP, logo, event), dan CRUD peserta/pemain.
- Auth berbasis session dengan role `admin` dan `club`.
- Data tersimpan di Firebase Realtime Database.

## 1. Persiapan

1. Copy environment file:
   ```bash
   cp .env.example .env
   ```
2. Isi variabel Firebase di `.env`:
   ```env
   FIREBASE_PROJECT=app
   FIREBASE_CREDENTIALS=D:/path/ke/firebase-service-account.json
   # atau:
   GOOGLE_APPLICATION_CREDENTIALS=D:/path/ke/firebase-service-account.json
   ```
3. Isi variabel Google OAuth untuk login klub di `.env`:
   ```env
   GOOGLE_CLIENT_ID=
   GOOGLE_CLIENT_SECRET=
   GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/club/auth/google/callback
   ```
4. Install dependency backend & frontend:
   ```bash
   composer install
   npm install
   ```
5. Generate key + migrate tabel lokal Laravel (untuk session/cache):
   ```bash
   php artisan key:generate
   php artisan migrate
   ```
6. Aktifkan symlink storage agar gambar klub/pemain tampil:
   ```bash
   php artisan storage:link
   ```

## 2. Bootstrap Data Firebase

Jalankan command berikut untuk membuat akun admin + sample data liga:

```bash
php artisan app:firebase-bootstrap-data
```

Default akun admin:

- Email: `admin@kbckotabaru.id`
- Password: `admin12345`

Jika ingin paksa generate ulang sample data:

```bash
php artisan app:firebase-bootstrap-data --force
```

## 3. Menjalankan Aplikasi

Jalankan server Laravel dan Vite:

```bash
php artisan serve
npm run dev
```

Akses:

- User/public: `http://127.0.0.1:8000`
- Admin panel: `http://127.0.0.1:8000/admin` (harus login admin)
- Portal klub: `http://127.0.0.1:8000/club/register` dan `http://127.0.0.1:8000/club/login`

Alur portal klub:

1. Klub login dengan Google.
2. Jika belum punya data klub, otomatis diarahkan ke onboarding `/club/onboarding`.
3. Isi data klub + upload dokumen + (opsional) isi maksimal 15 peserta.
4. Data otomatis muncul di panel admin bagian klub.

## 4. Struktur Utama

- `app/Services/FirestoreService.php`: wrapper koneksi Firebase Realtime Database.
- `app/Services/KbcRepository.php`: akses data domain KBC.
- `app/Services/SessionAuthService.php`: auth session + role.
- `app/Http/Controllers/Frontend/*`: halaman user.
- `app/Http/Controllers/Admin/*`: CRUD admin.
- `resources/views/frontend/*`: UI publik.
- `resources/views/admin/*`: UI panel admin.

## 5. Catatan

- Jika Firebase belum dikonfigurasi, aplikasi tetap bisa dibuka tetapi operasi CRUD akan gagal dan menampilkan notifikasi error.
- Pastikan service account Firebase memiliki akses Realtime Database read/write.
