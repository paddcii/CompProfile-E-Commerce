<?php
// Pelanggan menekan "Saya sudah bayar" -> status jadi menunggu verifikasi + notifikasi ke admin.
require_once __DIR__ . '/../includes/functions.php';
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$o = get_order_by_no($in['order_no'] ?? '');
if (!$o || $o['payment_method'] !== 'qris' || $o['payment_status'] !== 'pending') json_out(['ok' => false, 'error' => 'Pesanan tidak bisa dikonfirmasi.'], 422);
$now = date('Y-m-d H:i:s');
db()->prepare("UPDATE orders SET payment_status='waiting_verification', updated_at=? WHERE id=?")->execute([$now, $o['id']]);
db()->prepare('INSERT INTO notifications (type,order_id,message,created_at) VALUES (?,?,?,?)')
    ->execute(['payment_confirm', $o['id'], "Pelanggan {$o['customer_name']} mengaku sudah bayar QRIS {$o['queue_no']} (" . rupiah($o['total']) . ') — mohon cek mutasi.', $now]);
json_out(['ok' => true]);
