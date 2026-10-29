<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/qris.php';
$o = get_order_by_no($_GET['no'] ?? '');
if (!$o) { http_response_code(404); }
$pageTitle = 'Pesanan Anda'; $active = 'status';
$extraJs = [];
if ($o && $o['payment_method'] === 'qris') $extraJs[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>';
require __DIR__ . '/../includes/header.php';
?>
<main>
<section class="pt-5">
<div class="container" style="max-width:920px">
<?php if (!$o): ?>
  <div class="card-x text-center py-5"><h1 class="h3">Pesanan tidak ditemukan</h1><a class="btn btn-blue mt-3" href="<?= url('pages/menu.php') ?>">Kembali ke menu</a></div>
<?php else: ?>
  <div class="text-center mb-4">
    <span class="badge-st st-paid mb-2"><i class="bi bi-check-circle-fill me-1"></i> Pesanan diterima</span>
    <h1 class="display h2 mb-1">Terima kasih, <?= e($o['customer_name']) ?>!</h1>
    <p class="text-muted mb-0">Nomor pesanan <b><?= e($o['order_no']) ?></b> &middot; simpan untuk cek status.</p>
  </div>
  <div class="row g-4">
    <div class="col-md-7">
      <div class="card-x text-center h-100" id="payBox">
      <?php if ($o['payment_method'] === 'cashier'): ?>
        <p class="text-muted mb-1">Nomor antrean Anda</p>
        <div class="queue-no"><?= e($o['queue_no']) ?></div>
        <p class="mt-3 mb-1 fw-bold">Bayar di kasir <?= rupiah($o['total']) ?></p>
        <p class="text-muted small mb-0">Tunjukkan nomor ini ke kasir. Pesanan diproses setelah pembayaran diterima, lalu nomor Anda akan dipanggil.</p>
      <?php else: ?>
        <?php $payload = qris_payload((int)$o['total'], $o['order_no']); ?>
        <div id="qrArea">
          <p class="text-muted mb-2">Scan dengan aplikasi bank / e-wallet apa pun</p>
          <div class="qr-box"><div id="qr"></div><div class="fw-bold mt-2 fs-5"><?= rupiah($o['total']) ?></div></div>
          <p class="small text-muted mt-3 mb-1">Berlaku <span id="cd" class="fw-bold text-dark"><?= PAY_WINDOW_MINUTES ?>:00</span> lagi</p>
          <?php if (qris_is_demo()): ?><p class="small text-warning-emphasis bg-warning-subtle rounded p-2">Mode demo: QRIS toko belum diisi di Admin &rsaquo; Pengaturan, jadi QR ini contoh saja.</p><?php endif; ?>
          <?php if ($o['payment_status'] === 'pending'): ?>
            <button class="btn btn-blue mt-2" id="paidBtn">Saya sudah bayar</button>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card-x h-100">
        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Pembayaran</span><span class="badge-st st-<?= e($o['payment_status']) ?>" id="payLbl"><?= e(PAY_STATUS[$o['payment_status']]) ?></span></div>
        <div class="d-flex justify-content-between mb-3"><span class="text-muted">Pesanan</span><span class="badge-st st-<?= e($o['order_status']) ?>" id="ordLbl"><?= e(ORDER_STATUS[$o['order_status']]) ?></span></div>
        <hr>
        <?php foreach ($o['items'] as $it): ?><div class="d-flex justify-content-between small mb-1"><span><?= (int)$it['qty'] ?>&times; <?= e($it['name']) ?></span><span><?= rupiah($it['price'] * $it['qty']) ?></span></div><?php endforeach; ?>
        <div class="d-flex justify-content-between fw-bold mt-2 pt-2 border-top"><span>Total</span><span><?= rupiah($o['total']) ?></span></div>
        <a class="btn btn-ghost w-100 mt-3" href="<?= url('pages/cek-status.php?no=' . urlencode($o['order_no'])) ?>">Buka halaman cek status</a>
      </div>
    </div>
  </div>
<script>
const NO=<?= json_encode($o['order_no']) ?>;
<?php if ($o['payment_method'] === 'qris'): ?>
new QRCode(document.getElementById('qr'),{text:<?= json_encode($payload) ?>,width:240,height:240,correctLevel:QRCode.CorrectLevel.M});
let left=<?= PAY_WINDOW_MINUTES ?>*60; const cd=document.getElementById('cd');
setInterval(()=>{ if(left>0)left--; cd.textContent=String(Math.floor(left/60)).padStart(2,'0')+':'+String(left%60).padStart(2,'0'); },1000);
const pb=document.getElementById('paidBtn');
if(pb) pb.onclick=async()=>{ pb.disabled=true; const r=await fetch(window.WTB.base+'/api/confirm_payment.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({order_no:NO})}); const d=await r.json(); if(d.ok){pb.remove();poll();toast('Terkirim ke kasir, mohon tunggu verifikasi');}else{pb.disabled=false;toast(d.error);} };
<?php endif; ?>
async function poll(){ try{ const d=await (await fetch(window.WTB.base+'/api/order_status.php?no='+encodeURIComponent(NO))).json(); if(!d.ok)return; const o=d.order;
  const p=document.getElementById('payLbl'), s=document.getElementById('ordLbl'); p.textContent=o.payment_label; p.className='badge-st st-'+o.payment_status; s.textContent=o.order_label; s.className='badge-st st-'+o.order_status;
  if(o.payment_status==='paid'){const q=document.getElementById('qrArea'); if(q) q.innerHTML='<i class="bi bi-check-circle-fill text-success" style="font-size:4rem"></i><h2 class="h4 mt-2">Pembayaran diterima</h2><p class="text-muted mb-1">Nomor antrean</p><div class="queue-no">'+o.queue_no+'</div>';}
 }catch(e){} }
setInterval(poll,6000);
</script>
<?php endif; ?>
</div>
</section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
