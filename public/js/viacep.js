'use strict';

document.addEventListener('DOMContentLoaded', function () {
    var cepInput = document.querySelector('[data-viacep="cep"]');
    if (!cepInput) return;

    var btnBuscar = document.getElementById('btn-buscar-cep');
    var feedback = document.getElementById('cep-feedback');

    var campos = {
        logradouro: document.querySelector('[data-viacep="logradouro"]'),
        complemento: document.querySelector('[data-viacep="complemento"]'),
        bairro: document.querySelector('[data-viacep="bairro"]'),
        localidade: document.querySelector('[data-viacep="localidade"]'),
        uf: document.querySelector('[data-viacep="uf"]'),
    };

    function limparCampos() {
        Object.values(campos).forEach(function (el) { if (el) el.value = ''; });
    }

    function formatarCep(valor) {
        return valor.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
    }

    cepInput.addEventListener('input', function () {
        this.value = formatarCep(this.value);
    });

    async function buscarCep() {
        var cep = cepInput.value.replace(/\D/g, '');
        feedback.textContent = '';
        feedback.style.display = 'none';
        cepInput.classList.remove('is-invalid');

        if (cep.length !== 8) {
            cepInput.classList.add('is-invalid');
            feedback.textContent = 'CEP deve ter 8 dígitos.';
            feedback.style.display = 'block';
            return;
        }

        btnBuscar.disabled = true;
        btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            var res = await fetch('https://viacep.com.br/ws/' + cep + '/json/');
            var data = await res.json();

            if (data.erro) {
                cepInput.classList.add('is-invalid');
                feedback.textContent = 'CEP não encontrado.';
                feedback.style.display = 'block';
                limparCampos();
                return;
            }

            Object.entries(campos).forEach(function (entry) {
                if (entry[1] && data[entry[0]]) entry[1].value = data[entry[0]];
            });

            var numInput = document.querySelector('[name$="[numero]"]');
            if (numInput) numInput.focus();
        } catch (e) {
            cepInput.classList.add('is-invalid');
            feedback.textContent = 'Erro ao consultar o CEP. Verifique sua conexão.';
            feedback.style.display = 'block';
        } finally {
            btnBuscar.disabled = false;
            btnBuscar.innerHTML = '<i class="bi bi-search"></i>';
        }
    }

    if (btnBuscar) btnBuscar.addEventListener('click', buscarCep);

    cepInput.addEventListener('blur', function () {
        if (this.value.replace(/\D/g, '').length === 8) buscarCep();
    });
});
