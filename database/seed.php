<?php
/** Data awal: 20 menu, pengaturan, dan akun admin (admin / admin123). Dijalankan otomatis sekali saat database dibuat. */
function fl($kw, $lock) { return "https://loremflickr.com/640/480/$kw?lock=$lock"; }

function seed_database(PDO $pdo): void {
  $menu = [
    // [nama, kategori, deskripsi, harga, kata kunci foto, favorit]
    ['Nasi Goreng Tenda Biru','Paket Nasi','Nasi goreng bumbu rempah, telur mata sapi, acar, dan kerupuk.',20000,'nasi,goreng',1],
    ['Nasi Uduk Komplit','Paket Nasi','Nasi uduk gurih dengan ayam goreng, orek tempe, telur, dan sambal.',22000,'nasi,uduk',1],
    ['Soto Ayam Kampung','Paket Nasi','Kuah kuning bening, suwiran ayam, soun, telur rebus, dan perasan jeruk nipis.',18000,'soto,ayam',1],
    ['Nasi Campur Warung','Paket Nasi','Nasi dengan tiga lauk pilihan dapur hari ini plus sayur dan sambal.',25000,'nasi,campur',0],
    ['Ayam Goreng Kremes','Lauk Utama','Ayam goreng bumbu kuning dengan kremesan renyah.',18000,'fried,chicken',1],
    ['Ayam Bakar Madu','Lauk Utama','Diungkep bumbu manis-gurih, dibakar sampai wangi karamelnya keluar.',20000,'grilled,chicken',1],
    ['Ayam Penyet Sambal Terasi','Lauk Utama','Ayam goreng dipenyet dengan sambal terasi yang pedasnya jujur.',19000,'chicken,rice',0],
    ['Bebek Goreng Sambal Hijau','Lauk Utama','Bebek empuk berbumbu, digoreng garing, ditemani sambal hijau.',32000,'roast,duck',1],
    ['Lele Goreng Crispy','Lauk Utama','Lele segar digoreng garing, disajikan dengan lalapan dan sambal.',16000,'catfish,fried',0],
    ['Ikan Nila Bakar','Lauk Utama','Nila utuh dibakar dengan olesan bumbu kecap dan jeruk nipis.',28000,'grilled,fish',0],
    ['Rendang Sapi','Lauk Utama','Daging sapi dimasak pelan hingga bumbu meresap dan legit.',25000,'rendang,beef',1],
    ['Empal Gepuk','Lauk Utama','Daging sapi empuk berbumbu manis, digeprek sebelum digoreng.',24000,'beef,fried',0],
    ['Telur Balado','Sayur & Pendamping','Telur rebus goreng dilumuri sambal balado merah.',8000,'egg,sambal',0],
    ['Tempe Tahu Goreng','Sayur & Pendamping','Sepasang gorengan hangat, teman setia nasi putih.',6000,'tempeh,tofu',0],
    ['Capcay Kuah','Sayur & Pendamping','Tumis aneka sayur berkuah bening, ringan dan segar.',15000,'stir,fry,vegetables',0],
    ['Sayur Lodeh','Sayur & Pendamping','Sayur santan dengan labu siam, terong, dan tempe.',10000,'vegetable,soup',0],
    ['Es Teh Manis','Minuman','Teh seduh dingin, manisnya pas.',5000,'iced,tea',1],
    ['Es Jeruk Peras','Minuman','Jeruk peras asli dengan es batu.',8000,'orange,juice',0],
    ['Es Kelapa Muda','Minuman','Air dan daging kelapa muda, dingin dan menyegarkan.',12000,'coconut,drink',0],
    ['Kopi Tubruk','Minuman','Kopi tubruk pekat, ampasnya dibiarkan mengendap.',6000,'coffee,cup',0],
  ];
  $st = $pdo->prepare('INSERT INTO menu (name,category,description,price,image,image_url,is_favorite,sort_order) VALUES (?,?,?,?,?,?,?,?)');
  foreach ($menu as $i => $m) {
    $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($m[0])), '-') . '.jpg';   // taruh foto sendiri dengan nama ini di assets/images/menu/
    $st->execute([$m[0], $m[1], $m[2], $m[3], $slug, fl($m[4], $i + 11), $m[5], $i + 1]);
  }
  $defaults = [
    'phone' => '0823-1856-4499', 'whatsapp' => '628231854499',
    'address' => 'Jl. Perikanan Raya No. 2, Depok, Jawa Barat',
    'hours' => 'Setiap hari, 07.00 – 17.00 WIB',
    'instagram' => '@warungnasitendabiru',
    'qris_static' => '',   // isi dengan kode QRIS statis toko Anda di Admin > Pengaturan
  ];
  $s = $pdo->prepare('INSERT OR IGNORE INTO settings (key,value) VALUES (?,?)');
  foreach ($defaults as $k => $v) $s->execute([$k, $v]);
  $pdo->prepare('INSERT OR IGNORE INTO admins (username,password_hash) VALUES (?,?)')
      ->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
}
