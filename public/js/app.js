/* Izam Jaya Roster - interaksi halaman publik. Tanpa library. */
(function () {
    'use strict';

    // Menu mobile
    var toggle = document.querySelector('[data-nav-toggle]');
    var menu = document.querySelector('[data-nav-menu]');
    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            var open = menu.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Tombol +/- jumlah
    document.querySelectorAll('[data-qty]').forEach(function (box) {
        var input = box.querySelector('input[type=number]');
        var step = function (d) {
            var max = parseInt(input.max, 10) || 9999;
            var next = Math.min(max, Math.max(1, (parseInt(input.value, 10) || 1) + d));
            input.value = next;
            if (input.hasAttribute('data-autosubmit')) box.submit();
        };
        box.querySelector('[data-qty-minus]').addEventListener('click', function () { step(-1); });
        box.querySelector('[data-qty-plus]').addEventListener('click', function () { step(1); });
        if (input.hasAttribute('data-autosubmit')) {
            input.addEventListener('change', function () { box.submit(); });
        }
    });

    // Harga di halaman produk mengikuti varian yang dipilih
    var priceBox = document.querySelector('[data-price]');
    var fmt = function (n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); };
    document.querySelectorAll('.variants input[type=radio]').forEach(function (r) {
        var show = function () {
            if (!r.checked) return;
            if (priceBox) priceBox.textContent = fmt(r.dataset.price);
            // jumlah tidak boleh melebihi stok varian yang dipilih
            var q = document.querySelector('.buy input[name=qty]');
            if (q && r.dataset.stock) {
                q.max = r.dataset.stock;
                if (parseInt(q.value, 10) > parseInt(r.dataset.stock, 10)) q.value = r.dataset.stock;
            }
        };
        r.addEventListener('change', show);
        show();
    });

    // Galeri foto produk
    var main = document.querySelector('[data-gallery-main]');
    document.querySelectorAll('[data-gallery-thumb]').forEach(function (btn) {
        btn.addEventListener('click', function () { if (main) main.src = btn.dataset.galleryThumb; });
    });

    // Form request roster khusus -> buka WhatsApp
    var req = document.querySelector('[data-request-form]');
    if (req) {
        req.addEventListener('submit', function (e) {
            e.preventDefault();
            var text = 'Halo Izam Jaya Roster, saya mau request roster khusus.\n\n' +
                'Nama: ' + req.nama.value.trim() + '\n' +
                'Permintaan: ' + req.detail.value.trim() + '\n\n' +
                'Mohon info apakah bisa dibuat dan estimasi harganya.';
            window.open('https://wa.me/' + req.dataset.wa + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
        });
    }

    // Halaman "pesanan tersimpan": buka WhatsApp otomatis
    var auto = document.querySelector('[data-auto-redirect]');
    if (auto) {
        setTimeout(function () { window.location.href = auto.href; }, 2500);
    }
})();
