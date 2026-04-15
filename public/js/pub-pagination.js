'use strict';

/**
 * Paginação client-side para grids de cards Bootstrap.
 * Uso: <div data-card-grid data-page-size="12"> ... <div class="col-*">...</div> </div>
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-card-grid]').forEach(function (grid) {
        var pageSize = parseInt(grid.getAttribute('data-page-size') || '12');
        var items = Array.from(grid.querySelectorAll(':scope > [class*="col-"]'));
        if (items.length <= pageSize) return; // sem paginação se couber tudo

        var currentPage = 1;
        var totalPages = Math.ceil(items.length / pageSize);

        // Wrapper de controles
        var controls = document.createElement('div');
        controls.className = 'pub-pagination';
        controls.innerHTML = buildControls(pageSize);
        grid.parentNode.insertBefore(controls, grid.nextSibling);

        var selectEl = controls.querySelector('.pub-page-size');
        var paginateEl = controls.querySelector('.pub-paginate');
        var infoEl = controls.querySelector('.pub-page-info');

        function render() {
            var start = (currentPage - 1) * pageSize;
            var end = start + pageSize;
            items.forEach(function (item, i) {
                item.style.display = (i >= start && i < end) ? '' : 'none';
            });
            infoEl.textContent = (start + 1) + '–' + Math.min(end, items.length) + ' de ' + items.length;
            renderPaginate();
        }

        function renderPaginate() {
            var html = '';
            html += '<button class="pub-page-btn" data-page="prev" ' + (currentPage === 1 ? 'disabled' : '') + '>‹</button>';
            for (var p = 1; p <= totalPages; p++) {
                if (totalPages > 7 && p > 2 && p < totalPages - 1 && Math.abs(p - currentPage) > 1) {
                    if (p === 3 || p === totalPages - 2) html += '<span class="pub-page-ellipsis">…</span>';
                    continue;
                }
                html += '<button class="pub-page-btn' + (p === currentPage ? ' active' : '') + '" data-page="' + p + '">' + p + '</button>';
            }
            html += '<button class="pub-page-btn" data-page="next" ' + (currentPage === totalPages ? 'disabled' : '') + '>›</button>';
            paginateEl.innerHTML = html;

            paginateEl.querySelectorAll('.pub-page-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var p = this.getAttribute('data-page');
                    if (p === 'prev') { if (currentPage > 1) currentPage--; }
                    else if (p === 'next') { if (currentPage < totalPages) currentPage++; }
                    else { currentPage = parseInt(p); }
                    render();
                    grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });
        }

        selectEl.addEventListener('change', function () {
            pageSize = parseInt(this.value);
            totalPages = Math.ceil(items.length / pageSize);
            currentPage = 1;
            render();
        });

        render();
    });

    function buildControls(pageSize) {
        var opts = [12, 24, 48, 96].map(function (n) {
            return '<option value="' + n + '"' + (n === pageSize ? ' selected' : '') + '>' + n + ' por página</option>';
        }).join('');
        return '<div class="pub-pagination-inner">' +
            '<div class="pub-page-info-wrap"><select class="pub-page-size">' + opts + '</select><span class="pub-page-info"></span></div>' +
            '<div class="pub-paginate"></div>' +
            '</div>';
    }
});
