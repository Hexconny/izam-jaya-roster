/* Izam Jaya Roster - interaksi halaman admin. */
(function () {
    'use strict';

    // Menambah/menghapus baris dinamis. Template memakai penanda __INDEX__.
    function dynamicRows(opts) {
        var list = document.querySelector(opts.list);
        var tpl = document.querySelector(opts.template);
        var addBtn = document.querySelector(opts.add);
        if (!list || !tpl || !addBtn) return;

        var next = list.querySelectorAll(opts.row).length;

        addBtn.addEventListener('click', function () {
            list.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__INDEX__/g, next++));
        });

        list.addEventListener('click', function (e) {
            var btn = e.target.closest(opts.remove);
            if (!btn) return;
            if (list.querySelectorAll(opts.row).length <= 1) {
                alert('Minimal harus ada satu baris.');
                return;
            }
            btn.closest(opts.row).remove();
        });
    }

    dynamicRows({
        list: '[data-variants]', template: '[data-variant-template]',
        add: '[data-add-variant]', row: '[data-variant-row]', remove: '[data-remove-variant]'
    });

    dynamicRows({
        list: '[data-items]', template: '[data-item-template]',
        add: '[data-add-item]', row: '[data-item-row]', remove: '[data-remove-item]'
    });
})();
