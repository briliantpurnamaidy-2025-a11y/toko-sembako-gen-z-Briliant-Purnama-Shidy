<?php
require_once __DIR__ . '/includes/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
verify_csrf();
$idRaw = trim((string) ($_POST['id'] ?? ''));
$id = $idRaw === '' ? null : positive_id($idRaw);
if ($idRaw !== '' && $id === null) {
    flash('ID satuan tidak valid.', 'error');
    go('satuan.php');
}
$name = trim((string) ($_POST['nama_satuan'] ?? ''));
$status = (string) ($_POST['status'] ?? '');
if ($name === '' || (function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name)) > 45 || !in_array($status, ['0', '1'], true)) {
    flash('Nama satuan wajib diisi (maksimal 45 karakter) dan status harus valid.', 'error');
    go($id === null ? 'satuan-form.php' : 'satuan-form.php?id=' . $id);
}
try {
    if ($id === null) {
        $stmt = db()->prepare('INSERT INTO satuan (nama_satuan, status) VALUES (:nama, :status)');
        $stmt->execute(['nama' => $name, 'status' => (int) $status]);
        flash('Satuan ' . $name . ' berhasil ditambahkan.');
    } else {
        $stmt = db()->prepare('UPDATE satuan SET nama_satuan = :nama, status = :status WHERE idsatuan = :id');
        $stmt->execute(['nama' => $name, 'status' => (int) $status, 'id' => $id]);
        flash('Data satuan berhasil diperbarui.');
    }
} catch (PDOException $e) {
    flash('Gagal menyimpan data satuan. Periksa koneksi dan aturan database.', 'error');
}
go('satuan.php');
