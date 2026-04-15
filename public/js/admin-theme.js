'use strict';

function applyDark(dark) {
    var r = document.documentElement;
    var icon = document.getElementById('darkModeIcon');
    if (dark) {
        r.setAttribute('data-dark', '1');
        if (icon) icon.className = 'pi pi-sun';
        localStorage.setItem('bk-dark', '1');
    } else {
        r.removeAttribute('data-dark');
        if (icon) icon.className = 'pi pi-moon';
        localStorage.setItem('bk-dark', '0');
    }
}

document.addEventListener('DOMContentLoaded', function () {

    // Restaurar dark mode
    applyDark(localStorage.getItem('bk-dark') === '1');

    // Dark mode button
    var darkBtn = document.getElementById('darkModeBtn');
    if (darkBtn) {
        darkBtn.addEventListener('click', function () {
            applyDark(document.documentElement.getAttribute('data-dark') !== '1');
        });
    }

    // Theme picker toggle (topbar)
    var pickerBtn  = document.getElementById('themePickerBtn');
    var pickerMenu = document.getElementById('themePickerMenu');
    if (pickerBtn && pickerMenu) {
        pickerBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            pickerMenu.classList.toggle('open');
        });
        document.addEventListener('click', function () {
            pickerMenu.classList.remove('open');
        });
        pickerMenu.addEventListener('click', function (e) { e.stopPropagation(); });
    }
});
