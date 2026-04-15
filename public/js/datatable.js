'use strict';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('table.p-datatable').forEach(function (table) {
        var cols = table.querySelectorAll('thead th').length;
        var tableId = table.id || null;

        // Última coluna não ordenável se tiver ações
        var lastColHasActions = table.querySelector('tbody tr:first-child td:last-child form, tbody tr:first-child td:last-child a.p-btn');
        var columnDefs = lastColHasActions ? [{ orderable: false, targets: cols - 1 }] : [];

        // data-order="COL,DIR" ou padrão 0 asc
        var orderAttr = table.getAttribute('data-order');
        var defaultOrder = orderAttr
            ? [[parseInt(orderAttr.split(',')[0]), orderAttr.split(',')[1] || 'desc']]
            : [[0, 'asc']];

        // Verifica se há busca no header do card para esta tabela
        var hasHeaderSearch = tableId && document.querySelector('[data-dt-search-for="' + tableId + '"]');

        var dt = $(table).DataTable({
            language: {
                search: '',
                searchPlaceholder: 'Pesquisar...',
                lengthMenu: '_MENU_ por página',
                info: '_START_–_END_ de _TOTAL_',
                infoEmpty: 'Nenhum registro',
                infoFiltered: '(filtrado de _MAX_)',
                zeroRecords: 'Nenhum resultado encontrado',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' }
            },
            pageLength: 15,
            lengthMenu: [15, 25, 50, 100],
            stateSave: false,
            order: defaultOrder,
            responsive: true,
            columnDefs: columnDefs,
            search: { smart: true },
            // Se tem busca no header, esconde o campo interno (só mostra length + paginação)
            dom: hasHeaderSearch
                ? '<"dt-top d-flex justify-content-between align-items-center mb-3"l>rt<"dt-bottom d-flex justify-content-between align-items-center mt-3"ip>'
                : '<"dt-top d-flex justify-content-between align-items-center mb-3"lf>rt<"dt-bottom d-flex justify-content-between align-items-center mt-3"ip>',
            initComplete: function () {
                var wrapper = $(table).closest('.dt-wrapper');
                wrapper.find('.dataTables_filter input').attr('placeholder', 'Pesquisar em todos os campos...');
                wrapper.find('.dataTables_length select').addClass('form-select-sm');
            }
        });

        if (!tableId) return;

        // Busca no header do card
        if (hasHeaderSearch) {
            var searchInput = hasHeaderSearch.querySelector('input');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    dt.search(this.value).draw();
                });
            }
        }

        // Filtros por coluna via data-dt-table + data-dt-col
        document.querySelectorAll('[data-dt-table="' + tableId + '"]').forEach(function (sel) {
            sel.addEventListener('change', function () {
                dt.column(parseInt(this.getAttribute('data-dt-col'))).search(this.value).draw();
            });
        });

        // Filtro de faixa de preço
        var precoSel = document.querySelector('[data-dt-price="' + tableId + '"]');
        if (precoSel) {
            precoSel.addEventListener('change', function () {
                var val = this.value;
                var colIdx = parseInt(this.getAttribute('data-dt-col'));
                $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function (fn) {
                    return fn._tableId !== tableId;
                });
                if (val) {
                    var fn = function (settings, data) {
                        if (settings.nTable.id !== tableId) return true;
                        var raw = data[colIdx].replace(/[^0-9,]/g, '').replace(',', '.');
                        var p = parseFloat(raw) || 0;
                        if (val === '0-50')    return p <= 50;
                        if (val === '50-100')  return p > 50  && p <= 100;
                        if (val === '100-200') return p > 100 && p <= 200;
                        if (val === '200+')    return p > 200;
                        return true;
                    };
                    fn._tableId = tableId;
                    $.fn.dataTable.ext.search.push(fn);
                }
                dt.draw();
            });
        }

        // Botão limpar
        var clearBtn = document.querySelector('[data-dt-clear="' + tableId + '"]');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                document.querySelectorAll('[data-dt-table="' + tableId + '"], [data-dt-price="' + tableId + '"]').forEach(function (el) { el.value = ''; });
                if (hasHeaderSearch) { var si = hasHeaderSearch.querySelector('input'); if (si) si.value = ''; }
                $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function (fn) { return fn._tableId !== tableId; });
                dt.search('').columns().search('').draw();
            });
        }
    });
});
