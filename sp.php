<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/demo_ui.php';
$error = null; $rows = []; $units = []; $chosen = null;
try {
    $pdo = db();
    $units = $pdo->query('SELECT idsatuan, nama_satuan FROM v_master_satuan ORDER BY idsatuan')->fetchAll();
    if (isset($_GET['idsatuan']) && ctype_digit((string)$_GET['idsatuan'])) {
        $chosen = (int)$_GET['idsatuan'];
        $stmt = $pdo->prepare('CALL sp_tampil_barang_per_satuan(?)');
        $stmt->execute([$chosen]);
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();
    }
} catch (Throwable $e) { $error = 'Gagal memanggil Stored Procedure. Pastikan SQL file 01 & 03 telah dijalankan. Detail ada pada PHP error log.'; error_log($e->getMessage()); }
mulaiHalaman('Stored Procedure'); ?>
<div class="demo-card"><h2>Filter Barang Berdasarkan Satuan</h2><p>Interface ini memanggil <code>CALL sp_tampil_barang_per_satuan(?)</code>. Data sumber prosedur dibaca dari SQL View <code>v_master_barang</code>.</p>
<?php if ($error): ?><div class="err"><?= h($error) ?></div><?php endif; ?>
<form class="demo-form" method="get"><div><label for="idsatuan">Pilih satuan</label><select id="idsatuan" name="idsatuan" required><option value="">-- Pilih satuan --</option><?php foreach ($units as $u): ?><option value="<?= (int)$u['idsatuan'] ?>" <?= $chosen === (int)$u['idsatuan']?'selected':'' ?>><?= h($u['nama_satuan']) ?></option><?php endforeach; ?></select></div><div style="align-self:end"><button type="submit">Jalankan SP</button></div></form>
<pre class="code">CALL sp_tampil_barang_per_satuan(<?= $chosen ?? 'ID_SATUAN' ?>);</pre></div>
<?php if ($chosen !== null && !$error): ?><div class="demo-card"><h2>Hasil Stored Procedure (<?= count($rows) ?> baris)</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama Barang</th><th>Satuan</th><th>Harga DB</th><th>Status</th></tr></thead><tbody><?php foreach ($rows as $r): ?><tr><td><?= h($r['idbarang']) ?></td><td><?= h($r['nama_barang']) ?></td><td><?= h($r['nama_satuan']) ?></td><td><?= h(uang($r['harga'])) ?></td><td><?= h($r['status']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php if (!$rows): ?><p>Belum ada barang untuk satuan ini.</p><?php endif; ?></div><?php endif; ?>
<?php akhirHalaman(); ?>
