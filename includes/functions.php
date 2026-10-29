<?php
require_once __DIR__ . '/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah($n): string { return 'Rp' . number_format((int)$n, 0, ',', '.'); }
function url(string $path = ''): string { return BASE_URL . '/' . ltrim($path, '/'); }
function json_out($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function setting(string $key, string $default = ''): string {
  static $cache = null;
  if ($cache === null) {
    $cache = [];
    foreach (db()->query('SELECT key,value FROM settings') as $r) $cache[$r['key']] = $r['value'];
  }
  return $cache[$key] ?? $default;
}
function set_setting(string $key, string $value): void {
  db()->prepare('INSERT INTO settings (key,value) VALUES (?,?) ON CONFLICT(key) DO UPDATE SET value=excluded.value')->execute([$key, $value]);
}

/** Foto menu: file lokal di assets/images/menu/ menang; kalau tidak ada, pakai link open source; terakhir placeholder. */
function menu_img(array $m): string {
  if (!empty($m['image']) && file_exists(MENU_IMG_DIR . '/' . $m['image'])) return url('assets/images/menu/' . rawurlencode($m['image']));
  if (!empty($m['image_url'])) return $m['image_url'];
  return url('assets/images/placeholder.svg');
}
function local_or(string $rel, string $fallback): string {
  return file_exists(ROOT . '/' . $rel) ? url($rel) : $fallback;
}

const ORDER_STATUS = ['new' => 'Pesanan diterima', 'processing' => 'Sedang dimasak', 'ready' => 'Siap diambil / diantar', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
const PAY_STATUS   = ['pending' => 'Menunggu pembayaran', 'waiting_verification' => 'Menunggu verifikasi kasir', 'paid' => 'Lunas', 'failed' => 'Gagal / dibatalkan'];
const PAY_METHOD   = ['qris' => 'QRIS', 'cashier' => 'Bayar di kasir'];

function get_order_by_no(string $no): ?array {
  $st = db()->prepare('SELECT * FROM orders WHERE order_no = ?'); $st->execute([strtoupper(trim($no))]);
  $o = $st->fetch();
  if (!$o) return null;
  $it = db()->prepare('SELECT name, price, qty FROM order_items WHERE order_id = ?'); $it->execute([$o['id']]);
  $o['items'] = $it->fetchAll();
  return $o;
}

/* ---- Admin auth & CSRF ---- */
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if (!is_admin()) { header('Location: ' . url('admin/login.php')); exit; } }
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check(): void {
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Token tidak valid. Muat ulang halaman.'); }
}
