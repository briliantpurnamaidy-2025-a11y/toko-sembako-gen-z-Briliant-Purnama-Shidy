<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/demo_ui.php';
$error=null;$barang=[];$margin=null;$chosen=null;$harga=null;$hasil=null;
try {
    $pdo=db();
    $barang=$pdo->query('SELECT idbarang, nama_barang, harga FROM v_master_barang ORDER BY nama_barang')->fetchAll();
    $margin=$pdo->query('SELECT persen FROM v_margin_aktif LIMIT 1')->fetchColumn();
    if (isset($_GET['idbarang']) && ctype_digit((string)$_GET['idbarang'])) {
        $chosen=(int)$_GET['idbarang'];
        $stmt=$pdo->prepare('SELECT idbarang, nama_barang, harga FROM v_master_barang WHERE idbarang=? LIMIT 1');
        $stmt->execute([$chosen]); $record=$stmt->fetch();
        if (!$record) {throw new RuntimeException('Barang tidak ditemukan');}
        if ($margin === false) {throw new RuntimeException('Belum ada margin aktif');}
        $harga=$record;
        $stmt=$pdo->prepare('SELECT fn_hitung_harga_jual(?, ?) AS harga_jual');
        $stmt->execute([$record['harga'], $margin]);
        $hasil=$stmt->fetchColumn();
    }
} catch (Throwable $e) { $error='Pengujian Function gagal: '.($e instanceof RuntimeException?h($e->getMessage()):'periksa file SQL 04 dan koneksi database');error_log($e->getMessage()); }
mulaiHalaman('Function Harga Jual'); ?>
<div class="demo-card"><h2>Simulasi Markup Harga</h2><p>Website memilih barang melalui View <code>v_master_barang</code>, margin aktif melalui <code>v_margin_aktif</code>, lalu memanggil Function MySQL <code>fn_hitung_harga_jual</code>.</p>
<?php if ($error): ?><div class="err"><?= h($error) ?></div><?php endif; ?>
<form method="get" class="demo-form"><div><label for="idbarang">Pilih barang</label><select id="idbarang" name="idbarang" required><option value="">-- Pilih barang --</option><?php foreach($barang as $b): ?><option value="<?= (int)$b['idbarang'] ?>" <?= (int)$b['idbarang']===$chosen?'selected':'' ?>><?= h($b['nama_barang']) ?> — <?= h(uang($b['harga'])) ?></option><?php endforeach; ?></select></div><div><label>Margin aktif dari SQL View</label><input readonly value="<?= $margin!==false ? h(number_format((float)$margin,0,',','.')).'%' : 'Tidak tersedia' ?>"></div><div class="full"><button type="submit">Hitung lewat Function</button></div></form>
<pre class="code">SELECT fn_hitung_harga_jual(harga_barang, persen_margin_aktif);</pre>
<p class="hint">Rumus yang dipakai adalah <strong>markup</strong> = harga dasar × (1 + persen/100). Kolom harga pada database diperlakukan sebagai harga dasar hanya untuk simulasi; belum ditentukan aturan bisnis harga jual final.</p></div>
<?php if($hasil!==null): ?><div class="demo-card"><h2>Hasil Perhitungan MySQL</h2><p>Barang: <strong><?= h($harga['nama_barang']) ?></strong> · Harga dasar: <?= h(uang($harga['harga'])) ?> · Markup: <?= h($margin) ?>%</p><div class="metric"><?= h(uang($hasil)) ?></div><p>Nilai ini dihitung oleh Function di database, bukan perhitungan PHP.</p></div><?php endif; ?>
<?php akhirHalaman(); ?>
