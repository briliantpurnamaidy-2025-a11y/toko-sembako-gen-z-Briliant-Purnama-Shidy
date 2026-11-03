<?php
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/store_layout.php';
$pdo=db();
$q=trim((string)($_GET['q']??''));
$satuan=isset($_GET['satuan'])?positive_id($_GET['satuan']):null;
$sql='SELECT b.idbarang,b.nama,b.harga,s.nama_satuan,b.status FROM barang b INNER JOIN satuan s ON s.idsatuan=b.idsatuan WHERE b.status=1';
$params=[];
if($q!==''){$sql.=' AND b.nama LIKE :q';$params['q']='%'.$q.'%';}
if($satuan!==null){$sql.=' AND b.idsatuan=:sid';$params['sid']=$satuan;}
$sql.=' ORDER BY b.nama ASC';
$stmt=$pdo->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
$opts=$pdo->query('SELECT idsatuan,nama_satuan FROM satuan WHERE status=1 ORDER BY nama_satuan')->fetchAll();
store_start('Daftar Barang & Harga','catalog');
?>
<div class="catalog-banner"><span class="eyebrow">TOKO SEMBAKO GEN Z / KATALOG</span><h1>Daftar barang & harga</h1><p>Cari nama produk dan lihat harga yang tersimpan di database toko.</p></div>
<div class="catalog-area"><form class="catalog-toolbar" method="get"><label class="searchbox"><span>⌕</span><input name="q" value="<?= e($q) ?>" type="search" placeholder="Cari beras, gula, minyak..." maxlength="45"></label><select name="satuan" aria-label="Filter satuan"><option value="">Semua Satuan</option><?php foreach($opts as $opt):?><option value="<?= (int)$opt['idsatuan'] ?>" <?= $satuan===(int)$opt['idsatuan']?'selected':'' ?>><?= e($opt['nama_satuan']) ?></option><?php endforeach;?></select><button type="submit" class="store-btn store-primary">Cari Barang →</button><a class="reset-link" href="katalog.php">Reset</a></form>
<div class="catalog-table-head"><div><b>Produk yang tercatat</b><span><?= count($rows) ?> barang ditemukan</span></div><div>Harga sesuai <code>barang.harga</code></div></div><div class="catalog-scroll"><table class="catalog-table"><thead><tr><th>KODE</th><th>NAMA BARANG</th><th>SATUAN</th><th>HARGA TERCATAT</th><th>STATUS KATALOG</th></tr></thead><tbody><?php foreach($rows as $row):?><tr><td>#<?= (int)$row['idbarang'] ?></td><td class="catalog-name"><span><?= product_emoji($row['nama']) ?></span><b><?= e($row['nama']) ?></b></td><td><?= e($row['nama_satuan']) ?></td><td class="catalog-price"><?= rupiah($row['harga']) ?></td><td><span class="pill pill-active">Aktif</span></td></tr><?php endforeach;?><?php if(!$rows):?><tr><td colspan="5" class="empty">Produk tidak ditemukan. Coba kata kunci lain.</td></tr><?php endif;?></tbody></table></div></div>
<div class="store-disclaimer">ℹ Katalog hanya menampilkan produk berstatus aktif. Angka harga berasal dari <code>barang.harga</code>; margin penjualan belum dihitung otomatis pada tahap ini. Tersedia di katalog bukan jaminan stok fisik.</div>
<?php store_end(); ?>
