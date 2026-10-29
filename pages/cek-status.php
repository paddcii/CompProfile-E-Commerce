<?php
$pageTitle = 'Cek Status Pesanan'; $active = 'status';
require __DIR__ . '/../includes/header.php';
?>
<main>
<div class="page-head"><div class="container"><h1 class="display mb-1">Cek Status Pesanan</h1><p class="lead-sm mb-0">Masukkan nomor pesanan (contoh: TB<?= date('ymd') ?>-001) untuk melihat pembayaran dan proses masakan.</p></div></div>
<section class="pt-4">
  <div class="container" style="max-width:820px">
    <form id="f" class="d-flex gap-2 mb-4">
      <input id="no" class="form-control form-control-lg" placeholder="Nomor pesanan" value="<?= e($_GET['no'] ?? '') ?>" required>
      <button class="btn btn-blue px-4">Cek</button>
    </form>
    <div id="res"></div>
  </div>
</section>
</main>
<script>
const steps=[['new','Diterima'],['processing','Dimasak'],['ready','Siap'],['done','Selesai']];
let timer=null;
function esc(s){return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
async function load(){
  const no=document.getElementById('no').value.trim(), box=document.getElementById('res'); if(!no)return;
  const r=await fetch(window.WTB.base+'/api/order_status.php?no='+encodeURIComponent(no)); const d=await r.json();
  if(!d.ok){box.innerHTML='<div class="alert alert-warning">'+esc(d.error)+'</div>';clearInterval(timer);return;}
  const o=d.order, idx=steps.findIndex(s=>s[0]===o.order_status);
  box.innerHTML=`<div class="card-x">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
      <div><div class="text-muted small">Nomor antrean</div><div class="queue-no" style="font-size:3.4rem">${esc(o.queue_no)}</div><div class="small text-muted">${esc(o.order_no)} &middot; ${esc(o.name)}</div></div>
      <div class="text-end"><div class="mb-2"><span class="badge-st st-${o.payment_status}">${esc(o.payment_label)}</span></div><div class="small text-muted">${esc(o.payment_method_label)}</div><div class="fw-bold fs-5">${fmtRp(o.total)}</div></div>
    </div>
    ${o.order_status==='cancelled'?'<div class="alert alert-danger">Pesanan ini dibatalkan.</div>':
      `<ul class="track mb-3">${steps.map((s,i)=>`<li class="${i<=idx?'on':''}">${s[1]}</li>`).join('')}</ul>
       <p class="text-center mb-0 fw-semibold">${o.payment_status==='paid'?esc(o.order_label):'Menunggu pembayaran diverifikasi kasir'}</p>`}
    <hr>${o.items.map(i=>`<div class="d-flex justify-content-between small mb-1"><span>${i.qty}&times; ${esc(i.name)}</span><span>${fmtRp(i.price*i.qty)}</span></div>`).join('')}
  </div>`;
  clearInterval(timer); if(!['done','cancelled'].includes(o.order_status)) timer=setInterval(load,6000);
}
document.getElementById('f').addEventListener('submit',e=>{e.preventDefault();load();});
if(document.getElementById('no').value) load();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
