# Template Mazer

Template panel admin berbasis **Laravel 13** dan tema **Mazer** (Bootstrap 5). Siap dipakai sebagai fondasi aplikasi internal: login, manajemen user, role & permission, pemantauan user online, dan activity log sudah tersedia, sehingga modul baru tinggal ditambahkan.

## Daftar Isi

- [Fitur](#fitur)
- [Kebutuhan](#kebutuhan)
- [Instalasi Lokal](#instalasi-lokal)
- [Deploy Pertama](#deploy-pertama)
- [Deploy Update](#deploy-update)
- [Cara Penggunaan](#cara-penggunaan)
- [Menambah Modul Baru](#menambah-modul-baru)
- [Struktur Folder](#struktur-folder)
- [Testing](#testing)

## Fitur

| Fitur | Keterangan |
|---|---|
| Login | Login email + password, logout, lupa password. |
| Dashboard | Sapaan dan role user, daftar user online, jumlah user dan role, 8 perubahan data terakhir, tombol akses cepat sesuai hak akses. |
| Users | Tambah, ubah, hapus user dan tetapkan role-nya. |
| Roles & Permissions | Buat role, pilih permission per modul (dengan "Pilih semua"), lihat detail, ubah, hapus. |
| Log Aktif | Daftar user, kapan terakhir terlihat, status Online/Offline (aktif dalam 10 menit terakhir). |
| Activity Log | Riwayat tambah/ubah/hapus data: siapa, kapan, dari IP mana, nilai sebelum dan sesudah. Bisa difilter per aksi dan jenis data. |
| Installer | `php artisan app:install` menyiapkan aplikasi dan akun admin pertama dalam satu perintah. |
| Mode gelap | Tombol terang/gelap di sidebar, tersimpan di browser. |

Keamanan yang sudah diterapkan:

- Setiap menu dan tombol aksi dicek permission-nya, baik di tampilan maupun di backend.
- Role `super-admin` selalu punya akses penuh, tidak bisa dihapus atau diganti nama, dan hanya terlihat oleh sesama super-admin.
- User biasa tidak bisa memberi role `super-admin`, serta tidak bisa mengubah atau menghapus akun super-admin.
- Activity log tidak pernah menyimpan atau menampilkan password, `remember_token`, maupun header `Authorization`.

## Kebutuhan

- PHP **8.4** dengan extension `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `fileinfo`.
- Composer 2.
- MySQL 8 / MariaDB.
- Node.js 18+ (untuk build aset halaman login bawaan: lupa password, profil).

## Instalasi Lokal

```bash
git clone <repo> template-mazer
cd template-mazer
composer install
npm ci && npm run build
cp .env.example .env
```

Buat database kosong (misal `template_mazer`), lalu isi `.env`:

```env
APP_NAME="Template Mazer"
APP_URL=http://template-mazer.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=template_mazer
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan installer, lalu server:

```bash
php artisan app:install
php artisan serve
```

Buka `http://127.0.0.1:8000` dan login dengan akun admin yang dibuat installer.

> Laragon: bila `php` di PATH bukan 8.4, panggil langsung `C:/laragon/bin/php/php-8.4.27-nts-Win32-vs17-x64/php.exe artisan ...`.

## Deploy Pertama

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
cp .env.example .env          # isi APP_URL, DB_*, MAIL_*; APP_ENV=production, APP_DEBUG=false
php artisan app:install --force
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`app:install` bersifat interaktif dan berhenti bila salah satu langkah gagal:

1. Membuat `APP_KEY` bila masih kosong.
2. Menjalankan migration.
3. Menjalankan `PermissionSeeder`: membuat semua permission dan role `super-admin`.
4. Meminta nama, email, dan password (minimal 10 karakter) admin pertama, lalu memberinya role `super-admin`.

Flag `--force` wajib bila `APP_ENV=production`. Installer menolak email yang sudah terdaftar, jadi aman dijalankan ulang tanpa mengubah akun yang ada.

## Deploy Update

```bash
php artisan down --retry=60
git pull
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan db:seed --class=PermissionSeeder --force   # bila ada permission baru
npm ci && npm run build                                # bila aset berubah
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

## Cara Penggunaan

### Login

Buka aplikasi, masukkan email dan password. Setelah login Anda masuk ke **Dashboard**. Keluar lewat menu **LOGOUT** di sidebar.

### Dashboard

- **Sedang online**: user yang membuka aplikasi dalam 10 menit terakhir beserta role-nya.
- **Perubahan data terakhir**: 8 aktivitas terbaru; klik **Lihat semua log** untuk membuka Activity Log. Bagian ini hanya muncul bagi yang punya akses Activity Log.
- **Akses cepat**: tombol ke Tambah user, Kelola user, dan Kelola hak akses sesuai permission Anda.

### Membuat role (Access Control List → Roles & Permissions)

1. Klik **Tambah role**.
2. Isi **Nama role**, misal `operator`.
3. Centang aksi yang boleh dilakukan. Permission dikelompokkan per modul; **Pilih semua** mencentang seluruh aksi satu modul, dan angka `x/y` menunjukkan jumlah yang terpilih.
4. Klik **Simpan role**.

Perubahan permission langsung berlaku untuk semua user yang memegang role tersebut. Tombol **Detail** menampilkan permission aktif sebuah role tanpa bisa diubah. Menghapus role akan mencabutnya dari semua user pemegangnya; jumlah user terdampak ditampilkan di konfirmasi.

Arti aksi permission:

| Aksi | Label | Contoh |
|---|---|---|
| `index` | Lihat daftar | `acl_user_index` membuka menu Users |
| `show` | Lihat detail | `acl_role_show` membuka detail role |
| `create` | Tambah | `acl_user_create` menampilkan tombol Tambah user |
| `edit` | Ubah | `acl_role_edit` mengubah role |
| `delete` | Hapus | `acl_user_delete` menghapus user |

Permission `main_acl_index` menampilkan menu induk **Access Control List** di sidebar; berikan bersama permission submenu yang diinginkan.

### Mengelola user (Access Control List → Users)

1. Klik **Tambah Data**, isi nama, email, password, dan pilih role.
2. Untuk mengubah, klik **Edit**. Kosongkan password bila tidak ingin menggantinya.
3. **Delete** menghapus user setelah konfirmasi.

Akun super-admin tidak bisa diubah atau dihapus oleh user non-super-admin, dan pilihan role `super-admin` hanya muncul bagi super-admin.

### Memantau user (Access Control List → Log Aktif)

Menampilkan semua user dengan waktu terakhir terlihat dan status **Online** (aktif 10 menit terakhir) atau **Offline**.

### Melacak perubahan data (Access Control List → Activity Log)

1. Saring dengan **Aksi** (Tambah/Ubah/Hapus) dan **Data** (User, Role, ...), lalu klik **Terapkan**.
2. Klik **Detail** untuk melihat isi data. Pada aksi Ubah, kolom yang berubah disorot beserta nilai sebelum dan sesudahnya.

Data yang dicatat: model yang didaftarkan di `app/Providers/AppServiceProvider.php` (saat ini `User` dan `Role`).

### Hak akses bawaan

| Permission | Fungsi |
|---|---|
| `main_acl_index` | Menu Access Control List |
| `acl_user_index` / `create` / `edit` / `delete` | Users |
| `acl_role_index` / `show` / `create` / `edit` / `delete` | Roles & Permissions |
| `acl_log_index` | Log Aktif |
| `acl_audit_index` / `acl_audit_show` | Activity Log dan detailnya |

## Menambah Modul Baru

1. **Permission**: tambahkan nama ke `database/seeders/PermissionSeeder.php` dengan pola `{grup}_{modul}_{aksi}`, misal `inventory_barang_index`, lalu jalankan `php artisan db:seed --class=PermissionSeeder`. Permission otomatis muncul di form role, dikelompokkan per grup dan modul.
2. **Controller**: lindungi setiap aksi di constructor:

   ```php
   public function __construct()
   {
       $this->middleware('permission:inventory_barang_index')->only(['index']);
       $this->middleware('permission:inventory_barang_create')->only(['create', 'store']);
   }
   ```

3. **Route**: daftarkan di grup `auth` pada `routes/web.php`.
4. **View**: buat di `resources/views/v/{modul}/`, extend `layouts.dashboard`, dan bungkus tombol aksi dengan `@can('inventory_barang_create') ... @endcan`.
5. **Sidebar**: tambahkan menu di `resources/views/layouts/sidebar.blade.php` di dalam `@can(...)`.
6. **Activity Log** (opsional): daftarkan model di `AppServiceProvider::boot()`:

   ```php
   \App\Models\Barang::observe(GlobalModelObserver::class);
   ```

## Struktur Folder

```
app/Console/Commands/InstallApplication.php   # php artisan app:install
app/Http/Controllers/                         # Dashboard, User, Role, Log, AuditLog
app/Models/                                   # User, Role, Permission, ActivityLog
app/Observers/GlobalModelObserver.php         # pencatat Activity Log
database/seeders/PermissionSeeder.php         # daftar permission + role super-admin
resources/views/layouts/                      # layout dashboard dan sidebar
resources/views/v/                            # halaman per modul
routes/web.php                                # route aplikasi
```

## Testing

```bash
php artisan test
```

Test memakai SQLite in-memory (`phpunit.xml`), jadi tidak menyentuh database MySQL. `tests/Feature/RoleAccessTest.php` mencakup aturan super-admin, validasi role, Activity Log, dan installer.
