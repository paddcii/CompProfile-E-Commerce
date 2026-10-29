<?php
$pageTitle = 'Checkout'; $active = 'menu';
require __DIR__ . '/../includes/header.php';
?>
<main>
<div class="page-head"><div class="container"><h1 class="display mb-1">Selesaikan Pesanan</h1><p class="lead-sm mb-0">Tinggal isi data dan pilih cara bayar.</p></div></div>
<section class="pt-4">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-7">
        <form id="coForm" class="card-x" novalidate>
          <h2 class="h5 fw-bold mb-3">Data pemesan</h2>
          <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label fw-semibold" for="name">Nama *</label><input id="name" class="form-control" required maxlength="60" autocomplete="name"></div>
            <div class="col-md-6"><label class="form-label fw-semibold" for="phone">No. WhatsApp</label><input id="phone" class="form-control" inputmode="tel" maxlength="20" placeholder="08xxxxxxxxxx" autocomplete="tel"></div>
          </div>
          <h2 class="h5 fw-bold mb-3">Jenis pesanan</h2>
          <div class="row g-3 mb-3">
            <div class="col-sm-6"><label class="choice"><input type="radio" name="type" value="dine_in" checked><span class="box"><i class="bi bi-shop"></i><span><b>Makan di tempat</b><small>Diantar ke meja</small></span></span></label></div>
            <div class="col-sm-6"><label class="choice"><input type="radio" name="type" value="takeaway"><span class="box"><i class="bi bi-bag-check"></i><span><b>Bawa pulang</b><small>Dibungkus</small></span></span></label></div>
          </div>
          <div class="mb-4" id="tableWrap"><label class="form-label fw-semibold" for="table">Nomor meja (jika sudah duduk)</label><input id="table" class="form-control" maxlength="10" placeholder="Contoh: 5"></div>
          <div class="mb-4"><label class="form-label fw-semibold" for="notes">Catatan</label><textarea id="notes" class="form-control" rows="2" maxlength="300" placeholder="Misal: sambal dipisah, tidak pedas"></textarea></div>
          <h2 class="h5 fw-bold mb-3">Metode pembayaran</h2>
          <div class="row g-3 mb-4">
            <div class="col-sm-6"><label class="choice"><input type="radio" name="pay" value="qris" checked><span class="box"><i class="bi bi-qr-code-scan"></i><span><b>QRIS</b><small>QR dibuat otomatis sesuai total</small></span></span></label></div>
            <div class="col-sm-6"><label class="choice"><input type="radio" name="pay" value="cashier"><span class="box"><i class="bi bi-cash-coin"></i><span><b>Bayar di kasir</b><small>Dapat nomor antrean, tunggu dipanggil</small></span></span></label></div>
          </div>
          <div id="formErr" class="alert alert-danger py-2" hidden></div>
          <button class="btn btn-blue btn-lg w-100" id="submitBtn" type="submit">Buat Pesanan</button>
        </form>
      </div>
      <div class="col-lg-5">
        <div class="card-x position-sticky" style="top:100px">
          <h2 class="h5 fw-bold mb-2">Ringkasan</h2>
          <div id="sumLines"></div>
          <div class="d-flex justify-content-between align-items-center pt-3"><span class="text-muted">Total</span><strong class="fs-3" id="sumTotal">Rp0</strong></div>
          <a href="<?= url('pages/menu.php') ?>" class="btn btn-link px-0">&lsaquo; Tambah menu lagi</a>
        </div>
      </div>
    </div>
  </div>
</section>
</main>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const lines=document.getElementById('sumLines'); bindCartLines(lines);
  function draw(){ renderCartLines(lines); document.getElementById('sumTotal').textContent=fmtRp(Cart.total()); document.getElementById('submitBtn').disabled=Cart.count()===0; }
  document.addEventListener('cart:change',draw); draw();
  const tw=document.getElementById('tableWrap');
  document.querySelectorAll('input[name=type]').forEach(r=>r.addEventListener('change',()=>{ tw.hidden=document.querySelector('input[name=type]:checked').value==='takeaway'; }));
  document.getElementById('coForm').addEventListener('submit',async e=>{
    e.preventDefault(); const err=document.getElementById('formErr'), btn=document.getElementById('submitBtn'); err.hidden=true;
    const name=document.getElementById('name').value.trim(); if(!name){err.textContent='Nama wajib diisi.';err.hidden=false;return;}
    btn.disabled=true; btn.textContent='Memproses…';
    try{
      const res=await fetch(window.WTB.base+'/api/place_order.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({
        name, phone:document.getElementById('phone').value, order_type:document.querySelector('input[name=type]:checked').value,
        table_no:document.getElementById('table').value, notes:document.getElementById('notes').value,
        payment:document.querySelector('input[name=pay]:checked').value,
        items:Object.values(Cart.get()).map(i=>({id:+i.id,qty:i.qty}))})});
      const d=await res.json(); if(!d.ok) throw new Error(d.error||'Gagal membuat pesanan');
      Cart.clear(); location.href=window.WTB.base+'/pages/pesanan.php?no='+encodeURIComponent(d.order_no);
    }catch(x){err.textContent=x.message;err.hidden=false;btn.disabled=false;btn.textContent='Buat Pesanan';}
  });
});
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
