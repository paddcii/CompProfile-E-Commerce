<?php
$pageTitle = 'Menu & Pesan Online'; $active = 'menu';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/menu_card.php';
$rows = db()->query('SELECT * FROM menu ORDER BY sort_order, id')->fetchAll();
$cats = array_values(array_unique(array_column($rows, 'category')));
?>
<main>
<div class="page-head">
  <div class="container">
    <h1 class="display mb-2">Menu Kami</h1>
    <p class="lead-sm mb-4">Pilih yang kamu suka, tambahkan ke keranjang, lalu bayar lewat QRIS atau di kasir.</p>
    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
      <div class="pills" id="pills" role="tablist">
        <button class="pill on" type="button" data-cat="">Semua</button>
        <?php foreach ($cats as $c): ?><button class="pill" type="button" data-cat="<?= e($c) ?>"><?= e($c) ?></button><?php endforeach; ?>
      </div>
      <input type="search" id="q" class="form-control search" style="max-width:280px" placeholder="Cari menu…" aria-label="Cari menu">
    </div>
  </div>
</div>
<section class="pt-4">
  <div class="container">
    <div class="dish-grid" id="grid"><?php foreach ($rows as $m) menu_card($m); ?></div>
    <p id="none" class="text-center text-muted py-5" hidden>Menu tidak ditemukan.</p>
  </div>
</section>
</main>
<div class="floating-cart"><button class="btn btn-cart btn-lg shadow" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer"><i class="bi bi-bag-heart-fill"></i> <span class="js-cart-count">0</span> item</button></div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  let cat='',term='';
  const cards=[...document.querySelectorAll('#grid .dish')], none=document.getElementById('none');
  function apply(){ let n=0; cards.forEach(c=>{const ok=(!cat||c.dataset.cat===cat)&&(!term||c.dataset.name.includes(term)); c.style.display=ok?'':'none'; if(ok)n++;}); none.hidden=n>0; }
  document.getElementById('pills').addEventListener('click',e=>{const b=e.target.closest('.pill'); if(!b)return; document.querySelectorAll('.pill').forEach(p=>p.classList.remove('on')); b.classList.add('on'); cat=b.dataset.cat; apply();});
  document.getElementById('q').addEventListener('input',e=>{term=e.target.value.trim().toLowerCase(); apply();});
});
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
