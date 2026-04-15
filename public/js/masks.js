'use strict';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[data-mask="money"]').forEach(function (el) {
        el.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '');
            if (!v) { this.value = ''; return; }
            v = (parseInt(v) / 100).toFixed(2);
            this.value = v.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    });

    document.querySelectorAll('input[data-mask="phone"]').forEach(function (el) {
        el.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '').substring(0, 11);
            if (v.length > 10) {
                this.value = v.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (v.length > 6) {
                this.value = v.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (v.length > 2) {
                this.value = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            }
        });
    });
});
