# Interface 6 SQL View — Toko Sembako Gen Z

Aplikasi **PHP Native, PDO, dan SQL SELECT langsung** untuk menampilkan enam SQL View data master. **Tidak menggunakan Query Builder, tidak perlu trigger, dan tidak membuat tabel baru.**

## Persiapan
Pastikan MySQL Laragon berjalan dan website utama kamu sudah bisa dibuka dengan normal. Folder website utama pada contoh adalah `C:\laragon\www\toko_sembako_gen_z_uts_v2`.

## Instalasi (AMAN: tidak menimpa kode CUD lama)
1. Ekstrak ZIP.
2. Salin **hanya folder** `interface_6_view` ke dalam folder website utama `C:\laragon\www\toko_sembako_gen_z_uts_v2\`.
3. Folder `interface_6_view` harus sejajar dengan `index.php` dan folder `includes` website utama. **Jangan** memindahkan isi folder ke dalam `includes`.
4. Tidak perlu mengganti `config.php` atau file `includes/db.php` yang sudah terhubung ke database.
5. Buka `http://localhost:8080/toko_sembako_gen_z_uts_v2/interface_6_view/index.php`.

## 6 View yang digunakan (sesuai laporan/database sebelumnya)

| Menu | SQL View | Kolom yang ditampilkan |
|---|---|---|
| Data Role | `v_master_role` | `idrole`, `nama_role` |
| Data User | `v_master_user` | `iduser`, `username`, `nama_role` |
| Data Satuan | `v_master_satuan` | `idsatuan`, `nama_satuan`, `status` |
| Data Barang | `v_master_barang` | `idbarang`, `jenis`, `nama_barang`, `nama_satuan`, `harga`, `status` |
| Data Vendor | `v_master_vendor` | `idvendor`, `nama_vendor`, `badan_hukum`, `status` |
| Margin Aktif | `v_margin_aktif` | `idmargin_penjualan`, `persen`, `status`, `created_at` |

**Perhatian:** `v_margin_aktif` menampilkan **hanya margin aktif**. Tidak harus menampilkan seluruh 5 opsi margin (10–50%).

## Cara menguji bahwa data benar-benar berasal dari View
1. Di HeidiSQL, jalankan `SELECT * FROM v_master_barang;`, bandingkan data dengan menu **Data Barang** di interface.
2. Jalankan `SELECT * FROM v_master_user;`, bandingkan dengan menu **Data User**.
3. Ubah salah satu barang menggunakan modul CUD yang sudah ada (gunakan data uji, jangan rusak data utama), lalu refresh menu Data Barang pada interface. Perubahan dari tabel sumber akan tercermin di SQL View secara otomatis.
4. Screenshot bagian dashboard dan enam menu data master untuk responsi.

## Jika menu menunjukkan 'View belum dapat dibaca'
Periksa apakah nama dan kolom di SQL View sesuai laporan. Di HeidiSQL jalankan:

```sql
SHOW FULL TABLES WHERE Table_type = 'VIEW';
SELECT * FROM v_master_role;
SELECT * FROM v_master_user;
SELECT * FROM v_master_satuan;
SELECT * FROM v_master_barang;
SELECT * FROM v_master_vendor;
SELECT * FROM v_margin_aktif;
```

## Penjelasan singkat untuk responsi
- **Tabel**: menyimpan record asli di database.
- **SQL View**: query SELECT yang disimpan di database, membentuk tampilan data virtual dari tabel sumber.
- **Interface (antarmuka)**: tampilan website yang dilihat/digunakan orang; PHP membaca SQL View dan menampilkan datanya sebagai dashboard dan tabel.
- **Perbedaan**: SQL View berada pada database, interface berada pada aplikasi web.

## Keamanan
- Halaman ini **read-only**; belum dilengkapi login. Gunakan hanya dalam lingkungan responsi/lokal sampai autentikasi dan otorisasi tersedia.
- File `config.php` berisi kredensial tetap berada di folder website utama dan **tidak termasuk** di ZIP ini. Jangan unggah kredensial ke GitHub.
- Username ditampilkan dari View, password tidak ikut dipilih.
- Harga memakai `barang.harga`; belum otomatis dihitung dengan margin dan belum ditetapkan sebagai harga jual akhir.
