<?php
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Metode tidak valid'], 405);

$in = json_decode(file_get_contents('php://input'), true) ?: [];
$name  = trim($in['name'] ?? '');
$phone = preg_replace('/[^0-9+]/', '', $in['phone'] ?? '');
$type  = ($in['order_type'] ?? '') === 'takeaway' ? 'takeaway' : 'dine_in';
$table = mb_substr(trim($in['table_no'] ?? ''), 0, 10);
$notes = mb_substr(trim($in['notes'] ?? ''), 0, 300);
$pay   = ($in['payment'] ?? '') === 'cashier' ? 'cashier' : 'qris';
$items = is_array($in['items'] ?? null) ? $in['items'] : [];

if ($name === '' || mb_strlen($name) > 60) json_out(['ok' => false, 'error' => 'Nama wajib diisi (maks. 60 karakter).'], 422);
if (!$items) json_out(['ok' => false, 'error' => 'Keranjang masih kosong.'], 422);

$pdo = db();
$pdo->beginTransaction();
try {
  // Harga SELALU diambil dari database, bukan dari browser.
  $lines = []; $total = 0;
  $q = $pdo->prepare('SELECT id,name,price FROM menu WHERE id=? AND is_available=1');
  foreach ($items as $it) {
    $qty = max(1, min(50, (int)($it['qty'] ?? 1)));
    $q->execute([(int)($it['id'] ?? 0)]);
    $m = $q->fetch();
    if (!$m || $qty < 1) continue;
    $lines[] = [$m, $qty]; $total += $m['price'] * $qty;
  }
  if (!$lines) throw new RuntimeException('Menu di keranjang sudah tidak tersedia.');

  $today = date('Y-m-d');
  $n = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE substr(created_at,1,10)='$today'")->fetchColumn() + 1;
  $orderNo = 'TB' . date('ymd') . '-' . str_pad((string)$n, 3, '0', STR_PAD_LEFT);
  $queueNo = 'A' . str_pad((string)$n, 3, '0', STR_PAD_LEFT);
  $now = date('Y-m-d H:i:s');

  $pdo->prepare('INSERT INTO orders (order_no,queue_no,customer_name,phone,order_type,table_no,notes,total,payment_method,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
      ->execute([$orderNo, $queueNo, $name, $phone, $type, $table, $notes, $total, $pay, $now, $now]);
  $oid = (int)$pdo->lastInsertId();
  $ins = $pdo->prepare('INSERT INTO order_items (order_id,menu_id,name,price,qty) VALUES (?,?,?,?,?)');
  foreach ($lines as [$m, $qty]) $ins->execute([$oid, $m['id'], $m['name'], $m['price'], $qty]);

  $msg = "Pesanan baru $queueNo dari $name — " . rupiah($total) . ' (' . PAY_METHOD[$pay] . ')';
  $pdo->prepare('INSERT INTO notifications (type,order_id,message,created_at) VALUES (?,?,?,?)')->execute(['new_order', $oid, $msg, $now]);
  $pdo->commit();
  json_out(['ok' => true, 'order_no' => $orderNo]);
} catch (Throwable $ex) {
  $pdo->rollBack();
  json_out(['ok' => false, 'error' => $ex->getMessage()], 422);
}
