<script>
    (function () {
        var form = document.getElementById('form-digesto-busca');
        var anoSelect = document.getElementById('digesto-ano');
        var ordenar = document.getElementById('digesto-ordenar');

        if (form) {
            form.querySelectorAll('.digesto-seg input[type="radio"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    form.querySelectorAll('.digesto-seg label').forEach(function (label) {
                        label.classList.toggle('active', label.querySelector('input').checked);
                    });
                });
            });
        }

        if (anoSelect) {
            anoSelect.addEventListener('change', function () {
                document.querySelectorAll('.digesto-ano-hidden').forEach(function (input) {
                    input.remove();
                });
            });
        }

        if (ordenar) {
            ordenar.addEventListener('change', function () {
                window.location = this.value;
            });
        }

        document.querySelectorAll('.js-copiar-citacao').forEach(function (botao) {
            botao.addEventListener('click', function () {
                var texto = this.getAttribute('data-citacao') || '';
                var original = this.innerHTML;
                var marcar = function (ok) {
                    botao.innerHTML = ok
                        ? '<i class="fas fa-check"></i> Copiado'
                        : original;
                    if (ok) {
                        setTimeout(function () {
                            botao.innerHTML = original;
                        }, 1800);
                    }
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(texto).then(function () {
                        marcar(true);
                    }).catch(function () {
                        marcar(false);
                    });
                    return;
                }

                var area = document.createElement('textarea');
                area.value = texto;
                document.body.appendChild(area);
                area.select();
                try {
                    document.execCommand('copy');
                    marcar(true);
                } catch (e) {
                    marcar(false);
                }
                document.body.removeChild(area);
            });
        });
    })();
</script>
