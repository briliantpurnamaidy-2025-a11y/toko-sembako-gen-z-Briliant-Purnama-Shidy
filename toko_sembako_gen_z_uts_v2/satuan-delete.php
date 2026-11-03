<?php
require_once __DIR__ . '/includes/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
verify_csrf();
$id = positive_id($_POST['id'] ?? null);
if ($id === null) {
    flash('ID satuan tidak valid.', 'error');
    go('satuan.php');
}
try {
    $pdo = db();
    $pdo->beginTransaction();
    // Satuan yang dipakai produk tidak boleh dihapus.
    $ref = $pdo->prepare('SELECT COUNT(*) FROM barang WHERE idsatuan = :id');
    $ref->execute(['id' => $id]);
    if ((int) $ref->fetchColumn() > 0) {
        $pdo->rollBack();
        flash('Satuan masih digunakan oleh barang. Data tidak dihapus.', 'error');
        go('satuan.php');
    }
    $delete = $pdo->prepare('DELETE FROM satuan WHERE idsatuan = :id');
    $delete->execute(['id' => $id]);
    $deleted = $delete->rowCount();
    $pdo->commit();
    flash($deleted ? 'Data satuan berhasil dihapus.' : 'Data satuan tidak ditemukan.', $deleted ? 'success' : 'error');
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('Penghapusan gagal karena data terkait atau aturan database.', 'error');
}
go('satuan.php');
