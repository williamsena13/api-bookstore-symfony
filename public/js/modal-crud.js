'use strict';

document.addEventListener('DOMContentLoaded', function () {

    // ===== MODAL CRUD (criar/editar) =====
    var crudModalEl = document.getElementById('crudModal');
    if (crudModalEl) {
        var crudModal = new bootstrap.Modal(crudModalEl);
        var modalTitle = crudModalEl.querySelector('.p-modal-title');
        var modalBody = crudModalEl.querySelector('.modal-body');
        var alertContainer = document.getElementById('alert-container');

        function showPageAlert(message, type) {
            if (!alertContainer) return;
            var icon = type === 'success' ? 'pi-check-circle' : (type === 'warning' ? 'pi-exclamation-triangle' : 'pi-times-circle');
            alertContainer.innerHTML =
                '<div class="p-alert p-alert-' + type + '">' +
                    '<i class="pi ' + icon + '"></i>' + message +
                '</div>';
            setTimeout(function () { alertContainer.innerHTML = ''; }, 5000);
        }

        var flashMessage = sessionStorage.getItem('flashMessage');
        var flashType = sessionStorage.getItem('flashType') || 'success';
        if (flashMessage) {
            showPageAlert(flashMessage, flashType);
            sessionStorage.removeItem('flashMessage');
            sessionStorage.removeItem('flashType');
        }

        function showModalAlert(message) {
            var existing = modalBody.querySelector('.p-alert');
            if (existing) existing.remove();
            var div = document.createElement('div');
            div.className = 'p-alert p-alert-danger';
            div.innerHTML = '<i class="pi pi-times-circle"></i>' + message;
            modalBody.prepend(div);
        }

        function bindForm(url) {
            var form = modalBody.querySelector('#modal-form');
            if (!form) return;
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                var btn = form.querySelector('[type="submit"]');
                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="pi pi-spin pi-spinner"></i> Salvando...'; }
                try {
                    var res = await fetch(url, { method: 'POST', body: new FormData(this) });
                    var contentType = res.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        var data = await res.json();
                        if (data.success) {
                            crudModal.hide();
                            sessionStorage.setItem('flashMessage', data.message);
                            sessionStorage.setItem('flashType', 'success');
                            location.reload();
                        } else {
                            showModalAlert(data.message);
                            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="pi pi-check"></i> Salvar'; }
                        }
                    } else {
                        modalBody.innerHTML = await res.text();
                        bindForm(url);
                    }
                } catch (err) {
                    showModalAlert('Erro de conexão. Tente novamente.');
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="pi pi-check"></i> Salvar'; }
                }
            });
        }

        document.querySelectorAll('.btn-modal').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                var url = this.dataset.url;
                if (modalTitle) modalTitle.textContent = this.dataset.title || '';
                modalBody.innerHTML = '<div class="text-center" style="padding:2rem;"><i class="pi pi-spin pi-spinner" style="font-size:1.5rem;color:var(--primary);"></i></div>';
                crudModal.show();
                try {
                    var html = await (await fetch(url)).text();
                    modalBody.innerHTML = html;
                    bindForm(url);
                    if (typeof initTomSelects === 'function') initTomSelects(modalBody);
                } catch (err) {
                    modalBody.innerHTML = '<div class="p-alert p-alert-danger"><i class="pi pi-times-circle"></i> Erro ao carregar o formulário.</div>';
                }
            });
        });
    }

    // ===== MODAL DE CONFIRMAÇÃO DE EXCLUSÃO =====
    var confirmModalEl = document.getElementById('confirmDeleteModal');
    if (confirmModalEl) {
        var confirmModal = new bootstrap.Modal(confirmModalEl);
        var confirmForm = null;

        document.querySelectorAll('.btn-delete').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                confirmForm = this.closest('form');
                var label = this.dataset.label || 'este registro';
                var msg = confirmModalEl.querySelector('#confirmDeleteMessage');
                if (msg) msg.textContent = 'Tem certeza que deseja excluir ' + label + '? Esta ação não pode ser desfeita.';
                confirmModal.show();
            });
        });

        var confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                if (confirmForm) {
                    confirmModal.hide();
                    confirmForm.submit();
                }
            });
        }
    }

});
