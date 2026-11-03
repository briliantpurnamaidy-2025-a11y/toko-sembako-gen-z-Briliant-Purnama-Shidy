<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';
$pdo = db();
$rows = $pdo->query('SELECT idsatuan, nama_satuan, status FROM satuan ORDER BY idsatuan ASC')->fetchAll();
page_start('Data Satuan', 'satuan');
?>
<section class="page-heading"><div><span class="eyebrow">MASTER DATA / SATUAN</span><h1>Data Satuan</h1><p>Kelola satuan ukuran yang digunakan pada daftar barang toko.</p></div><a href="satuan-form.php" class="btn btn-primary"><?= icon_svg('plus', 18) ?> Tambah Satuan</a></section>
<div class="card table-card"><div class="section-top"><div><h2>Daftar Satuan Barang</h2><p>Total <strong><?= count($rows) ?></strong> satuan tercatat di database.</p></div><span class="muted-tag">SQL langsung</span></div><div class="table-scroll"><table><thead><tr><th>ID</th><th>Nama Satuan</th><th>Status</th><th class="align-right">Tindakan</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?>
<tr><td class="id-cell">#<?= (int) $row['idsatuan'] ?></td><td><div class="item-name"><span class="item-symbol"><?= icon_svg('box', 17) ?></span><?= e($row['nama_satuan']) ?></div></td><td><span class="pill <?= (int) $row['status'] === 1 ? 'pill-active' : 'pill-inactive' ?>"><?= (int) $row['status'] === 1 ? 'Aktif' : 'Nonaktif' ?></span></td><td><div class="actions"><a class="btn-icon" title="Edit" aria-label="Edit <?= e($row['nama_satuan']) ?>" href="satuan-form.php?id=<?= (int) $row['idsatuan'] ?>"><?= icon_svg('edit', 17) ?></a><form action="satuan-delete.php" method="post" class="inline-form" data-confirm="Hapus satuan <?= e($row['nama_satuan']) ?>? Jika sudah dipakai oleh barang, penghapusan akan ditolak."><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $row['idsatuan'] ?>"><button type="submit" class="btn-icon danger" aria-label="Hapus <?= e($row['nama_satuan']) ?>" title="Hapus"><?= icon_svg('trash', 17) ?></button></form></div></td></tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="4" class="empty">Belum ada data satuan. Klik “Tambah Satuan” untuk memulai.</td></tr><?php endif; ?>
</tbody></table></div><div class="table-footer"><span>Menampilkan <?= count($rows) ?> data satuan</span><span class="hint-inline">Hapus hanya tersedia untuk satuan yang tidak dipakai barang.</span></div></div>
<div class="info-banner"><span class="info-mark">i</span><p><strong>Catatan UTS:</strong> penghapusan data yang memiliki relasi ke tabel <code>barang</code> ditolak untuk menjaga integritas database. Uji fitur hapus dengan membuat satuan sementara, lalu hapus satuan tersebut.</p></div>
<?php page_end(); ?>
