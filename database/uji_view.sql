-- Jalankan di HeidiSQL pada database "praktikumpbd" (atau database yang kamu pakai).
-- Skrip INI HANYA MEMBACA DATA, tidak mengubah tabel atau SQL VIEW.
SELECT DATABASE() AS database_aktif;
SELECT TABLE_NAME AS nama_view
FROM information_schema.VIEWS
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY TABLE_NAME;

SELECT * FROM v_master_role;
SELECT * FROM v_master_user;
SELECT * FROM v_master_satuan;
SELECT * FROM v_master_barang;
SELECT * FROM v_master_vendor;
SELECT * FROM v_margin_aktif;
