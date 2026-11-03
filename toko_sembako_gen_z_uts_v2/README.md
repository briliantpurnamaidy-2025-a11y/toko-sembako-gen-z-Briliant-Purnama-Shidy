# TOKO SEMBAKO GEN Z — Cicilan UI + CUD UTS

Prototype web asli berbasis **PHP Native + MySQL/MariaDB (PDO dengan SQL langsung)**. Tidak menggunakan ORM atau Query Builder. Sistem memakai tabel yang sudah ada sesuai ERD dosen; tidak ada file SQL yang otomatis mengubah struktur database.

## Persiapan Laragon

1. Ekstrak folder ZIP `toko_sembako_gen_z_uts_v2` ke `C:\laragon\www\`.
2. Jalankan Apache dan MySQL/MariaDB di Laragon.
3. Ubah `config.php` dan sesuaikan `DB_NAME` dengan nama database milikmu (sebelumnya terlihat `in_praktikumpbd` dalam screenshot laporan). Pengaturan default dalam paket ini perlu diperiksa.
4. Buka `http://localhost/toko_sembako_gen_z_uts_v2/index.php` untuk **Dashboard Pembeli**.
5. Buka `http://localhost/toko_sembako_gen_z_uts_v2/katalog.php` untuk daftar barang & harga.
6. Buka `http://localhost/toko_sembako_gen_z_uts_v2/dashboard.php` untuk Panel Pengelola, lanjut ke `barang.php` dan `satuan.php`.
7. Untuk versi pratinjau tanpa DB, buka `demo/pratinjau.html` (khusus demonstrasi desain; tidak menyimpan data ke MySQL).

## Fitur yang benar-benar tersedia dalam kode

- Dashboard Pembeli: menampilkan katalog produk berstatus 1 dan harga dari `barang.harga`.
- Daftar Barang & Harga: tabel produk, cari nama, dan filter satuan (PDO prepared statement).
- Dashboard Pengelola: jumlah barang, satuan, vendor, pengguna.
- **CUD Data Barang**: tambah/edit/hapus barang; form nama, kode jenis satu karakter, satuan, harga, status.
- **CUD Data Satuan**: tambah/edit/hapus satuan, sebagaimana paket versi pertama.
- Perlindungan CSRF dan query parametrik untuk operasi masukan. Foreign key mencegah hapus barang yang sudah dipakai tabel terkait.

## Batasan yang harus disebut di laporan

- **Katalog belum sama dengan transaksi pembelian.** Belum ada checkout, akun pembeli, pembayaran, atau pengurangan stok akibat penjualan. Itu pekerjaan pengembangan untuk UAS.
- `barang.harga` ditampilkan langsung. Perhitungan margin belum diimplementasikan pada frontend; pastikan arti harga jual dan harga beli disepakati sebelum produksi.
- Tidak ada autentikasi/otorisasi panel pengelola. **Hanya untuk demonstrasi localhost**, jangan dihosting secara publik.
- **Kode sudah diperiksa sintaks PHP (`php -l`), tetapi koneksi ke DB Laragon Anda belum bisa diuji dari sini.** Verifikasi dengan browser dan database aktual sebelum mengklaim fitur sukses.
- Data demo/screenshot pratinjau adalah contoh visual, bukan bukti transaksi aktual.

## Langkah uji CUD yang aman

1. Buka Panel Pengelola → **Data Barang** → Tambah Barang.
2. Buat `Barang Uji CUD`, jenis `1` (kode contoh, arti jenis belum dijelaskan ERD), pilih satuan yang ada, harga mis. `12345`, status Aktif; simpan dan periksa di tabel serta katalog.
3. Edit `Barang Uji CUD` menjadi `Barang Uji Edit`, harga `13500`; simpan dan buktikan hasil berubah.
4. Hapus barang uji tadi, lalu cek baris menghilang dari database dan katalog.
5. Ulangi CUD untuk **Data Satuan** dengan `Satuan Uji`, lalu edit/hapus bila belum digunakan barang.
6. Ambil **screenshot hasil dari browser lokal** untuk laporan. Screenshot pratinjau tidak membuktikan query berjalan.

## Penjelasan file

- `index.php`, `katalog.php`: tampilan pembeli (baca data saja)
- `dashboard.php`: dashboard pengelola
- `barang.php`, `barang-form.php`, `barang-save.php`, `barang-delete.php`: CUD barang
- `satuan.php`, `satuan-form.php`, `satuan-save.php`, `satuan-delete.php`: CUD satuan
- `includes/db.php`: koneksi PDO
- `includes/store_layout.php`, `includes/layout.php`: layout pembeli dan admin
- `assets/styles.css`, `assets/app.js`: desain dan konfirmasi hapus
