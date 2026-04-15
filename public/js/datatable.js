'use strict';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('table.p-datatable').forEach(function (table) {
        var cols = table.querySelectorAll('thead th').length;
        var sortableCols = [];
        for (var i = 0; i < cols - 1; i++) { sortableCols.push(i); }

        $(table).DataTable({
            language: {
                search: '',
                searchPlaceholder: 'Pesquisar...',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Exibindo _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Nenhum registro encontrado',
                infoFiltered: '(filtrado de _MAX_ registros)',
                zeroRecords: 'Nenhum registro encontrado',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' }
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [[0, 'asc']],
            responsive: true,
            columnDefs: [
                { orderable: false, targets: cols - 1 },
                { orderable: true, targets: sortableCols }
            ],
            dom: '<"dt-top flex justify-content-between align-items-center mb-3"lf>rt<"dt-bottom flex justify-content-between align-items-center mt-3"ip>',
            initComplete: function () {
                var wrapper = $(table).closest('.dt-wrapper');
                wrapper.find('.dataTables_filter input').attr('placeholder', 'Pesquisar...');
            }
        });
    });
});
