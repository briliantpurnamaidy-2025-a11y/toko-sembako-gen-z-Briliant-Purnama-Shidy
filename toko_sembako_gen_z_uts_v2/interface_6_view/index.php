<?php

declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';

function masterEsc($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function masterRupiah($amount): string
{
    return 'Rp' . number_format((float)$amount, 0, ',', '.');
}

function masterDate($value): string
{
    if (!$value) return '—';
    try {
        return (new DateTime((string)$value))->format('d M Y, H:i');
    } catch (Exception $e) {
        return (string)$value;
    }
}

$modules = [
    'role' => [
        'view' => 'v_master_role', 'title' => 'Data Role', 'desc' => 'Daftar peran dan hak akses pada aplikasi.',
        'icon' => '◈', 'columns' => [
            'idrole' => 'ID Role', 'nama_role' => 'Nama Role',
        ],
    ],
    'user' => [
        'view' => 'v_master_user', 'title' => 'Data User', 'desc' => 'Daftar akun beserta perannya, tanpa menampilkan password.',
        'icon' => '♙', 'columns' => [
            'iduser' => 'ID User', 'username' => 'Username', 'nama_role' => 'Hak Akses',
        ],
    ],
    'satuan' => [
        'view' => 'v_master_satuan', 'title' => 'Data Satuan', 'desc' => 'Satuan yang digunakan pada barang.',
        'icon' => '▤', 'columns' => [
            'idsatuan' => 'ID Satuan', 'nama_satuan' => 'Nama Satuan', 'status' => 'Status',
        ],
    ],
    'barang' => [
        'view' => 'v_master_barang', 'title' => 'Data Barang', 'desc' => 'Produk sembako, satuan, dan harga yang tersimpan di database.',
        'icon' => '▣', 'columns' => [
            'idbarang' => 'ID Barang', 'jenis' => 'Jenis', 'nama_barang' => 'Nama Barang',
            'nama_satuan' => 'Satuan', 'harga' => 'Harga (DB)', 'status' => 'Status',
        ],
    ],
    'vendor' => [
        'view' => 'v_master_vendor', 'title' => 'Data Vendor', 'desc' => 'Daftar pemasok barang toko.',
        'icon' => '⌂', 'columns' => [
            'idvendor' => 'ID Vendor', 'nama_vendor' => 'Nama Vendor', 'badan_hukum' => 'Kode Badan Hukum', 'status' => 'Status',
        ],
    ],
    'margin' => [
        'view' => 'v_margin_aktif', 'title' => 'Margin Aktif', 'desc' => 'Hanya margin yang berstatus aktif (status = 1).',
        'icon' => '%', 'columns' => [
            'idmargin_penjualan' => 'ID Margin', 'persen' => 'Persentase',
            'status' => 'Status', 'created_at' => 'Dibuat Pada',
        ],
    ],
];

$selected = $_GET['view'] ?? 'dashboard';
if (!is_string($selected) || ($selected !== 'dashboard' && !isset($modules[$selected]))) {
    http_response_code(404);
    $selected = 'dashboard';
}

$results = [];
$viewErrors = [];
try {
    $pdo = db();
    foreach ($modules as $key => $module) {
        try {
            $columns = array_keys($module['columns']);
            $projection = implode(', ', array_map(static fn($name) => '`' . $name . '`', $columns));
            $sql = 'SELECT ' . $projection . ' FROM `' . $module['view'] . '`';
            $results[$key] = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $results[$key] = [];
            $viewErrors[$key] = 'View belum dapat dibaca. Periksa keberadaan dan kolom dari ' . $module['view'] . '.';
            error_log('[Interface6View] ' . $module['view'] . ': ' . $e->getMessage());
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    $viewErrors['global'] = 'Koneksi database gagal. Periksa konfigurasi lokal dan layanan MySQL.';
    error_log('[Interface6View] DB connection: ' . $e->getMessage());
    foreach ($modules as $key => $_) $results[$key] = [];
}

$availableCount = count($modules) - count(array_intersect(array_keys($viewErrors), array_keys($modules)));
$pageTitle = $selected === 'dashboard' ? 'Dashboard Master Data' : $modules[$selected]['title'];
$activeMargins = count($results['margin'] ?? []);

function masterCell(string $column, $value): string
{
    if ($column === 'harga') {
        return '<strong class="price">' . masterEsc(masterRupiah($value)) . '</strong>';
    }
    if ($column === 'persen') {
        return '<span class="pill pill-yellow">' . masterEsc(number_format((float)$value, 0, ',', '.')) . '%</span>';
    }
    if ($column === 'created_at') {
        return masterEsc(masterDate($value));
    }
    if ($column === 'status' && (string)$value === '1') {
        return '<span class="pill pill-green"><span class="status-dot"></span>Aktif (1)</span>';
    }
    if ($column === 'status' && (string)$value === '0') {
        return '<span class="pill pill-gray"><span class="status-dot"></span>Tidak aktif (0)</span>';
    }
    return masterEsc($value);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= masterEsc($pageTitle) ?> | Toko Sembako Gen Z</title>
  <link rel="stylesheet" href="assets/style.css">
  <script defer src="assets/app.js"></script>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="index.php" aria-label="Beranda data master">
      <span class="brand-mark">G<span>Z</span></span>
      <span class="brand-copy"><strong>SEMBAKO<span>GEN Z</span></strong><small>Panel Data Master</small></span>
    </a>
    <div class="sidebar-tag">RESPONSI PEMROGRAMAN BASIS DATA</div>
    <nav class="main-nav" aria-label="Navigasi data master">
      <span class="nav-caption">MENU UTAMA</span>
      <a class="nav-item <?= $selected === 'dashboard' ? 'active' : '' ?>" href="index.php"><span class="nav-icon">◫</span> Dashboard <span class="nav-chevron">›</span></a>
      <span class="nav-caption">6 SQL VIEW</span>
      <?php foreach ($modules as $key => $module): ?>
        <a class="nav-item <?= $selected === $key ? 'active' : '' ?>" href="?view=<?= masterEsc($key) ?>">
          <span class="nav-icon"><?= masterEsc($module['icon']) ?></span>
          <?= masterEsc($module['title']) ?>
          <span class="nav-count"><?= isset($viewErrors[$key]) ? '!' : count($results[$key] ?? []) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom">
      <div class="connection"><span class="live-dot"></span><?= $availableCount ?>/6 view terbaca</div>
      <p>Data ditampilkan dengan perintah <code>SELECT</code> dari SQL View.</p>
      <a class="back-link" href="../index.php">← Kembali ke Website Toko</a>
    </div>
  </aside>

  <div class="app-main">
    <header class="topbar">
      <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka menu">☰</button>
      <div class="crumb"><span>Toko Sembako Gen Z</span><b> / </b><strong><?= masterEsc($pageTitle) ?></strong></div>
      <div class="top-right"><span class="read-only"><span class="small-dot"></span> Mode tampilan data</span><span class="user-badge">PBD</span></div>
    </header>
    <main class="content">
    <?php if (isset($viewErrors['global'])): ?>
      <div class="notice notice-error"><?= masterEsc($viewErrors['global']) ?></div>
    <?php endif; ?>

    <?php if ($selected === 'dashboard'): ?>
      <section class="intro">
        <div>
          <span class="eyebrow">✦ DATABASE VIEW / DATA MASTER</span>
          <h1>Dashboard Data Master<span class="accent">.</span></h1>
          <p>Enam tampilan data master dalam satu interface sederhana. Seluruh isi tabel diambil langsung dari <strong>SQL View MySQL</strong>.</p>
          <a class="primary-btn" href="?view=barang">Lihat daftar barang <span>↗</span></a>
        </div>
        <div class="intro-art" aria-hidden="true"><div class="art-ring"></div><div class="art-bag">▣</div><div class="art-sparkle">✳</div><span>GEN Z<br>DATA HUB</span></div>
      </section>
      <div class="section-head"><h2>Ringkasan Database</h2><p>Jumlah baris dari masing-masing View</p></div>
      <section class="stats-grid" aria-label="Jumlah data setiap view">
        <?php foreach ($modules as $key => $module): ?>
          <a class="stat-card" href="?view=<?= masterEsc($key) ?>">
            <span class="stat-icon"><?= masterEsc($module['icon']) ?></span>
            <div class="stat-value"><?= isset($viewErrors[$key]) ? '—' : count($results[$key] ?? []) ?></div>
            <div class="stat-label"><?= masterEsc($module['title']) ?></div>
            <div class="stat-view"><?= masterEsc($module['view']) ?> <span>↗</span></div>
          </a>
        <?php endforeach; ?>
      </section>
      <div class="main-grid">
        <section class="panel">
          <div class="panel-heading"><div><h2>Preview Produk Sembako</h2><p>Harga diambil dari View master barang</p></div><a class="text-link" href="?view=barang">Lihat semua →</a></div>
          <?php if (isset($viewErrors['barang'])): ?>
            <div class="notice notice-error"><?= masterEsc($viewErrors['barang']) ?></div>
          <?php elseif (!$results['barang']): ?>
            <div class="empty-state">Belum ada barang yang dapat ditampilkan.</div>
          <?php else: ?>
          <div class="table-wrap"><table class="data-table"><thead><tr><th>Nama Produk</th><th>Satuan</th><th>Harga (DB)</th></tr></thead><tbody>
            <?php foreach (array_slice($results['barang'], 0, 5) as $row): ?>
              <tr><td><span class="product-name"><?= masterEsc($row['nama_barang']) ?></span></td><td><?= masterEsc($row['nama_satuan']) ?></td><td><?= masterCell('harga', $row['harga']) ?></td></tr>
            <?php endforeach; ?>
          </tbody></table></div>
          <?php endif; ?>
        </section>
        <section class="panel status-panel">
          <div class="panel-heading"><div><h2>Status View</h2><p>Pemeriksaan data referensi</p></div></div>
          <div class="big-icon">%</div>
          <div class="margin-title">Margin penjualan aktif</div>
          <div class="margin-value">
            <?= $activeMargins === 1 ? masterEsc(number_format((float)($results['margin'][0]['persen'] ?? 0), 0, ',', '.')) . '%' : '—' ?>
          </div>
          <div class="info-chip <?= $activeMargins === 1 ? 'good' : 'warning' ?>"><?= $activeMargins === 1 ? '✓ Satu margin aktif' : '⚠ Periksa margin: ' . $activeMargins . ' baris aktif' ?></div>
          <p class="tiny">View <code>v_margin_aktif</code> menampilkan margin berstatus 1. Pilihan margin tidak aktif tidak tampil pada View ini.</p>
          <a class="text-link" href="?view=margin">Buka view margin →</a>
        </section>
      </div>
      <div class="notice notice-info"><strong>Catatan pengujian:</strong> interface ini <b>hanya membaca data</b>. Perubahan data (Create, Update, Delete) harus dilakukan melalui tabel sumber / modul CUD, bukan melalui halaman SQL View ini.</div>
    <?php else:
      $module = $modules[$selected];
      $rows = $results[$selected] ?? [];
    ?>
      <div class="page-header">
        <div><a href="index.php" class="breadcrumb-back">← Dashboard</a><span class="eyebrow">SQL VIEW / DATA MASTER</span><h1><?= masterEsc($module['title']) ?><span class="accent">.</span></h1><p><?= masterEsc($module['desc']) ?></p></div>
        <span class="view-badge">↳ <?= masterEsc($module['view']) ?></span>
      </div>
      <section class="panel full-panel">
        <div class="panel-heading list-heading"><div><h2>Daftar <?= masterEsc($module['title']) ?></h2><p>Data diperoleh dari <code><?= masterEsc($module['view']) ?></code>, bukan query tabel utama.</p></div>
          <div class="list-actions"><label class="sr-only" for="searchRows">Cari baris</label><input id="searchRows" type="search" placeholder="Cari data..." autocomplete="off"><span class="row-indicator"><b id="visibleCount"><?= count($rows) ?></b> data</span></div>
        </div>
        <?php if (isset($viewErrors[$selected])): ?>
          <div class="notice notice-error"><?= masterEsc($viewErrors[$selected]) ?><br>Jalankan <code>SHOW FULL TABLES WHERE Table_type = 'VIEW';</code> di HeidiSQL untuk memeriksa nama View.</div>
        <?php else: ?>
          <div class="table-wrap"><table class="data-table" id="masterTable"><thead><tr>
            <?php foreach ($module['columns'] as $caption): ?><th><?= masterEsc($caption) ?></th><?php endforeach; ?>
          </tr></thead><tbody>
            <?php foreach ($rows as $row): ?>
              <tr class="filterable-row">
              <?php foreach ($module['columns'] as $column => $caption): ?><td><?= masterCell($column, $row[$column] ?? null) ?></td><?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody></table></div>
          <?php if (!$rows): ?><div class="empty-state">View ini belum memiliki data.</div><?php endif; ?>
          <div id="noMatches" class="empty-state" hidden>Tidak ada data yang cocok dengan pencarian.</div>
        <?php endif; ?>
        <div class="panel-footer"><span>🔒 Read-only • Tidak mengubah isi database</span><a href="index.php">Kembali ke dashboard ↑</a></div>
      </section>
      <?php if ($selected === 'barang'): ?><div class="notice notice-info">Kolom <strong>Harga (DB)</strong> berasal dari <code>barang.harga</code>. Sistem ini belum memastikan apakah angka tersebut harga beli atau harga jual setelah margin, sehingga tidak diberi label harga jual.</div><?php endif; ?>
      <?php if ($selected === 'vendor'): ?><div class="notice notice-info">Kolom <strong>Kode Badan Hukum</strong> ditampilkan apa adanya karena aturan kode 0/1 belum didefinisikan secara resmi dalam ERD.</div><?php endif; ?>
      <?php if ($selected === 'margin'): ?><div class="notice notice-info">View <code>v_margin_aktif</code> hanya menampilkan margin dengan <code>status = 1</code>. Untuk melihat kelima opsi margin, buka tabel sumber <code>margin_penjualan</code> di HeidiSQL.</div><?php endif; ?>
    <?php endif; ?>
      <footer class="footer">TOKO SEMBAKO GEN Z <span>•</span> Interface 6 View Data Master <span>•</span> Praktikum Pemrograman Basis Data</footer>
    </main>
  </div>
</div>
</body>
</html>
