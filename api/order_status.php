<?php
require_once __DIR__ . '/../includes/functions.php';
$o = get_order_by_no($_GET['no'] ?? '');
if (!$o) json_out(['ok' => false, 'error' => 'Nomor pesanan tidak ditemukan.'], 404);
json_out(['ok' => true, 'order' => [
  'order_no' => $o['order_no'], 'queue_no' => $o['queue_no'], 'name' => $o['customer_name'], 'total' => (int)$o['total'],
  'payment_method' => $o['payment_method'], 'payment_method_label' => PAY_METHOD[$o['payment_method']],
  'payment_status' => $o['payment_status'], 'payment_label' => PAY_STATUS[$o['payment_status']],
  'order_status' => $o['order_status'], 'order_label' => ORDER_STATUS[$o['order_status']],
  'created_at' => $o['created_at'], 'items' => $o['items'],
]]);
