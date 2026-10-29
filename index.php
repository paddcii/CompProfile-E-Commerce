<?php
/**
 * HOMEPAGE — Warung Nasi Tenda Biru (SATU FILE)
 * Berisi: data, kartu menu, header/navbar, keranjang, seluruh isi halaman, dan footer.
 * Yang dipakai bersama halaman lain hanya: includes/functions.php (database), assets/css/style.css, assets/js/app.js.
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Rumah Makan Rumahan Sejak 1998'; $active = 'home'; $bodyClass = '';

$fav   = db()->query('SELECT * FROM menu WHERE is_favorite=1 AND is_available=1 ORDER BY sort_order LIMIT 8')->fetchAll();
$total = (int)db()->query('SELECT COUNT(*) FROM menu')->fetchColumn();
$years = (int)date('Y') - FOUNDED_YEAR;
$heroImg  = local_or('assets/images/hero/hero.jpg', 'https://loremflickr.com/900/900/indonesian,food?lock=7');
$aboutImg = local_or('assets/images/hero/about.jpg', 'https://loremflickr.com/800/1000/warung,cooking?lock=21');
$t1 = local_or('assets/images/gallery/tile-1.jpg', 'https://loremflickr.com/900/900/grilled,chicken?lock=31');
$t2 = local_or('assets/images/gallery/tile-2.jpg', 'https://loremflickr.com/700/500/vegetables,curry?lock=32');
$t3 = local_or('assets/images/gallery/tile-4.jpg', 'https://loremflickr.com/700/500/iced,tea?lock=33');

/* Kartu menu. $m = baris tabel menu; $hl = kartu disorot (biru penuh). */
function menu_card(array $m, bool $hl = false): void {
  $img = menu_img($m); ?>
  <article class="dish <?= $hl ? 'dish-hl' : '' ?> <?= $m['is_available'] ? '' : 'dish-off' ?>" data-cat="<?= e($m['category']) ?>" data-name="<?= e(strtolower($m['name'])) ?>">
    <div class="dish-photo"><img src="<?= e($img) ?>" alt="<?= e($m['name']) ?>" loading="lazy" onerror="this.onerror=null;this.src=window.WTB.placeholder"></div>
    <div class="dish-body">
      <div class="dish-cat"><?= e($m['category']) ?></div>
      <h3 class="dish-name"><?= e($m['name']) ?></h3>
      <p class="dish-desc"><?= e($m['description']) ?></p>
      <div class="dish-foot">
        <span class="dish-price"><?= rupiah($m['price']) ?></span>
        <?php if ($m['is_available']): ?>
          <button class="btn-add js-add" type="button" aria-label="Tambah <?= e($m['name']) ?> ke keranjang"
            data-id="<?= $m['id'] ?>" data-name="<?= e($m['name']) ?>" data-price="<?= $m['price'] ?>" data-img="<?= e($img) ?>"><i class="bi bi-plus-lg"></i></button>
        <?php else: ?><span class="badge text-bg-secondary">Habis</span><?php endif; ?>
      </div>
    </div>
  </article>
<?php }

$err = "this.onerror=null;this.src=window.WTB.placeholder";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | <?= SITE_NAME ?></title>
  <meta name="description" content="Warung Nasi Tenda Biru — rumah makan mini dengan lauk rumahan dimasak segar setiap hari sejak 1998. Pesan online, bayar QRIS atau di kasir.">
  <link rel="icon" href="<?= url('assets/images/favicon.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css') ?>?v=1" rel="stylesheet">
  <script>window.WTB = { base: "<?= BASE_URL ?>", placeholder: "<?= url('assets/images/placeholder.svg') ?>" };</script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">

<header class="site-header">
  <nav class="navbar navbar-expand-lg nav-pill">
    <div class="container-fluid px-3 px-lg-4">
      <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
        <img src="<?= url('assets/images/logo.png') ?>" alt="Logo Warung Nasi Tenda Biru" class="brand-logo">
        <span class="brand-text">Warung Nasi<br><b>Tenda Biru</b></span>
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Buka menu"><i class="bi bi-list fs-2"></i></button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto gap-lg-2 py-3 py-lg-0">
          <li class="nav-item"><a class="nav-link <?= $active==='home'?'active':'' ?>" href="<?= url('index.php') ?>">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('index.php#tentang') ?>">Tentang</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='menu'?'active':'' ?>" href="<?= url('pages/menu.php') ?>">Menu</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('index.php#kontak') ?>">Kontak</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='status'?'active':'' ?>" href="<?= url('pages/cek-status.php') ?>">Cek Pesanan</a></li>
        </ul>
        <button class="btn btn-cart" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-label="Buka keranjang">
          <i class="bi bi-bag-heart-fill"></i><span class="d-lg-none ms-2">Keranjang</span>
          <span class="cart-badge" id="cartBadge" hidden>0</span>
        </button>
      </div>
    </div>
  </nav>
</header>

<!-- Keranjang belanja (JavaScript, tersimpan di browser) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer" aria-labelledby="cartTitle">
  <div class="offcanvas-header">
    <h2 class="offcanvas-title h5 fw-bold" id="cartTitle">Keranjang Anda</h2>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column p-0">
    <div id="cartItems" class="flex-grow-1 overflow-auto px-3"></div>
    <div class="cart-foot p-3">
      <div class="d-flex justify-content-between align-items-center mb-3"><span class="text-muted">Total</span><strong class="fs-4" id="cartTotal">Rp0</strong></div>
      <a href="<?= url('pages/checkout.php') ?>" class="btn btn-blue w-100 mb-2" id="cartCheckout">Lanjut ke Pembayaran</a>
      <button class="btn btn-link text-muted w-100 btn-sm" id="cartClear" type="button">Kosongkan keranjang</button>
    </div>
  </div>
</div>

<main>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 hero-in">
        <h1 class="display mb-3">Masakan rumahan yang bikin makan terasa seperti pulang.</h1>
        <p class="lead mb-4">Sejak <?= FOUNDED_YEAR ?>, dari masakan sederhana hingga rumah makan mini dengan puluhan lauk yang dimasak segar setiap hari.</p>
        <div class="d-flex flex-wrap gap-2 mb-4">
          <a href="<?= url('pages/menu.php') ?>" class="btn btn-blue btn-lg">Pesan Sekarang</a>
          <a href="#tentang" class="btn btn-ghost btn-lg">Baca Kisah Kami</a>
        </div>
        <div class="row g-2">
          <div class="col-sm-4"><div class="svc"><i class="bi bi-phone"></i><div><b>Pesan online</b><small>Tanpa antre</small></div></div></div>
          <div class="col-sm-4"><div class="svc"><i class="bi bi-shop"></i><div><b>Makan di tempat</b><small>Hangat &amp; segar</small></div></div></div>
          <div class="col-sm-4"><div class="svc"><i class="bi bi-bag-check"></i><div><b>Bawa pulang</b><small>Dibungkus rapi</small></div></div></div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-panel">
          <img class="plate" src="<?= e($heroImg) ?>" alt="Hidangan Warung Nasi Tenda Biru" onerror="<?= $err ?>">
          <div class="hero-chip c2"><i class="bi bi-calendar-heart"></i><div><b>Sejak <?= FOUNDED_YEAR ?></b>dapur yang sama, rasa yang dijaga</div></div>
          <div class="hero-chip c1"><i class="bi bi-qr-code-scan"></i><div><b>Bayar QRIS / Kasir</b>pilih yang paling nyaman</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SOROTAN -->
<section class="pt-0">
  <div class="container">
    <div class="tiles">
      <a class="tile big" href="<?= url('pages/menu.php') ?>"><img src="<?= e($t1) ?>" alt="" loading="lazy" onerror="<?= $err ?>"><div class="cap"><h3>Lauk best seller kami</h3><span class="go">Lihat lauk utama &rsaquo;</span></div></a>
      <a class="tile" href="<?= url('pages/menu.php') ?>"><img src="<?= e($t2) ?>" alt="" loading="lazy" onerror="<?= $err ?>"><div class="cap"><h3>Sayur &amp; pendamping</h3><span class="go">Lihat menu &rsaquo;</span></div></a>
      <a class="tile" href="<?= url('pages/menu.php') ?>"><img src="<?= e($t3) ?>" alt="" loading="lazy" onerror="<?= $err ?>"><div class="cap"><h3>Minuman penyegar</h3><span class="go">Lihat menu &rsaquo;</span></div></a>
    </div>
  </div>
</section>

<!-- MENU FAVORIT -->
<section class="pt-0">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
      <div><h2 class="section-title mb-1">Menu <span class="accent">Favorit</span> Pelanggan</h2><p class="lead-sm mb-0">Pilihan yang paling sering dipesan. Klik tanda + untuk masuk keranjang.</p></div>
      <a href="<?= url('pages/menu.php') ?>" class="btn btn-ghost">Lihat semua <?= $total ?> menu</a>
    </div>
    <div class="dish-grid">
      <?php foreach ($fav as $i => $m) menu_card($m, $i === 0); ?>
    </div>
  </div>
</section>

<!-- TENTANG -->
<section class="about" id="tentang">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <div class="about-visual mx-auto">
          <div class="shape"><img class="photo" src="<?= e($aboutImg) ?>" alt="Dapur Warung Nasi Tenda Biru" loading="lazy" onerror="<?= $err ?>"></div>
          <div class="float"><b><?= $years ?> tahun</b><small class="text-muted">menemani meja makan</small></div>
        </div>
      </div>
      <div class="col-lg-7">
        <h2 class="section-title mb-3">Awalnya cuma iseng, sekarang jadi tempat langganan.</h2>
        <p>Tahun <?= FOUNDED_YEAR ?>, di bawah sebuah tenda biru sederhana, kami hanya iseng menjual lauk matang. Tidak ada rencana besar, hanya masakan rumah yang dimasak dengan sabar dan dijual dengan senyum.</p>
        <p>Rupanya banyak yang kembali. Satu dua pelanggan jadi langganan, langganan mengajak keluarga dan tetangga, dan lauk yang tadinya hanya beberapa macam terus bertambah. Dari lapak kecil itu, tumbuhlah sebuah rumah makan mini dengan pilihan lauk yang kini beragam, dari ayam, ikan, dan rendang sampai sayur dan sambal buatan sendiri.</p>
        <p class="mb-4">Tenda biru itu masih jadi identitas kami. Bukan sekadar nama, tapi pengingat bahwa masakan enak selalu dimulai dari hal sederhana dan dikerjakan dengan jujur.</p>
        <ul class="tl mb-4">
          <li><b><?= FOUNDED_YEAR ?> — Lapak lauk matang.</b><br><span class="text-muted">Dimulai iseng-iseng di bawah tenda biru.</span></li>
          <li><b>Lauk makin beragam.</b><br><span class="text-muted">Pelanggan bertambah, dapur ikut berkembang.</span></li>
          <li><b>Kini — restoran mini &amp; pesan online.</b><br><span class="text-muted">Bisa makan di tempat, dibawa pulang, atau dipesan dari layar ponsel.</span></li>
        </ul>
        <div class="row g-2">
          <div class="col-4"><div class="stat"><b><?= $years ?>+</b><span>tahun berjalan</span></div></div>
          <div class="col-4"><div class="stat"><b><?= $total ?></b><span>pilihan menu</span></div></div>
          <div class="col-4"><div class="stat"><b>2</b><span>cara bayar</span></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- KENAPA -->
<section>
  <div class="container">
    <h2 class="section-title mb-4">Kenapa orang kembali ke <span class="accent">Tenda Biru</span></h2>
    <div class="row g-3">
      <div class="col-md-6 col-lg-3"><div class="value"><i class="bi bi-fire"></i><h3>Dimasak segar</h3><p class="text-muted mb-0">Lauk dimasak setiap hari, bukan dipanaskan berulang-ulang.</p></div></div>
      <div class="col-md-6 col-lg-3"><div class="value"><i class="bi bi-grid-3x3-gap"></i><h3>Pilihan lengkap</h3><p class="text-muted mb-0">Ayam, bebek, ikan, daging, sayur, hingga minuman dingin.</p></div></div>
      <div class="col-md-6 col-lg-3"><div class="value"><i class="bi bi-phone-vibrate"></i><h3>Pesan tanpa antre</h3><p class="text-muted mb-0">Pilih dari ponsel, dapur langsung mendapat notifikasi.</p></div></div>
      <div class="col-md-6 col-lg-3"><div class="value"><i class="bi bi-wallet2"></i><h3>Bayar fleksibel</h3><p class="text-muted mb-0">Scan QRIS dari mana saja, atau bayar langsung di kasir.</p></div></div>
    </div>
  </div>
</section>

<!-- CARA PESAN -->
<section class="pt-0">
  <div class="container">
    <h2 class="section-title mb-4">Cara pesan <span class="accent">online</span></h2>
    <div class="steps">
      <div class="step"><b>Pilih menu</b><span class="text-muted small">Tambahkan makanan &amp; minuman ke keranjang.</span></div>
      <div class="step"><b>Isi data pesanan</b><span class="text-muted small">Nama, makan di tempat atau bawa pulang.</span></div>
      <div class="step"><b>Bayar</b><span class="text-muted small">Scan QRIS otomatis, atau ambil nomor antrean untuk bayar di kasir.</span></div>
      <div class="step"><b>Pantau status</b><span class="text-muted small">Cek di halaman "Cek Pesanan" sampai siap.</span></div>
    </div>
  </div>
</section>

<!-- KONTAK -->
<section class="pt-0" id="kontak">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-5">
        <div class="contact-card">
          <h2 class="section-title mb-4" style="font-size:2rem">Kunjungi kami</h2>
          <ul class="list-unstyled mb-4">
            <li><i class="bi bi-geo-alt-fill"></i><div><b>Alamat</b><br><?= e(setting('address')) ?></div></li>
            <li><i class="bi bi-clock-fill"></i><div><b>Jam buka</b><br><?= e(setting('hours')) ?></div></li>
            <li><i class="bi bi-telephone-fill"></i><div><b>Telepon / WhatsApp</b><br><?= e(setting('phone')) ?></div></li>
            <li><i class="bi bi-instagram"></i><div><b>Instagram</b><br><?= e(setting('instagram')) ?></div></li>
          </ul>
          <a class="btn btn-blue" href="https://wa.me/<?= e(setting('whatsapp')) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-1"></i> Chat WhatsApp</a>
        </div>
      </div>
      <div class="col-lg-7"><iframe class="map-frame" title="Lokasi" loading="lazy" src="https://maps.google.com/maps?q=<?= urlencode(setting('address')) ?>&output=embed"></iframe></div>
    </div>
  </div>
</section>

<section class="pt-0">
  <div class="container">
    <div class="cta-band d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
      <div style="position:relative;z-index:1"><h2 class="mb-1">Lapar? Pesan dari sini saja.</h2><p class="mb-0 opacity-75">Pilih menu, bayar, tinggal tunggu dipanggil.</p></div>
      <a href="<?= url('pages/menu.php') ?>" class="btn btn-ghost btn-lg" style="position:relative;z-index:1">Buka Menu</a>
    </div>
  </div>
</section>
</main>

<footer class="site-footer">
  <div class="container">
    <div class="row g-4 align-items-start">
      <div class="col-lg-5">
        <div class="d-flex align-items-center gap-2 mb-3">
          <img src="<?= url('assets/images/logo.png') ?>" alt="" class="brand-logo"><strong class="fs-5">Warung Nasi Tenda Biru</strong>
        </div>
        <p class="mb-0 opacity-75">Masakan rumahan yang dimasak segar setiap hari. Sejak <?= FOUNDED_YEAR ?>, dari tenda kecil untuk perut dan cerita banyak orang.</p>
      </div>
      <div class="col-6 col-lg-3">
        <h3 class="h6 fw-bold mb-3">Jelajahi</h3>
        <ul class="list-unstyled small opacity-75 mb-0 d-grid gap-2">
          <li><a href="<?= url('pages/menu.php') ?>">Menu &amp; Pesan Online</a></li>
          <li><a href="<?= url('pages/cek-status.php') ?>">Cek Status Pesanan</a></li>
          <li><a href="<?= url('index.php#tentang') ?>">Kisah Kami</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-4">
        <h3 class="h6 fw-bold mb-3">Hubungi Kami</h3>
        <ul class="list-unstyled small opacity-75 mb-0 d-grid gap-2">
          <li><i class="bi bi-telephone me-2"></i><?= e(setting('phone')) ?></li>
          <li><i class="bi bi-clock me-2"></i><?= e(setting('hours')) ?></li>
          <li><i class="bi bi-instagram me-2"></i><?= e(setting('instagram')) ?></li>
        </ul>
      </div>
    </div>
    <hr class="opacity-25 my-4">
    <div class="d-flex flex-column flex-md-row justify-content-between small opacity-75 gap-2">
      <span>&copy; <?= date('Y') ?> Warung Nasi Tenda Biru. Semua hak dilindungi.</span>
      <a href="<?= url('admin/login.php') ?>">Masuk admin</a>
    </div>
  </div>
</footer>

<div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index:1100">
  <div id="appToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite">
    <div class="d-flex"><div class="toast-body" id="appToastBody"></div></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/app.js') ?>?v=1"></script>
</body>
</html>
