<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pdo = db(); $msg = '';

function save_upload(string $field): ?string {
  if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
  $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) || !@getimagesize($_FILES[$field]['tmp_name'])) return null;
  if ($_FILES[$field]['size'] > 4 * 1024 * 1024) return null;
  $name = 'menu-' . time() . '-' . bin2hex(random_bytes(3)) . '.' . $ext;
  return move_uploaded_file($_FILES[$field]['tmp_name'], MENU_IMG_DIR . '/' . $name) ? $name : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $act = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
  if ($act === 'save') {
    $name = trim($_POST['name'] ?? ''); $price = max(0, (int)preg_replace('/\D/', '', $_POST['price'] ?? '0'));
    $cat = trim($_POST['category'] ?? '') ?: 'Lainnya';
    if ($name === '' || $price <= 0) { $msg = 'Nama dan harga wajib diisi.'; }
    else {
      $data = [$name, $cat, trim($_POST['description'] ?? ''), $price, trim($_POST['image_url'] ?? ''), isset($_POST['is_available']) ? 1 : 0, isset($_POST['is_favorite']) ? 1 : 0];
      $up = save_upload('photo');
      if ($id) {
        $pdo->prepare('UPDATE menu SET name=?,category=?,description=?,price=?,image_url=?,is_available=?,is_favorite=? WHERE id=?')->execute([...$data, $id]);
        if ($up) $pdo->prepare('UPDATE menu SET image=? WHERE id=?')->execute([$up, $id]);
      } else {
        $so = (int)$pdo->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM menu')->fetchColumn();
        $pdo->prepare('INSERT INTO menu (name,category,description,price,image_url,is_available,is_favorite,image,sort_order) VALUES (?,?,?,?,?,?,?,?,?)')->execute([...$data, $up ?? '', $so]);
      }
      header('Location: ' . url('admin/menu.php')); exit;
    }
  }
  if ($act === 'toggle') { $pdo->prepare('UPDATE menu SET is_available = 1 - is_available WHERE id=?')->execute([$id]); header('Location: ' . url('admin/menu.php')); exit; }
  if ($act === 'delete') { $pdo->prepare('DELETE FROM menu WHERE id=?')->execute([$id]); header('Location: ' . url('admin/menu.php')); exit; }
}
$rows = $pdo->query('SELECT * FROM menu ORDER BY category, sort_order, id')->fetchAll();
$cats = array_values(array_unique(array_column($rows, 'category')));
$adminPage = 'menu';
require __DIR__ . '/_top.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h1 class="h3 fw-bold mb-0">Kelola Menu <small class="text-muted fs-6">(<?= count($rows) ?> item)</small></h1>
  <button class="btn btn-blue" id="addBtn"><i class="bi bi-plus-lg me-1"></i> Tambah menu</button>
</div>
<?php if ($msg): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
<div class="card-x p-0 overflow-hidden"><div class="table-responsive">
<table class="table align-middle mb-0">
  <thead class="table-light"><tr><th style="width:74px">Foto</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $m): ?>
    <tr>
      <td><img src="<?= e(menu_img($m)) ?>" style="width:56px;height:56px;object-fit:cover;border-radius:10px" alt="" onerror="this.onerror=null;this.src=window.WTB.placeholder"></td>
      <td><b><?= e($m['name']) ?></b><?= $m['is_favorite'] ? ' <i class="bi bi-star-fill text-warning" title="Favorit (tampil di beranda)"></i>' : '' ?><div class="small text-muted text-truncate" style="max-width:340px"><?= e($m['description']) ?></div></td>
      <td><?= e($m['category']) ?></td><td><?= rupiah($m['price']) ?></td>
      <td><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $m['id'] ?>"><button name="action" value="toggle" class="badge-st border-0 <?= $m['is_available'] ? 'st-paid' : 'st-failed' ?>"><?= $m['is_available'] ? 'Tersedia' : 'Habis' ?></button></form></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-primary js-edit" data-m='<?= e(json_encode($m, JSON_UNESCAPED_UNICODE)) ?>'><i class="bi bi-pencil"></i></button>
        <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $m['id'] ?>"><button name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus menu ini?')"><i class="bi bi-trash"></i></button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>
<p class="small text-muted mt-3"><i class="bi bi-info-circle"></i> Foto: unggah file lewat tombol edit, atau taruh manual di folder <code>assets/images/menu/</code> dengan nama file sesuai kolom foto. Jika tidak ada file lokal, dipakai link foto (URL).</p>

<div class="modal fade" id="mModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="post" enctype="multipart/form-data" class="modal-content" style="border-radius:20px">
  <div class="modal-header"><h2 class="modal-title h5 fw-bold" id="mTitle">Tambah menu</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" id="f_id" value="">
    <div class="mb-3"><label class="form-label fw-semibold">Nama menu *</label><input class="form-control" name="name" id="f_name" required maxlength="80"></div>
    <div class="row g-3 mb-3">
      <div class="col-7"><label class="form-label fw-semibold">Kategori</label><input class="form-control" name="category" id="f_cat" list="catList" maxlength="40"><datalist id="catList"><?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?></datalist></div>
      <div class="col-5"><label class="form-label fw-semibold">Harga (Rp) *</label><input class="form-control" name="price" id="f_price" inputmode="numeric" required></div>
    </div>
    <div class="mb-3"><label class="form-label fw-semibold">Deskripsi</label><textarea class="form-control" name="description" id="f_desc" rows="2" maxlength="200"></textarea></div>
    <div class="mb-3"><label class="form-label fw-semibold">Unggah foto (jpg/png/webp, maks 4 MB)</label><input type="file" class="form-control" name="photo" accept="image/jpeg,image/png,image/webp"></div>
    <div class="mb-3"><label class="form-label fw-semibold">atau link foto (URL)</label><input class="form-control" name="image_url" id="f_url" placeholder="https://…"></div>
    <div class="d-flex gap-4"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_available" id="f_av" checked><label class="form-check-label" for="f_av">Tersedia</label></div>
      <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_favorite" id="f_fav"><label class="form-check-label" for="f_fav">Tampil di beranda</label></div></div>
  </div>
  <div class="modal-footer"><button class="btn btn-ghost" type="button" data-bs-dismiss="modal">Batal</button><button class="btn btn-blue">Simpan</button></div>
</form></div></div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const modal=()=>bootstrap.Modal.getOrCreateInstance(document.getElementById('mModal')), $=id=>document.getElementById(id);
  function fill(m){ $('mTitle').textContent=m?'Ubah menu':'Tambah menu'; $('f_id').value=m?m.id:''; $('f_name').value=m?m.name:''; $('f_cat').value=m?m.category:''; $('f_price').value=m?m.price:''; $('f_desc').value=m?m.description:''; $('f_url').value=m?m.image_url:''; $('f_av').checked=m?m.is_available==1:true; $('f_fav').checked=m?m.is_favorite==1:false; modal().show(); }
  $('addBtn').onclick=()=>fill(null);
  document.querySelectorAll('.js-edit').forEach(b=>b.onclick=()=>fill(JSON.parse(b.dataset.m)));
});
</script>
<?php require __DIR__ . '/_bottom.php'; ?>
