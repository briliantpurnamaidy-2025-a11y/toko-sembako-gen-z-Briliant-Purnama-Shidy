<?php
declare(strict_types=1);
function h($x): string { return htmlspecialchars((string)($x ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function uang($x): string { return 'Rp' . number_format((float)$x, 0, ',', '.'); }
function mulaiHalaman(string $judul): void { ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= h($judul) ?> | Toko Sembako Gen Z</title><link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/responsi.css"></head>
<body><div class="app-shell"><aside class="sidebar">
<a class="brand" href="index.php"><span class="brand-mark">G<span>Z</span></span><span class="brand-copy"><strong>SEMBAKO<span>GEN Z</span></strong><small>Panel Responsi PBD</small></span></a>
<div class="sidebar-tag">INTEGRASI MYSQL / PHP NATIVE</div>
<nav class="main-nav"><span class="nav-caption">MENU</span>
<a class="nav-item" href="index.php">◫ &nbsp; Semua SQL View</a>
<a class="nav-item" href="sp.php">⌕ &nbsp; Stored Procedure</a>
<a class="nav-item" href="function.php">ƒ &nbsp; Function Harga</a>
<a class="nav-item" href="penerimaan.php">＋ &nbsp; Trigger Penerimaan</a>
<a class="nav-item" href="index.php?view=kartu_stok">▦ &nbsp; View Kartu Stok</a>
</nav><div class="sidebar-bottom">Semua operasi baca menggunakan SQL View. Operasi tulis dilakukan di tabel dasar untuk memicu Trigger.</div>
</aside><div class="app-main"><header class="topbar"><div class="crumb">Toko Sembako Gen Z / <strong><?= h($judul) ?></strong></div><div class="read-only">Lingkungan lokal — demonstrasi</div></header><main class="content">
<div class="page-header"><div><a class="breadcrumb-back" href="index.php">← Semua SQL View</a><span class="eyebrow">RESPONSI PRAKTIKUM PBD</span><h1><?= h($judul) ?><span class="accent">.</span></h1></div></div>
<?php }
function akhirHalaman(): void { ?>
<footer class="footer">TOKO SEMBAKO GEN Z · SQL langsung (tanpa Query Builder)</footer>
</main></div></div></body></html>
<?php }
