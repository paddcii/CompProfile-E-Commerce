<?php
/**
 * KONFIGURASI UTAMA — Warung Nasi Tenda Biru
 * Semua path dihitung otomatis, jadi folder ini bisa ditaruh di htdocs/XAMPP/hosting mana pun.
 */
date_default_timezone_set('Asia/Jakarta');

define('ROOT', dirname(__DIR__));
$__doc  = rtrim(str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
$__root = str_replace('\\', '/', ROOT);
define('BASE_URL', rtrim(substr($__root, strlen($__doc)), '/'));

define('SITE_NAME', 'Warung Nasi Tenda Biru');
define('FOUNDED_YEAR', 1998);
define('DB_FILE', ROOT . '/database/warung.sqlite');
define('MENU_IMG_DIR', ROOT . '/assets/images/menu');
define('PAY_WINDOW_MINUTES', 15);   // batas waktu bayar QRIS (tampilan hitung mundur)
