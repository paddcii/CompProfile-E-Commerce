<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$adminPage = $adminPage ?? '';
$lastId = (int)db()->query('SELECT COALESCE(MAX(id),0) FROM notifications')->fetchColumn();
?><!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — <?= SITE_NAME ?></title>
<link rel="icon" href="<?= url('assets/images/favicon.png') ?>">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= url('assets/css/style.css') ?>?v=1" rel="stylesheet">
<style>body{background:#f4f8fc}.adm-nav{background:var(--navy)}.adm-nav .nav-link{color:#cfe3f7!important}.adm-nav .nav-link.active{background:var(--blue);color:#fff!important}
.ord{background:#fff;border:1px solid var(--line);border-radius:16px;padding:18px;margin-bottom:14px}.ord.is-new{border-color:var(--blue);box-shadow:0 0 0 3px rgba(11,99,206,.1)}
.qn{font-family:var(--f-display);font-weight:800;font-size:1.8rem;color:var(--blue);line-height:1}</style>
<script>window.WTB={base:"<?= BASE_URL ?>",placeholder:"<?= url('assets/images/placeholder.svg') ?>",lastNotif:<?= $lastId ?>};</script>
</head><body>
<nav class="navbar navbar-expand adm-nav px-3">
  <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="<?= url('admin/') ?>"><img src="<?= url('assets/images/logo.png') ?>" class="brand-logo" style="width:36px;height:36px" alt=""><span class="fw-bold">Admin</span></a>
  <ul class="navbar-nav me-auto ms-3 gap-1">
    <li class="nav-item"><a class="nav-link rounded-pill px-3 <?= $adminPage==='orders'?'active':'' ?>" href="<?= url('admin/index.php') ?>">Pesanan <span class="badge bg-warning text-dark" id="activeBadge" hidden></span></a></li>
    <li class="nav-item"><a class="nav-link rounded-pill px-3 <?= $adminPage==='menu'?'active':'' ?>" href="<?= url('admin/menu.php') ?>">Menu</a></li>
    <li class="nav-item"><a class="nav-link rounded-pill px-3 <?= $adminPage==='settings'?'active':'' ?>" href="<?= url('admin/settings.php') ?>">Pengaturan</a></li>
  </ul>
  <button class="btn btn-sm btn-outline-light me-2" id="soundBtn" type="button"><i class="bi bi-bell"></i> Aktifkan suara</button>
  <a class="btn btn-sm btn-light" href="<?= url('admin/logout.php') ?>">Keluar</a>
</nav>
<div class="container-fluid px-3 px-lg-4 py-4" style="max-width:1200px">
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100" id="notifBox"></div>
