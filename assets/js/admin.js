/* Notifikasi real-time admin: polling tiap 5 detik + suara + toast + Notification API */
(function () {
  let last = window.WTB.lastNotif, audioOn = localStorage.getItem('wtb_sound') === '1', ctx = null;
  const btn = document.getElementById('soundBtn');
  function paint() { if (btn) btn.innerHTML = audioOn ? '<i class="bi bi-bell-fill"></i> Suara aktif' : '<i class="bi bi-bell-slash"></i> Aktifkan suara'; }
  function beep() {
    if (!audioOn) return;
    try { ctx = ctx || new (window.AudioContext || window.webkitAudioContext)();
      [880, 1175].forEach((f, i) => { const o = ctx.createOscillator(), g = ctx.createGain(); o.frequency.value = f; o.type = 'sine'; o.connect(g); g.connect(ctx.destination);
        const t = ctx.currentTime + i * .18; g.gain.setValueAtTime(.0001, t); g.gain.exponentialRampToValueAtTime(.3, t + .02); g.gain.exponentialRampToValueAtTime(.0001, t + .16); o.start(t); o.stop(t + .17); });
    } catch (e) {}
  }
  if (btn) { paint(); btn.onclick = () => { audioOn = !audioOn; localStorage.setItem('wtb_sound', audioOn ? '1' : '0'); paint();
    if (audioOn) { beep(); if ('Notification' in window && Notification.permission === 'default') Notification.requestPermission(); } }; }

  function toastMsg(n) {
    const box = document.getElementById('notifBox'), el = document.createElement('div');
    el.className = 'toast align-items-center border-0 ' + (n.type === 'new_order' ? 'text-bg-primary' : 'text-bg-warning'); el.setAttribute('role', 'alert');
    el.innerHTML = '<div class="d-flex"><div class="toast-body"><b>' + (n.type === 'new_order' ? 'Pesanan baru' : 'Konfirmasi bayar') + '</b><br></div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    el.querySelector('.toast-body').append(n.message); box.append(el);
    const t = new bootstrap.Toast(el, { delay: 9000 }); t.show(); el.addEventListener('hidden.bs.toast', () => el.remove());
    if ('Notification' in window && Notification.permission === 'granted' && document.hidden) new Notification(n.type === 'new_order' ? 'Pesanan baru' : 'Konfirmasi bayar', { body: n.message });
  }
  async function reloadOrders() {
    const w = document.getElementById('ordersWrap'); if (!w) return;
    if (document.activeElement && w.contains(document.activeElement)) return;   // jangan ganggu saat admin sedang memilih status
    const html = await (await fetch(location.href)).text();
    const nw = new DOMParser().parseFromString(html, 'text/html').getElementById('ordersWrap'); if (nw) w.innerHTML = nw.innerHTML;
  }
  async function poll() {
    try {
      const d = await (await fetch(window.WTB.base + '/api/admin_poll.php?after=' + last, { cache: 'no-store' })).json();
      if (!d.ok) return;
      const b = document.getElementById('activeBadge'); if (b) { b.textContent = d.active; b.hidden = !d.active; }
      document.title = (d.active ? '(' + d.active + ') ' : '') + 'Admin — Warung Nasi Tenda Biru';
      if (d.items.length) { d.items.forEach(toastMsg); beep(); reloadOrders(); }
      last = Math.max(last, d.latest);
    } catch (e) {}
  }
  poll(); setInterval(poll, 5000);
})();
