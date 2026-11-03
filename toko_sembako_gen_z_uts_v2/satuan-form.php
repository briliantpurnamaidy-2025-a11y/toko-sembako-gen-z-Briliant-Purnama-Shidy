<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';
$id = isset($_GET['id']) ? positive_id($_GET['id']) : null;
if (isset($_GET['id']) && $id === null) {
    flash('ID satuan tidak valid.', 'error');
    go('satuan.php');
}
$editing = $id !== null;
$data = ['nama_satuan' => '', 'status' => 1];
if ($editing) {
    $stmt = db()->prepare('SELECT idsatuan, nama_satuan, status FROM satuan WHERE idsatuan = :id');
    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch();
    if (!$record) {
        flash('Data satuan tidak ditemukan.', 'error');
        go('satuan.php');
    }
    $data = $record;
}
page_start($editing ? 'Edit Satuan' : 'Tambah Satuan', 'satuan');
?>
<section class="page-heading"><div><span class="eyebrow">MASTER DATA / SATUAN</span><h1><?= $editing ? 'Edit Satuan' : 'Tambah Satuan' ?></h1><p><?= $editing ? 'Perbarui informasi satuan yang sudah terdaftar.' : 'Lengkapi form berikut untuk mendaftarkan satuan baru.' ?></p></div><a href="satuan.php" class="btn btn-light"><?= icon_svg('back', 17) ?> Kembali</a></section>
<div class="form-layout"><section class="card form-card"><div class="section-top"><div><h2>Informasi Satuan</h2><p>Kolom bertanda <span class="required">*</span> wajib diisi.</p></div><span class="muted-tag"><?= $editing ? 'UPDATE' : 'CREATE' ?></span></div><form method="post" action="satuan-save.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $editing ? (int) $id : '' ?>"><div class="form-group"><label for="nama_satuan">Nama Satuan <span class="required">*</span></label><input type="text" id="nama_satuan" name="nama_satuan" maxlength="45" placeholder="Contoh: Dus" required autofocus value="<?= e($data['nama_satuan']) ?>"><small>Masukkan nama satuan dengan maksimal 45 karakter.</small></div><div class="form-group"><label for="status">Status Satuan <span class="required">*</span></label><select name="status" id="status" required><option value="1" <?= (int) $data['status'] === 1 ? 'selected' : '' ?>>Aktif</option><option value="0" <?= (int) $data['status'] === 0 ? 'selected' : '' ?>>Nonaktif</option></select><small>Status digunakan untuk menandai satuan yang tersedia.</small></div><div class="form-actions"><a class="btn btn-light" href="satuan.php">Batal</a><button class="btn btn-primary" type="submit"><?= icon_svg('check', 18) ?> <?= $editing ? 'Simpan Perubahan' : 'Simpan Satuan' ?></button></div></form></section><aside class="tip-panel"><div class="tip-icon"><?= icon_svg('box', 27) ?></div><h3>Tips Data Master</h3><p>Satuan dipakai untuk menjelaskan ukuran produk sembako, misalnya Kilogram, Liter, Pcs, atau Pack.</p><div class="tip-divider"></div><strong>Terhubung ke tabel</strong><span class="relation-chip">barang.idsatuan → satuan.idsatuan</span></aside></div>
<?php page_end(); ?>
