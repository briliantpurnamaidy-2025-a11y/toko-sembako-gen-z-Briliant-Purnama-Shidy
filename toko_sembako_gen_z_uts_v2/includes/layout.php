<?php
require_once __DIR__ . '/helpers.php';

function icon_svg(string $name, int $size = 19): string
{
    $paths = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>',
        'box' => '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/>',
        'truck' => '<path d="M10 17h4V5H2v12h3"/><path d="M14 9h4l4 4v4h-3"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/>',
        'chart' => '<path d="M3 3v18h18M7 16v-5M12 16V7M17 16v-9"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'edit' => '<path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L9 17l-4 1 1-4Z"/>',
        'trash' => '<path d="M3 6h18M8 6V4h8v2M6 6l1 15h10l1-15M10 10v7M14 10v7"/>',
        'arrow' => '<path d="m5 12 14 0m-6-6 6 6-6 6"/>',
        'back' => '<path d="m15 18-6-6 6-6"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/>',
    ];
    $path = $paths[$name] ?? $paths['box'];
    return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

function page_start(string $title, string $current = 'dashboard'): void
{
    $flashMessage = take_flash();
    ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> | Toko Sembako Gen Z</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <a class="brand" href="dashboard.php" aria-label="Dashboard Pengelola">
      <span class="brand-mark">G<span>Z</span></span>
      <span class="brand-copy"><strong>Sembako Gen Z</strong><small>Store Management</small></span>
    </a>
    <div class="nav-caption">MENU UTAMA</div>
    <nav class="nav-list">
      <a class="nav-item <?= $current === 'dashboard' ? 'active' : '' ?>" href="dashboard.php"><?= icon_svg('grid') ?><span>Dashboard</span></a>
      <a class="nav-item <?= $current === 'barang' ? 'active' : '' ?>" href="barang.php"><?= icon_svg('box') ?><span>Data Barang</span></a>
      <a class="nav-item <?= $current === 'satuan' ? 'active' : '' ?>" href="satuan.php"><?= icon_svg('box') ?><span>Data Satuan</span><span class="nav-dot"></span></a>
    </nav>
    <div class="nav-caption second-caption">AKSES PEMBELI</div><div class="nav-list"><a class="nav-item" href="index.php"><?= icon_svg('arrow') ?><span>Beranda Pembeli</span></a><a class="nav-item" href="katalog.php"><?= icon_svg('grid') ?><span>Daftar Harga</span></a></div><div class="nav-caption second-caption">PENGEMBANGAN UAS</div>
    <div class="nav-list">
      <div class="nav-item disabled" title="Belum diimplementasikan"><?= icon_svg('truck') ?><span>Pengadaan</span><small>soon</small></div>
      <div class="nav-item disabled" title="Belum diimplementasikan"><?= icon_svg('chart') ?><span>Penjualan</span><small>soon</small></div>
    </div>
    <div class="sidebar-foot"><span class="status-led"></span><div><strong>Mode UTS</strong><small>Demo CUD · PHP + Raw SQL</small></div></div>
  </aside>
  <div class="main-area">
    <header class="topbar"><div class="topbar-left"><span class="crumb">Manajemen Toko</span><span class="crumb-divider">/</span><span><?= e($title) ?></span></div><span class="avatar">GZ</span></header>
    <main class="content">
      <?php if ($flashMessage): ?>
        <div class="notice <?= $flashMessage[1] === 'error' ? 'notice-error' : '' ?>" role="status"><?= icon_svg($flashMessage[1] === 'error' ? 'box' : 'check') ?><?= e($flashMessage[0]) ?></div>
      <?php endif; ?>
<?php
}

function page_end(): void
{
    ?>
    </main>
    <footer class="footer">© 2026 Toko Sembako Gen Z <span>•</span> Prototipe UTS · tanpa autentikasi</footer>
  </div>
</div>
<script src="assets/app.js"></script>
</body>
</html>
<?php
}
