<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? SITE_NAME;
$active = $active ?? '';
?><!DOCTYPE html>
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
