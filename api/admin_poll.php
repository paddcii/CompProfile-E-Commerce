<?php
// Dipanggil admin.js tiap 5 detik. Mengembalikan notifikasi baru sejak ID terakhir.
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) json_out(['ok' => false], 401);
$after = (int)($_GET['after'] ?? 0);
$st = db()->prepare('SELECT id,type,order_id,message,created_at FROM notifications WHERE id > ? ORDER BY id ASC LIMIT 20');
$st->execute([$after]);
$latest = (int)db()->query('SELECT COALESCE(MAX(id),0) FROM notifications')->fetchColumn();
$active = (int)db()->query("SELECT COUNT(*) FROM orders WHERE order_status IN ('new','processing') OR payment_status='waiting_verification'")->fetchColumn();
json_out(['ok' => true, 'latest' => $latest, 'active' => $active, 'items' => $st->fetchAll()]);
