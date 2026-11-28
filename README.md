 # Warung Nasi Tenda Biru — Company Profile + Pemesanan Online

Stack: PHP 8 (PDO + SQLite) · Bootstrap 5 · JavaScript (keranjang, polling notifikasi, QR).

## Cara menjalankan
**XAMPP/Laragon:** taruh folder ini di `htdocs`, buka `http://localhost/warung-tenda-biru/`.
**Tanpa XAMPP:** jalankan `php -S localhost:8000` di dalam folder ini, buka `http://localhost:8000`.
Butuh ekstensi PHP `pdo_sqlite` (biasanya aktif). Folder `database/` harus bisa ditulis.
Database dibuat otomatis (20 menu + akun admin) jika `database/warung.sqlite` belum ada.

## Admin
`/admin/` — user `admin`, password `admin123` (segera ganti di Admin > Pengaturan).
- Pesanan: notifikasi otomatis (cek tiap 5 detik) + suara + toast; tandai lunas, ubah status masak.
  Klik "Aktifkan suara" sekali agar browser mengizinkan bunyi.
- Menu: tambah/ubah/hapus, unggah foto, tandai habis, pilih tampil di beranda.
- Pengaturan: alamat, jam, kontak, dan kode QRIS statis toko.

## Alur pelanggan
Menu -> keranjang (JS) -> Checkout -> QRIS otomatis (nominal sesuai total) atau Bayar di kasir (nomor antrean A001, dst.)
-> halaman Cek Pesanan terpisah (`cek-status.php`) memakai nomor pesanan.

## QRIS
Tempel kode QRIS statis toko di Pengaturan; sistem mengubahnya jadi QRIS dinamis (nominal + CRC16).   

Kosong = QR demo (bukan rekening asli). Belum terhubung payment gateway: kasir memverifikasi lewat mutasi lalu klik
"Tandai lunas". Untuk verifikasi otomatis, sambungkan Midtrans/Xendit di folder `api/`.

## Struktur
index.php (homepage, 1 file) · pages/ (menu, checkout, pesanan, cek-status) · includes/ · api/ · admin/ · assets/{css,js,images/{menu,hero,gallery}} · database/ (schema.sql, seed.php, warung.sqlite)
 

 
