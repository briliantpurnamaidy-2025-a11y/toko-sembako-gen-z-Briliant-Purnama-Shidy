<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/demo_ui.php';
$pdo=null;$error=null;$ok=null;$barangs=[];$vendors=[];$users=[];$kartu=[];
try {
    $pdo=db();
    $barangs=$pdo->query('SELECT idbarang, nama_barang, harga FROM v_master_barang ORDER BY nama_barang')->fetchAll();
    $vendors=$pdo->query('SELECT idvendor, nama_vendor FROM v_master_vendor ORDER BY nama_vendor')->fetchAll();
    $users=$pdo->query('SELECT iduser, username FROM v_master_user ORDER BY iduser')->fetchAll();
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        // Demo hanya untuk localhost, bukan formulir yang boleh dipublikasikan.
        $user=filter_input(INPUT_POST,'iduser',FILTER_VALIDATE_INT);
        $vendor=filter_input(INPUT_POST,'idvendor',FILTER_VALIDATE_INT);
        $barang=filter_input(INPUT_POST,'idbarang',FILTER_VALIDATE_INT);
        $jumlah=filter_input(INPUT_POST,'jumlah',FILTER_VALIDATE_INT);
        $harga=filter_input(INPUT_POST,'harga_satuan',FILTER_VALIDATE_INT);
        if (!$user || !$vendor || !$barang || !$jumlah || $jumlah<1 || $jumlah>100000 || $harga===false || $harga===null || $harga<0 || $harga>100000000) {
            throw new RuntimeException('Input tidak valid. Pilih user/vendor/barang serta jumlah positif dan harga minimal 0.');
        }
        // Validasi ada dalam View, termasuk mencegah ID buatan pengguna.
        foreach ([[$users,'iduser',$user],[$vendors,'idvendor',$vendor],[$barangs,'idbarang',$barang]] as [$dataset,$key,$id]) {
            if (!in_array($id,array_map(static fn($r)=>(int)$r[$key],$dataset),true)) {
                throw new RuntimeException('Pilihan pengguna, vendor, atau barang tidak terdaftar dalam View.');
            }
        }
        $pdo->beginTransaction();
        try {
            $stmt=$pdo->prepare('INSERT INTO pengadaan (user_iduser, vendor_idvendor) VALUES (?,?)');
            $stmt->execute([$user,$vendor]);
            $idPengadaan=(int)$pdo->lastInsertId();
            $stmt=$pdo->prepare('INSERT INTO penerimaan (idpengadaan,iduser) VALUES (?,?)');
            $stmt->execute([$idPengadaan,$user]);
            $idPenerimaan=(int)$pdo->lastInsertId();
            $stmt=$pdo->prepare('INSERT INTO detail_penerimaan (idpenerimaan,barang_idbarang,jumlah_terima,harga_satuan_terima,sub_total_terima) VALUES (?,?,?,?,?)');
            $stmt->execute([$idPenerimaan,$barang,$jumlah,$harga,$jumlah*$harga]);
            // Trigger berjalan secara otomatis saat INSERT detail_penerimaan.
            // Bukti wajib dibaca dari SQL View, bukan INSERT manual kartu_stok.
            $stmt=$pdo->prepare("SELECT nama_barang, masuk, keluar, stock, idtransaksi, created_at FROM v_kartu_stok WHERE idtransaksi=? AND idbarang=? AND jenis_transaksi='M' ORDER BY idkartu_stok DESC LIMIT 1");
            $stmt->execute([$idPenerimaan,$barang]);
            $kartu=$stmt->fetch() ?: [];
            if (!$kartu) {throw new RuntimeException('Trigger belum mencatat kartu stok; transaksi dibatalkan. Jalankan SQL 05 terlebih dahulu.');}
            $pdo->commit();
            $ok='Penerimaan #'.$idPenerimaan.' berhasil dibuat. Trigger otomatis mencatat barang masuk ke kartu stok.';
        } catch(Throwable $t) { if ($pdo->inTransaction()) $pdo->rollBack();throw $t; }
    }
} catch(Throwable $e) { $error=$e instanceof RuntimeException?$e->getMessage():'Gagal menyimpan transaksi. Periksa file SQL 02 & 05, foreign key, serta log PHP.';error_log($e->getMessage()); }
mulaiHalaman('Trigger Kartu Stok'); ?>
<?php if($error): ?><div class="err"><?= h($error) ?></div><?php endif; ?>
<?php if($ok): ?><div class="ok"><?= h($ok) ?></div><?php endif; ?>
<div class="demo-card"><h2>Form Uji Penerimaan Barang</h2><p>Form ini membuat pengadaan, penerimaan, dan detail penerimaan dengan transaksi MySQL. Saat <code>detail_penerimaan</code> ditambah, Trigger mencatat kartu stok secara otomatis.</p>
<p><strong>Perhatian:</strong> Tombol simpan akan menambah data permanen pada database lokal. Untuk percobaan yang bisa dibatalkan, gunakan skrip <code>07_uji_trigger_dan_rollback.sql</code> melalui HeidiSQL. Hanya jalankan sekali saat demonstrasi yang diperlukan.</p>
<form class="demo-form" method="post">
<div><label for="iduser">Petugas</label><select id="iduser" name="iduser" required><option value="">-- Pilih --</option><?php foreach($users as $u): ?><option value="<?= (int)$u['iduser'] ?>"><?= h($u['username']) ?></option><?php endforeach; ?></select></div>
<div><label for="idvendor">Vendor</label><select id="idvendor" name="idvendor" required><option value="">-- Pilih --</option><?php foreach($vendors as $v): ?><option value="<?= (int)$v['idvendor'] ?>"><?= h($v['nama_vendor']) ?></option><?php endforeach; ?></select></div>
<div><label for="idbarang">Barang</label><select id="idbarang" name="idbarang" required><option value="">-- Pilih --</option><?php foreach($barangs as $b): ?><option value="<?= (int)$b['idbarang'] ?>"><?= h($b['nama_barang']) ?></option><?php endforeach; ?></select></div>
<div><label for="jumlah">Jumlah barang masuk</label><input id="jumlah" name="jumlah" type="number" min="1" max="100000" value="10" required></div>
<div><label for="harga_satuan">Harga satuan penerimaan</label><input id="harga_satuan" name="harga_satuan" type="number" min="0" max="100000000" value="18000" required></div>
<div style="align-self:end"><button type="submit">Simpan & Uji Trigger</button></div></form></div>
<?php if($kartu): ?><div class="demo-card"><h2>Bukti Hasil Trigger (SELECT dari v_kartu_stok)</h2><div class="table-wrap"><table><thead><tr><th>Barang</th><th>Masuk</th><th>Keluar</th><th>Saldo</th><th>ID Penerimaan</th></tr></thead><tbody><tr><td><?= h($kartu['nama_barang']) ?></td><td><?= h($kartu['masuk']) ?></td><td><?= h($kartu['keluar']) ?></td><td><?= h($kartu['stock']) ?></td><td><?= h($kartu['idtransaksi']) ?></td></tr></tbody></table></div></div><?php endif; ?>
<div class="demo-card"><h2>Hubungan SQL dengan Interface</h2><pre class="code">INSERT INTO detail_penerimaan (...) VALUES (...);
-- Trigger: trg_kartu_stok_masuk (AFTER INSERT)
SELECT * FROM v_kartu_stok;</pre><p>Ini integrasi sesungguhnya: tindakan dari form memicu Trigger MySQL, lalu hasilnya ditampilkan melalui View.</p></div>
<?php akhirHalaman(); ?>
