<?php
require_once __DIR__.'/includes/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Metode tidak diizinkan.');}
verify_csrf();$id=positive_id($_POST['id']??null);
if($id===null){flash('ID barang tidak valid.','error');go('barang.php');}
try{
 $stmt=db()->prepare('DELETE FROM barang WHERE idbarang=:id');
 $stmt->execute(['id'=>$id]);
 flash($stmt->rowCount()?'Data barang berhasil dihapus.':'Barang tidak ditemukan.', $stmt->rowCount()?'success':'error');
}catch(PDOException $e){flash('Barang tidak dapat dihapus karena telah digunakan pada transaksi/kartu stok atau ada batasan database. Gunakan status Nonaktif.','error');}
go('barang.php');
