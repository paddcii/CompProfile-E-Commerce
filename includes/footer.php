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
<?php if (!empty($extraJs)) foreach ($extraJs as $js) echo $js . "\n"; ?>
</body>
</html>
