<?php
/** Kartu menu. $m = baris tabel menu; $hl = kartu disorot (biru penuh). */
function menu_card(array $m, bool $hl = false): void {
  $img = menu_img($m); ?>
  <article class="dish <?= $hl ? 'dish-hl' : '' ?> <?= $m['is_available'] ? '' : 'dish-off' ?>" data-cat="<?= e($m['category']) ?>" data-name="<?= e(strtolower($m['name'])) ?>">
    <div class="dish-photo"><img src="<?= e($img) ?>" alt="<?= e($m['name']) ?>" loading="lazy" onerror="this.onerror=null;this.src=window.WTB.placeholder"></div>
    <div class="dish-body">
      <div class="dish-cat"><?= e($m['category']) ?></div>
      <h3 class="dish-name"><?= e($m['name']) ?></h3>
      <p class="dish-desc"><?= e($m['description']) ?></p>
      <div class="dish-foot">
        <span class="dish-price"><?= rupiah($m['price']) ?></span>
        <?php if ($m['is_available']): ?>
          <button class="btn-add js-add" type="button" aria-label="Tambah <?= e($m['name']) ?> ke keranjang"
            data-id="<?= $m['id'] ?>" data-name="<?= e($m['name']) ?>" data-price="<?= $m['price'] ?>" data-img="<?= e($img) ?>"><i class="bi bi-plus-lg"></i></button>
        <?php else: ?><span class="badge text-bg-secondary">Habis</span><?php endif; ?>
      </div>
    </div>
  </article>
<?php }
