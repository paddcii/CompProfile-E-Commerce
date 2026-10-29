<?php
require_once __DIR__ . '/../includes/functions.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $st = db()->prepare('SELECT * FROM admins WHERE username=?'); $st->execute([trim($_POST['username'] ?? '')]);
  $a = $st->fetch();
  if ($a && password_verify($_POST['password'] ?? '', $a['password_hash'])) {
    session_regenerate_id(true); $_SESSION['admin_id'] = $a['id']; header('Location: ' . url('admin/index.php')); exit;
  }
  $err = 'Username atau password salah.'; usleep(400000);
}
?><!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Masuk Admin</title>
<link rel="icon" href="<?= url('assets/images/favicon.png') ?>">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= url('assets/css/style.css') ?>" rel="stylesheet"></head>
<body style="background:var(--sky)" class="d-grid align-items-center min-vh-100 px-3">
<form method="post" class="card-x mx-auto w-100" style="max-width:400px">
  <div class="text-center mb-4"><img src="<?= url('assets/images/logo.png') ?>" alt="" style="width:84px;border-radius:20px"><h1 class="h4 fw-bold mt-3 mb-0">Masuk Admin</h1><small class="text-muted">Warung Nasi Tenda Biru</small></div>
  <?php if ($err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <label class="form-label fw-semibold" for="u">Username</label><input id="u" name="username" class="form-control mb-3" required autofocus autocomplete="username">
  <label class="form-label fw-semibold" for="p">Password</label><input id="p" name="password" type="password" class="form-control mb-4" required autocomplete="current-password">
  <button class="btn btn-blue w-100">Masuk</button>
  <a href="<?= url('index.php') ?>" class="d-block text-center mt-3 small">&lsaquo; Kembali ke website</a>
</form></body></html>
