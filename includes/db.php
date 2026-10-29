<?php
require_once __DIR__ . '/config.php';
require_once ROOT . '/database/seed.php';

function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;
  $fresh = !file_exists(DB_FILE);
  $pdo = new PDO('sqlite:' . DB_FILE);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $pdo->exec('PRAGMA foreign_keys = ON');
  if ($fresh) {
    $pdo->exec(file_get_contents(ROOT . '/database/schema.sql'));
    seed_database($pdo);
  }
  return $pdo;
}
