/* Keranjang belanja (JavaScript murni + localStorage) & util umum */
(function () {
  const KEY = 'wtb_cart_v1';
  const fmt = n => 'Rp' + Number(n).toLocaleString('id-ID');
  const Cart = {
    get() { try { return JSON.parse(localStorage.getItem(KEY)) || {}; } catch (e) { return {}; } },
    save(c) { localStorage.setItem(KEY, JSON.stringify(c)); document.dispatchEvent(new Event('cart:change')); },
    add(item) { const c = this.get(); c[item.id] = c[item.id] ? { ...c[item.id], qty: c[item.id].qty + 1 } : { ...item, qty: 1 }; this.save(c); },
    setQty(id, q) { const c = this.get(); if (!c[id]) return; if (q < 1) delete c[id]; else c[id].qty = Math.min(50, q); this.save(c); },
    clear() { localStorage.removeItem(KEY); document.dispatchEvent(new Event('cart:change')); },
    count() { return Object.values(this.get()).reduce((a, i) => a + i.qty, 0); },
    total() { return Object.values(this.get()).reduce((a, i) => a + i.qty * i.price, 0); },
  };
  window.Cart = Cart; window.fmtRp = fmt;

  window.toast = function (msg) {
    const el = document.getElementById('appToast'); if (!el) return;
    document.getElementById('appToastBody').textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 1800 }).show();
  };

  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  // Baris keranjang (dipakai drawer & halaman checkout)
  window.renderCartLines = function (box) {
    const items = Object.values(Cart.get());
    if (!items.length) { box.innerHTML = '<div class="empty"><i class="bi bi-bag"></i>Keranjang masih kosong.<br>Yuk pilih menu favoritmu.</div>'; return; }
    box.innerHTML = items.map(i => `
      <div class="cart-line" data-id="${i.id}">
        <img src="${esc(i.img)}" alt="" onerror="this.onerror=null;this.src=window.WTB.placeholder">
        <div class="flex-grow-1"><div class="nm">${esc(i.name)}</div><div class="pr">${fmt(i.price)}</div></div>
        <div class="text-end">
          <div class="qty"><button type="button" data-act="dec" aria-label="Kurangi">−</button><span>${i.qty}</span><button type="button" data-act="inc" aria-label="Tambah">+</button></div>
          <div class="small fw-bold mt-1">${fmt(i.price * i.qty)}</div>
        </div>
      </div>`).join('');
  };
  function bindLines(box) {
    box.addEventListener('click', e => {
      const b = e.target.closest('[data-act]'); if (!b) return;
      const id = b.closest('.cart-line').dataset.id, cur = Cart.get()[id]; if (!cur) return;
      Cart.setQty(id, cur.qty + (b.dataset.act === 'inc' ? 1 : -1));
    });
  }

  function refreshUI() {
    const n = Cart.count(), badge = document.getElementById('cartBadge');
    if (badge) { badge.textContent = n; badge.hidden = n === 0; }
    document.querySelectorAll('.js-cart-count').forEach(el => el.textContent = n);
    const items = document.getElementById('cartItems');
    if (items) { renderCartLines(items); document.getElementById('cartTotal').textContent = fmt(Cart.total());
      const co = document.getElementById('cartCheckout'); co.classList.toggle('disabled', n === 0); co.setAttribute('aria-disabled', n === 0); }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const items = document.getElementById('cartItems'); if (items) bindLines(items);
    const clr = document.getElementById('cartClear'); if (clr) clr.onclick = () => { if (Cart.count() && confirm('Kosongkan keranjang?')) Cart.clear(); };
    document.addEventListener('click', e => {
      const b = e.target.closest('.js-add'); if (!b) return;
      Cart.add({ id: b.dataset.id, name: b.dataset.name, price: +b.dataset.price, img: b.dataset.img });
      toast(b.dataset.name + ' ditambahkan');
      ['cartBadge'].forEach(id => { const el = document.getElementById(id); if (el) { el.classList.remove('bump'); void el.offsetWidth; el.classList.add('bump'); } });
    });
    document.addEventListener('cart:change', refreshUI); refreshUI();
    window.addEventListener('storage', refreshUI);
  });
  window.bindCartLines = bindLines;
})();
