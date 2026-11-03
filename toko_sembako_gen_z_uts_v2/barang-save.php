<?php
require_once __DIR__.'/includes/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Metode tidak diizinkan.');}
verify_csrf();
$rawId=trim((string)($_POST['id']??''));$id=$rawId===''?null:positive_id($rawId);
if($rawId!=='' && $id===null){flash('ID barang tidak valid.','error');go('barang.php');}
$nama=trim((string)($_POST['nama']??''));
$jenis=trim((string)($_POST['jenis']??''));
$satuan=positive_id($_POST['idsatuan']??null);
$hargaRaw=filter_var($_POST['harga']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>0,'max_range'=>2147483647]]);
$status=(string)($_POST['status']??'');
$validName=$nama!=='' && (function_exists('mb_strlen')?mb_strlen($nama,'UTF-8'):strlen($nama))<=45;
$validJenis=$jenis!=='' && (function_exists('mb_strlen')?mb_strlen($jenis,'UTF-8'):strlen($jenis))===1;
if(!$validName || !$validJenis || $satuan===null || $hargaRaw===false || !in_array($status,['0','1'],true)){
 flash('Periksa nama (maks. 45), kode jenis (1 karakter), satuan, harga, dan status.','error');go($id===null?'barang-form.php':'barang-form.php?id='.$id);
}
try {
 $pdo=db();
 $q=$pdo->prepare('SELECT COUNT(*) FROM satuan WHERE idsatuan=:id');$q->execute(['id'=>$satuan]);
 if((int)$q->fetchColumn()===0){flash('Satuan belum tersedia di database.','error');go('barang-form.php');}
 if($id===null){
  $q=$pdo->prepare('INSERT INTO barang (jenis,nama,idsatuan,status,harga) VALUES (:jenis,:nama,:satuan,:status,:harga)');
  $q->execute(['jenis'=>$jenis,'nama'=>$nama,'satuan'=>$satuan,'status'=>(int)$status,'harga'=>(int)$hargaRaw]);
  flash('Barang berhasil ditambahkan ke database.');
 }else{
  $q=$pdo->prepare('UPDATE barang SET jenis=:jenis,nama=:nama,idsatuan=:satuan,status=:status,harga=:harga WHERE idbarang=:id');
  $q->execute(['jenis'=>$jenis,'nama'=>$nama,'satuan'=>$satuan,'status'=>(int)$status,'harga'=>(int)$hargaRaw,'id'=>$id]);
  flash('Perubahan barang berhasil disimpan.');
 }
}catch(PDOException $ex){flash('Data gagal disimpan. Periksa aturan tabel dan koneksi database.','error');}
go('barang.php');
