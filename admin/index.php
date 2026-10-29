<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pdo = db();

// ---- aksi ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $id = (int)($_POST['id'] ?? 0); $now = date('Y-m-d H:i:s'); $act = $_POST['action'] ?? '';
  if ($act === 'paid')   $pdo->prepare("UPDATE orders SET payment_status='paid', order_status=CASE WHEN order_status='new' THEN 'processing' ELSE order_status END, updated_at=? WHERE id=?")->execute([$now, $id]);
  if ($act === 'unpaid') $pdo->prepare("UPDATE orders SET payment_status='failed', updated_at=? WHERE id=?")->execute([$now, $id]);
  if ($act === 'status' && isset(ORDER_STATUS[$_POST['status'] ?? ''])) $pdo->prepare('UPDATE orders SET order_status=?, updated_at=? WHERE id=?')->execute([$_POST['status'], $now, $id]);
  if ($act === 'delete') $pdo->prepare('DELETE FROM orders WHERE id=?')->execute([$id]);
  header('Location: ' . url('admin/index.php') . '?f=' . urlencode($_GET['f'] ?? 'aktif')); exit;
}

$f = $_GET['f'] ?? 'aktif';
$where = ['aktif' => "order_status IN ('new','processing','ready') AND payment_status!='failed'", 'selesai' => "order_status IN ('done','cancelled') OR payment_status='failed'", 'semua' => '1=1'][$f] ?? '1=1';
$orders = $pdo->query("SELECT * FROM orders WHERE $where ORDER BY id DESC LIMIT 100")->fetchAll();
$itemQ = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?');
$today = date('Y-m-d');
$sum = $pdo->query("SELECT COUNT(*) c, COALESCE(SUM(CASE WHEN payment_status='paid' THEN total END),0) s FROM orders WHERE substr(created_at,1,10)='$today'")->fetch();
$adminPage = 'orders';
require __DIR__ . '/_top.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
  <h1 class="h3 fw-bold mb-0">Pesanan</h1>
  <div class="pills">
    <?php foreach (['aktif' => 'Aktif', 'selesai' => 'Selesai', 'semua' => 'Semua'] as $k => $l): ?><a class="pill <?= $f===$k?'on':'' ?>" href="?f=<?= $k ?>"><?= $l ?></a><?php endforeach; ?>
  </div>
</div>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="stat"><b><?= $sum['c'] ?></b><span>pesanan hari ini</span></div></div>
  <div class="col-6 col-md-3"><div class="stat"><b><?= rupiah($sum['s']) ?></b><span>lunas hari ini</span></div></div>
</div>
<div id="ordersWrap">
<?php if (!$orders): ?><div class="empty"><i class="bi bi-inbox"></i>Belum ada pesanan di daftar ini.</div><?php endif; ?>
<?php foreach ($orders as $o): $itemQ->execute([$o['id']]); $items = $itemQ->fetchAll(); ?>
  <div class="ord <?= ($o['order_status']==='new' || $o['payment_status']==='waiting_verification') ? 'is-new' : '' ?>">
    <div class="row g-3">
      <div class="col-lg-3">
        <div class="qn"><?= e($o['queue_no']) ?></div>
        <div class="small text-muted"><?= e($o['order_no']) ?> &middot; <?= date('H:i', strtotime($o['created_at'])) ?></div>
        <div class="fw-bold mt-2"><?= e($o['customer_name']) ?></div>
        <?php if ($o['phone']): ?><a class="small" href="https://wa.me/<?= e(preg_replace('/^0/', '62', $o['phone'])) ?>" target="_blank" rel="noopener"><?= e($o['phone']) ?></a><?php endif; ?>
        <div class="small text-muted"><?= $o['order_type']==='takeaway' ? 'Bawa pulang' : 'Makan di tempat' . ($o['table_no'] ? ' · Meja ' . e($o['table_no']) : '') ?></div>
      </div>
      <div class="col-lg-5">
        <?php foreach ($items as $it): ?><div class="d-flex justify-content-between small"><span><?= (int)$it['qty'] ?>&times; <?= e($it['name']) ?></span><span><?= rupiah($it['price'] * $it['qty']) ?></span></div><?php endforeach; ?>
        <?php if ($o['notes']): ?><div class="small mt-2 p-2 rounded" style="background:#fff8e6"><i class="bi bi-chat-left-text me-1"></i><?= e($o['notes']) ?></div><?php endif; ?>
        <div class="d-flex justify-content-between fw-bold border-top mt-2 pt-2"><span>Total</span><span><?= rupiah($o['total']) ?></span></div>
      </div>
      <div class="col-lg-4">
        <div class="mb-2"><span class="badge-st st-<?= e($o['payment_status']) ?>"><?= e(PAY_METHOD[$o['payment_method']]) ?>: <?= e(PAY_STATUS[$o['payment_status']]) ?></span> <span class="badge-st st-<?= e($o['order_status']) ?>"><?= e(ORDER_STATUS[$o['order_status']]) ?></span></div>
        <div class="d-flex flex-wrap gap-2">
          <?php if (in_array($o['payment_status'], ['pending','waiting_verification'])): ?>
            <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>"><button name="action" value="paid" class="btn btn-sm btn-success"><i class="bi bi-check2-circle"></i> Tandai lunas</button></form>
            <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>"><button name="action" value="unpaid" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak / batalkan pembayaran?')">Tolak</button></form>
          <?php endif; ?>
          <form method="post" class="d-flex gap-1"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>"><input type="hidden" name="action" value="status">
            <select name="status" class="form-select form-select-sm" style="width:auto"><?php foreach (ORDER_STATUS as $k => $l): ?><option value="<?= $k ?>" <?= $o['order_status']===$k?'selected':'' ?>><?= e($l) ?></option><?php endforeach; ?></select>
            <button class="btn btn-sm btn-blue px-3" style="padding:.25rem .8rem">Ubah</button></form>
          <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>"><button name="action" value="delete" class="btn btn-sm btn-link text-danger" onclick="return confirm('Hapus pesanan ini permanen?')" aria-label="Hapus"><i class="bi bi-trash"></i></button></form>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php $adminReloadOrders = true; require __DIR__ . '/_bottom.php'; ?>
