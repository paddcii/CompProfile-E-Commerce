<?php
/**
 * QRIS DINAMIS OTOMATIS
 * - Jika "QRIS statis" toko diisi di Admin > Pengaturan, kode itu diubah menjadi QRIS dinamis
 *   (nominal = total pesanan) lalu CRC16 dihitung ulang. Hasilnya bisa dipindai aplikasi bayar.
 * - Jika kosong, dibuat QR DEMO (bentuk sama, tapi bukan rekening asli) supaya alur bisa dicoba.
 */
function crc16_ccitt(string $s): string {
  $crc = 0xFFFF;
  for ($i = 0, $n = strlen($s); $i < $n; $i++) {
    $crc ^= ord($s[$i]) << 8;
    for ($j = 0; $j < 8; $j++) { $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1); $crc &= 0xFFFF; }
  }
  return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
}
function tlv(string $tag, string $val): string { return $tag . str_pad((string)strlen($val), 2, '0', STR_PAD_LEFT) . $val; }

function qris_is_demo(): bool { return trim(setting('qris_static')) === ''; }

function qris_payload(int $amount, string $orderNo): string {
  $static = trim(setting('qris_static'));
  if ($static !== '') {
    $s = preg_replace('/6304[0-9A-Fa-f]{4}$/', '', $static);
    $s = str_replace('010211', '010212', $s);
    $s = preg_replace('/54\d{2}\d+(?=5802ID)/', '', $s);          // buang nominal lama jika ada
    $parts = explode('5802ID', $s, 2);
    if (count($parts) === 2) $s = $parts[0] . tlv('54', (string)$amount) . '5802ID' . $parts[1];
    $s .= '6304';
    return $s . crc16_ccitt($s);
  }
  $mai = tlv('00', 'ID.WARUNGTENDABIRU.DEMO') . tlv('01', 'DEMO0000000001') . tlv('02', '000000000000') . tlv('03', 'UMI');
  $p = tlv('00', '01') . tlv('01', '12') . tlv('26', $mai) . tlv('52', '5812') . tlv('53', '360')
     . tlv('54', (string)$amount) . tlv('58', 'ID') . tlv('59', 'WARUNG NASI TENDA BIRU') . tlv('60', 'TANGERANG')
     . tlv('62', tlv('01', $orderNo)) . '6304';
  return $p . crc16_ccitt($p);
}
