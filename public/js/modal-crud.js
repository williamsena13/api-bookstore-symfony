'use strict';

document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('crudModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    const modalTitle = modalEl.querySelector('.modal-title');
    const modalBody = modalEl.querySelector('.modal-body');
    const alertContainer = document.getElementById('alert-container');

    function showAlert(message, type) {
        if (!alertContainer) return;
        alertContainer.innerHTML =
            '<div class="alert alert-' + type + ' alert-dismissible fade show">' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
    }

    const flashMessage = sessionStorage.getItem('flashMessage');
    const flashType = sessionStorage.getItem('flashType') || 'success';
    if (flashMessage) {
        showAlert(flashMessage, flashType);
        sessionStorage.removeItem('flashMessage');
        sessionStorage.removeItem('flashType');
    }

    function showModalAlert(message) {
        var existing = modalBody.querySelector('.alert');
        if (existing) existing.remove();
        var div = document.createElement('div');
        div.className = 'alert alert-danger alert-dismissible fade show';
        div.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        modalBody.prepend(div);
    }

    function bindForm(url) {
        var form = modalBody.querySelector('#modal-form');
        if (!form) return;
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            try {
                var res = await fetch(url, { method: 'POST', body: new FormData(this) });
                var contentType = res.headers.get('content-type') || '';

                if (contentType.includes('application/json')) {
                    var data = await res.json();
                    if (data.success) {
                        modal.hide();
                        sessionStorage.setItem('flashMessage', data.message);
                        location.reload();
                    } else {
                        showModalAlert(data.message);
                    }
                } else {
                    modalBody.innerHTML = await res.text();
                    bindForm(url);
                }
            } catch (err) {
                showModalAlert('Erro de conexão. Tente novamente.');
            }
        });
    }

    document.querySelectorAll('.btn-modal').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            var url = this.dataset.url;
            modalTitle.textContent = this.dataset.title;
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';
            modal.show();

            try {
                var html = await (await fetch(url)).text();
                modalBody.innerHTML = html;
                bindForm(url);
            } catch (err) {
                modalBody.innerHTML = '<div class="alert alert-danger">Erro ao carregar o formulário.</div>';
            }
        });
    });
});
