<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/layout.php';
function store_start(string $title, string $active='home'): void { ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title) ?> | Toko Sembako Gen Z</title><link rel="stylesheet" href="assets/styles.css"></head>
<body class="store-body"><div class="store-topnote">🛒 &nbsp; Toko Sembako Gen Z — Katalog produk & informasi harga</div>
<header class="store-header"><a class="store-logo" href="index.php"><span class="store-logo-box">G<span>Z</span></span><span><strong>SEMBAKO <em>GEN Z</em></strong><small>Belanja kebutuhan harian, lebih gampang.</small></span></a><nav class="store-nav" aria-label="Menu pembeli"><a class="<?= $active==='home'?'is-current':'' ?>" href="index.php">Beranda</a><a class="<?= $active==='catalog'?'is-current':'' ?>" href="katalog.php">Daftar Barang & Harga</a><a href="dashboard.php">Panel Pengelola ↗</a></nav><span class="visitor-chip">● Katalog publik</span></header><main class="store-container">
<?php }
function store_end():void { ?>
</main><footer class="store-footer"><div><strong>TOKO SEMBAKO GEN Z</strong><p>Sistem informasi toko berbasis web · Pengembangan awal UTS</p></div><small>Harga mengacu pada data <code>barang.harga</code>. Pemesanan & pembayaran online belum tersedia.</small></footer></body></html>
<?php }
function product_emoji(string $nama):string {
 $n=strtolower($nama);
 foreach(['beras'=>'🌾','minyak'=>'🫗','gula'=>'🧂','tepung'=>'🥣','kopi'=>'☕','teh'=>'🍵','air'=>'💧','susu'=>'🥛','mi '=>'🍜','mie'=>'🍜','tisu'=>'🧻'] as $k=>$v){if(str_contains($n,$k)) return $v;}
 return '🛍️';
}
function rupiah($nominal):string {return 'Rp'.number_format((int)$nominal,0,',','.');}
