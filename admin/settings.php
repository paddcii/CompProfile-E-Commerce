<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/qris.php';
require_admin();
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (($_POST['action'] ?? '') === 'info') {
    foreach (['phone', 'whatsapp', 'address', 'hours', 'instagram'] as $k) set_setting($k, trim($_POST[$k] ?? ''));
    $q = preg_replace('/\s+/', '', $_POST['qris_static'] ?? '');
    if ($q !== '' && !preg_match('/^000201.*6304[0-9A-Fa-f]{4}$/', $q)) $err = 'Kode QRIS tidak valid (harus diawali 000201 dan diakhiri 6304 + 4 karakter).';
    else { set_setting('qris_static', $q); $msg = 'Pengaturan disimpan.'; }
  }
  if (($_POST['action'] ?? '') === 'password') {
    $st = db()->prepare('SELECT * FROM admins WHERE id=?'); $st->execute([$_SESSION['admin_id']]); $a = $st->fetch();
    if (!password_verify($_POST['old'] ?? '', $a['password_hash'])) $err = 'Password lama salah.';
    elseif (strlen($_POST['new'] ?? '') < 6) $err = 'Password baru minimal 6 karakter.';
    else { db()->prepare('UPDATE admins SET password_hash=? WHERE id=?')->execute([password_hash($_POST['new'], PASSWORD_DEFAULT), $a['id']]); $msg = 'Password diganti.'; }
  }
}
$s = fn($k) => e(db()->query("SELECT value FROM settings WHERE key='$k'")->fetchColumn());
$adminPage = 'settings';
require __DIR__ . '/_top.php';
?>
<h1 class="h3 fw-bold mb-3">Pengaturan</h1>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-lg-7"><form method="post" class="card-x">
    <?= csrf_field() ?><input type="hidden" name="action" value="info">
    <h2 class="h5 fw-bold mb-3">Info restoran (tampil di website)</h2>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label fw-semibold">Telepon</label><input class="form-control" name="phone" value="<?= $s('phone') ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold">WhatsApp (format 62…)</label><input class="form-control" name="whatsapp" value="<?= $s('whatsapp') ?>"></div>
      <div class="col-12"><label class="form-label fw-semibold">Alamat</label><input class="form-control" name="address" value="<?= $s('address') ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold">Jam buka</label><input class="form-control" name="hours" value="<?= $s('hours') ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold">Instagram</label><input class="form-control" name="instagram" value="<?= $s('instagram') ?>"></div>
      <div class="col-12"><label class="form-label fw-semibold">Kode QRIS statis toko</label>
        <textarea class="form-control font-monospace small" rows="4" name="qris_static" placeholder="000201010211…6304XXXX"><?= $s('qris_static') ?></textarea>
        <div class="form-text">Kosongkan = QR demo. Isi dengan teks hasil scan QRIS statis toko Anda (dari aplikasi pembaca QR). Sistem mengubahnya jadi QRIS dinamis dengan nominal sesuai total tiap pesanan.</div></div>
    </div>
    <button class="btn btn-blue mt-3">Simpan</button>
  </form></div>
  <div class="col-lg-5"><form method="post" class="card-x">
    <?= csrf_field() ?><input type="hidden" name="action" value="password">
    <h2 class="h5 fw-bold mb-3">Ganti password admin</h2>
    <label class="form-label fw-semibold">Password lama</label><input type="password" class="form-control mb-3" name="old" required autocomplete="current-password">
    <label class="form-label fw-semibold">Password baru</label><input type="password" class="form-control mb-3" name="new" required minlength="6" autocomplete="new-password">
    <button class="btn btn-blue">Ganti password</button>
  </form></div>
</div>
<?php require __DIR__ . '/_bottom.php'; ?>
