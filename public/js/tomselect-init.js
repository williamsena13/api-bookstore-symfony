'use strict';

function initTomSelects(scope) {
    var root = scope || document;

    root.querySelectorAll('select[multiple]:not(.ts-initialized)').forEach(function (el) {
        new TomSelect(el, {
            plugins: ['remove_button', 'checkbox_options'],
            placeholder: el.dataset.placeholder || 'Selecione...',
            searchField: ['text'],
            maxOptions: 200,
            render: {
                option: function (data, escape) {
                    return '<div class="ts-option">' + escape(data.text) + '</div>';
                },
                item: function (data, escape) {
                    return '<div class="ts-item">' + escape(data.text) + '</div>';
                },
                no_results: function () {
                    return '<div class="ts-no-results">Nenhum resultado encontrado</div>';
                }
            },
            onInitialize: function () {
                el.classList.add('ts-initialized');
            }
        });
    });

    root.querySelectorAll('select:not([multiple]):not(.ts-initialized):not([data-no-ts])').forEach(function (el) {
        new TomSelect(el, {
            allowEmptyOption: true,
            placeholder: el.dataset.placeholder || 'Selecione...',
            searchField: ['text'],
            maxOptions: 200,
            render: {
                no_results: function () {
                    return '<div class="ts-no-results">Nenhum resultado encontrado</div>';
                }
            },
            onInitialize: function () {
                el.classList.add('ts-initialized');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initTomSelects();
});

window.initTomSelects = initTomSelects;
